jQuery(document).ready(function($) {
	"use strict";

	if ( (typeof _hoot_customizer_data != 'undefined') && (typeof _hoot_customizer_data['premium-section'] != 'undefined') ) {


		/*** Add Premium Section to main panel ***/

		var premium_section = _hoot_customizer_data['premium-section'];
		$(premium_section).appendTo( "#customize-theme-controls" );


		/*** Premium Fly Panel ***/

		var $body = $('body');

		$body.on( "openflypanel", function() {
			var $flypanelbutton = $body.data('flypanelbutton');
			if( $flypanelbutton && $flypanelbutton.data('flypaneltype')=='premium' && $flypanelbutton.data('flypanel')=='open' ) {
				$body.addClass('hoot-displaying-flypremium');
				$body.data('flypaneltype','premium');
			}
		});

		$body.on( "closeflypanel", function() {
			$body.removeClass('hoot-displaying-flypremium');
			if($body.data('flypaneltype')=='premium') {
				$body.data('flypaneltype','');
			}
		});


	}

});
