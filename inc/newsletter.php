<?php
/**
 * Accessibilité du formulaire de newsletter.
 *
 * @package Refoulement
 */

defined( 'ABSPATH' ) || exit;

/**
 * Restaure les étiquettes supprimées du formulaire natif Brevo.
 *
 * Le plugin conserve le texte des étiquettes mais retire les éléments `label`
 * lors de son passage dans KSES. Cette transformation locale reconstruit leur
 * association sans modifier le plugin ni les balises autorisées globalement.
 *
 * @param string $html Fragment HTML rendu par le shortcode Brevo.
 * @return string Fragment accessible, ou fragment original si rien ne change.
 */
function refoulement_accessibiliser_formulaire_brevo( $html ) {
	$html = (string) $html;

	if ( '' === $html || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}

	$dom        = new DOMDocument( '1.0', 'UTF-8' );
	$wrapper_id = 'refoulement-brevo-fragment-' . hash( 'sha256', $html );
	$options    = 0;

	if ( defined( 'LIBXML_HTML_NOIMPLIED' ) ) {
		$options |= LIBXML_HTML_NOIMPLIED;
	}

	if ( defined( 'LIBXML_HTML_NODEFDTD' ) ) {
		$options |= LIBXML_HTML_NODEFDTD;
	}

	$ancien_etat_libxml = libxml_use_internal_errors( true );

	try {
		$charge = $dom->loadHTML(
			'<?xml encoding="UTF-8"><div id="' . $wrapper_id . '">' . $html . '</div>',
			$options
		);
	} catch ( Throwable $erreur ) {
		$charge = false;
	} finally {
		libxml_clear_errors();
		libxml_use_internal_errors( $ancien_etat_libxml );
	}

	if ( ! $charge ) {
		return $html;
	}

	$wrapper = $dom->getElementById( $wrapper_id );

	if ( ! $wrapper instanceof DOMElement ) {
		return $html;
	}

	$normaliser_identifiant = static function ( $valeur, $repli ) {
		$valeur = (string) $valeur;

		if ( function_exists( 'iconv' ) ) {
			$ascii = iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', $valeur );

			if ( false !== $ascii ) {
				$valeur = $ascii;
			}
		}

		$valeur = strtolower( $valeur );
		$valeur = trim( (string) preg_replace( '/[^a-z0-9]+/', '-', $valeur ), '-' );

		return '' !== $valeur ? $valeur : $repli;
	};

	$identifiants = array();

	foreach ( $wrapper->getElementsByTagName( '*' ) as $element ) {
		if ( $element instanceof DOMElement && $element->hasAttribute( 'id' ) ) {
			$identifiant = $element->getAttribute( 'id' );

			if ( '' !== $identifiant ) {
				$identifiants[ $identifiant ] = isset( $identifiants[ $identifiant ] ) ? $identifiants[ $identifiant ] + 1 : 1;
			}
		}
	}

	$element_fournit_nom = static function ( $element ) use ( $dom ) {
		if ( ! $element instanceof DOMElement ) {
			return false;
		}

		if ( '' !== trim( $element->textContent ) || '' !== trim( $element->getAttribute( 'aria-label' ) ) || '' !== trim( $element->getAttribute( 'title' ) ) ) {
			return true;
		}

		$references = preg_split( '/\s+/', trim( $element->getAttribute( 'aria-labelledby' ) ), -1, PREG_SPLIT_NO_EMPTY );

		foreach ( $references as $reference ) {
			$cible = $dom->getElementById( $reference );

			if ( ! $cible instanceof DOMElement ) {
				return true;
			}

			if ( '' !== trim( $cible->textContent ) || '' !== trim( $cible->getAttribute( 'aria-label' ) ) || '' !== trim( $cible->getAttribute( 'title' ) ) ) {
				return true;
			}
		}

		return false;
	};

	$champs = array();

	foreach ( array( 'input', 'select', 'textarea' ) as $balise ) {
		foreach ( $wrapper->getElementsByTagName( $balise ) as $champ ) {
			if ( $champ instanceof DOMElement ) {
				$champs[] = $champ;
			}
		}
	}

	$modifie = false;

	foreach ( $champs as $champ ) {
		$type = 'input' === strtolower( $champ->tagName ) ? strtolower( $champ->getAttribute( 'type' ) ?: 'text' ) : '';

		if ( in_array( $type, array( 'hidden', 'submit', 'button', 'reset', 'image', 'checkbox', 'radio' ), true ) ) {
			continue;
		}

		if ( '' !== trim( $champ->getAttribute( 'aria-label' ) ) ) {
			continue;
		}

		$aria_attribut = trim( $champ->getAttribute( 'aria-labelledby' ) );
		$aria_designe  = preg_split( '/\s+/', $aria_attribut, -1, PREG_SPLIT_NO_EMPTY );
		$aria_valide   = false;
		$aria_interne  = true;

		foreach ( $aria_designe as $identifiant_aria ) {
			$element_aria = $dom->getElementById( $identifiant_aria );

			if ( ! $element_aria instanceof DOMElement ) {
				$aria_interne = false;
				break;
			}

			if ( $element_fournit_nom( $element_aria ) ) {
				$aria_valide = true;
				break;
			}
		}

		if ( ! $aria_interne || $aria_valide ) {
			continue;
		}

		$ancetre = $champ->parentNode;
		$etiquete_existante = false;

		while ( $ancetre instanceof DOMElement && $ancetre !== $wrapper ) {
			if ( 'label' === strtolower( $ancetre->tagName ) && $element_fournit_nom( $ancetre ) ) {
				$etiquete_existante = true;
				break;
			}

			$ancetre = $ancetre->parentNode;
		}

		$ancien_id = $champ->getAttribute( 'id' );

		if ( ! $etiquete_existante && '' !== $ancien_id && 1 === ( $identifiants[ $ancien_id ] ?? 0 ) ) {
			foreach ( $wrapper->getElementsByTagName( 'label' ) as $etiquette ) {
				if ( $etiquette instanceof DOMElement && $ancien_id === $etiquette->getAttribute( 'for' ) && $element_fournit_nom( $etiquette ) ) {
					$etiquete_existante = true;
					break;
				}
			}
		}

		if ( $etiquete_existante || ! $champ->parentNode instanceof DOMNode ) {
			continue;
		}

		$noeud_texte = null;

		for ( $noeud = $champ->previousSibling; $noeud; $noeud = $noeud->previousSibling ) {
			if ( XML_TEXT_NODE === $noeud->nodeType ) {
				if ( '' !== trim( $noeud->nodeValue ) ) {
					$noeud_texte = $noeud;
					break;
				}

				continue;
			}

			if ( XML_ELEMENT_NODE === $noeud->nodeType ) {
				if ( in_array( strtolower( $noeud->nodeName ), array( 'input', 'select', 'textarea' ), true ) ) {
					break;
				}

				continue;
			}

			break;
		}

		if ( ! $noeud_texte instanceof DOMText ) {
			continue;
		}

		preg_match( '/^(\s*)(.*?)(\s*)$/su', $noeud_texte->nodeValue, $parties );
		$texte_etiquette = isset( $parties[2] ) ? trim( $parties[2] ) : '';

		if ( '' === $texte_etiquette ) {
			continue;
		}

		$formulaire = $champ->parentNode;

		while ( $formulaire instanceof DOMElement && 'form' !== strtolower( $formulaire->tagName ) && $formulaire !== $wrapper ) {
			$formulaire = $formulaire->parentNode;
		}

		$id_formulaire = $formulaire instanceof DOMElement && 'form' === strtolower( $formulaire->tagName ) ? $formulaire->getAttribute( 'id' ) : '';

		if ( preg_match( '/(?:^|[_-])(\d+)$/', $id_formulaire, $numero_formulaire ) ) {
			$id_formulaire = $numero_formulaire[1];
		} else {
			$id_formulaire = $normaliser_identifiant( $id_formulaire, 'formulaire' );
		}

		$nom_champ    = $normaliser_identifiant( $champ->getAttribute( 'name' ), strtolower( $champ->tagName ) );
		$id_base      = 'refoulement-brevo-' . $id_formulaire . '-' . $nom_champ;
		$nouvel_id    = $id_base;
		$numero       = 2;

		while ( isset( $identifiants[ $nouvel_id ] ) && ( $ancien_id !== $nouvel_id || 1 !== $identifiants[ $nouvel_id ] ) ) {
			$nouvel_id = $id_base . '-' . $numero;
			++$numero;
		}

		if ( '' !== $ancien_id && isset( $identifiants[ $ancien_id ] ) ) {
			--$identifiants[ $ancien_id ];

			if ( 0 === $identifiants[ $ancien_id ] ) {
				unset( $identifiants[ $ancien_id ] );
			}
		}

		$identifiants[ $nouvel_id ] = isset( $identifiants[ $nouvel_id ] ) ? $identifiants[ $nouvel_id ] + 1 : 1;
		$champ->setAttribute( 'id', $nouvel_id );

		if ( '' !== $aria_attribut ) {
			$champ->removeAttribute( 'aria-labelledby' );
		}

		$etiquette = $dom->createElement( 'label' );
		$etiquette->setAttribute( 'for', $nouvel_id );
		$etiquette->appendChild( $dom->createTextNode( $texte_etiquette ) );

		$parent = $champ->parentNode;

		if ( isset( $parties[1] ) && '' !== $parties[1] ) {
			$noeud_texte->nodeValue = $parties[1];
		} else {
			$parent->removeChild( $noeud_texte );
		}

		$parent->insertBefore( $etiquette, $champ );

		if ( isset( $parties[3] ) && '' !== $parties[3] ) {
			$parent->insertBefore( $dom->createTextNode( $parties[3] ), $champ );
		}

		$modifie = true;
	}

	if ( ! $modifie ) {
		return $html;
	}

	$sortie = '';

	foreach ( iterator_to_array( $wrapper->childNodes ) as $enfant ) {
		$fragment = $dom->saveHTML( $enfant );

		if ( false === $fragment ) {
			return $html;
		}

		$sortie .= $fragment;
	}

	return $sortie;
}
