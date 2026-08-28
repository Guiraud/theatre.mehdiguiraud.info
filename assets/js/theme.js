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
