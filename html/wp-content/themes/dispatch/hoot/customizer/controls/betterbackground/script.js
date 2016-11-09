jQuery(document).ready(function($) {
	"use strict";


	/*** Betterbackground Control ***/

	$('.hoot-customize-control-betterbackgroundstart').each(function( index ){

		var $blocks = $(this).nextUntil( '.hoot-customize-control-betterbackgroundend', "li" ),
			$bbButtons = $blocks.filter('.hoot-customize-control-betterbackgroundbutton'),
			$buttons = $bbButtons.find('.hoot-betterbackground-button'),
			$typeInput = $bbButtons.find('input.hoot-customizer-control-betterbackground'),
			$customs = $blocks.filter('.customize-control-color, .customize-control-image, .customize-control-select'),
			$predefineds = $blocks.filter('.customize-control-color, .hoot-customize-control-groupstart'),
			showBlocks = function(control){
				if ( control == 'predefined' ) {
					$customs.hide();
					$predefineds.show();
				}
				if ( control == 'custom' ) {
					$predefineds.hide();
					$customs.show();
				}
			};

		$blocks.addClass('hoot-customize-control-background-blocks');//.attr('data-controlbackground', id);

		// If we have both custom image and pattern options
		if ( $bbButtons.length ) {

			$blocks.hide();
			$blocks.filter('.hoot-customize-control-betterbackgroundbutton').show();

			showBlocks( $typeInput.val() );

			$buttons.on('click',function(){
				var value = $(this).data('value');

				$buttons.removeClass('selected').addClass('deactive');
				$(this).removeClass('deactive').addClass('selected');

				$typeInput.val(value).trigger('change');

				showBlocks(value);
			});

		}

		/* Patterns */

		var $pattPreview = $blocks.find('.hoot-betterbackground-button-pattern'),
			$patterns = $blocks.find('.hoot-customize-radioimage');

		if ( $pattPreview.length ) {
			$pattPreview.html('').append( $patterns.filter('.radiocheck').children('img').clone() );

			$patterns.on('click',function(){
				$pattPreview.html('').append( $(this).children('img').clone() );
			});
		}

	});


});