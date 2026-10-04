/**
 * Bascule de la navigation sur petit écran.
 *
 * Le menu est visible sans JavaScript tant que la requête média ne s'applique
 * pas ; le script ne fait qu'ouvrir et fermer le panneau replié.
 */
( function () {
	'use strict';

	var bascule = document.querySelector( '[data-bascule-menu]' );
	var nav = document.getElementById( 'menu-principal' );

	if ( ! bascule || ! nav ) {
		return;
	}

	function fermer() {
		nav.classList.remove( 'est-ouvert' );
		bascule.setAttribute( 'aria-expanded', 'false' );
	}

	bascule.addEventListener( 'click', function () {
		var ouvert = nav.classList.toggle( 'est-ouvert' );
		bascule.setAttribute( 'aria-expanded', ouvert ? 'true' : 'false' );
	} );

	document.addEventListener( 'keydown', function ( evenement ) {
		if ( 'Escape' === evenement.key && nav.classList.contains( 'est-ouvert' ) ) {
			fermer();
			bascule.focus();
		}
	} );

	// Referme le panneau quand la mise en page repasse en large.
	if ( window.matchMedia ) {
		var large = window.matchMedia( '(min-width: 781px)' );
		var surChangement = function ( evenement ) {
			if ( evenement.matches ) {
				fermer();
			}
		};

		if ( large.addEventListener ) {
			large.addEventListener( 'change', surChangement );
		} else if ( large.addListener ) {
			large.addListener( surChangement );
		}
	}
}() );

/**
 * Carrousel vidéo de l'accueil.
 *
 * Sans script, la piste défile au doigt ou au clavier et chaque vignette mène à
 * YouTube. Le script affiche les flèches et remplace la vignette cliquée par le
 * lecteur youtube-nocookie : aucune requête vers le lecteur avant ce clic.
 */
( function () {
	'use strict';

	var carrousel = document.querySelector( '[data-carrousel]' );

	if ( ! carrousel ) {
		return;
	}

	var piste = carrousel.querySelector( '[data-carrousel-piste]' );
	var commandes = carrousel.querySelector( '[data-carrousel-commandes]' );
	var precedent = carrousel.querySelector( '[data-carrousel-precedent]' );
	var suivant = carrousel.querySelector( '[data-carrousel-suivant]' );
	var reduit = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function majFleches() {
		var max = piste.scrollWidth - piste.clientWidth - 1;
		precedent.disabled = piste.scrollLeft <= 0;
		suivant.disabled = piste.scrollLeft >= max;
		commandes.hidden = max <= 0;
	}

	function defiler( sens ) {
		piste.scrollBy( { left: sens * piste.clientWidth * 0.8, behavior: reduit ? 'auto' : 'smooth' } );
	}

	precedent.addEventListener( 'click', function () {
		defiler( -1 );
	} );
	suivant.addEventListener( 'click', function () {
		defiler( 1 );
	} );
	piste.addEventListener( 'scroll', majFleches, { passive: true } );
	window.addEventListener( 'resize', majFleches );
	majFleches();

	carrousel.addEventListener( 'click', function ( evenement ) {
		var lanceur = evenement.target.closest( '[data-video-id]' );

		if ( ! lanceur ) {
			return;
		}

		evenement.preventDefault();

		var cadre = document.createElement( 'iframe' );
		cadre.className = 'videos__lecteur';
		cadre.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( lanceur.getAttribute( 'data-video-id' ) ) + '?autoplay=1&rel=0&playsinline=1';
		cadre.title = lanceur.getAttribute( 'data-video-titre' ) || '';
		cadre.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
		cadre.referrerPolicy = 'strict-origin-when-cross-origin';

		lanceur.replaceWith( cadre );
		cadre.focus();
	} );
}() );
