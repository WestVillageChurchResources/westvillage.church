jQuery(document).ready(function($) {
	"use strict";

	if ( (typeof _hoot_customizer_data != 'undefined') && (typeof _hoot_customizer_data.iconslist != 'undefined') ) {


		/*** Fly Icon ***/

		var $body = $('body'),
			$flyicon = $('#hoot-flyicon-content');

		$body.on( "openflypanel", function() {
			var $flypanelbutton = $body.data('flypanelbutton');
			if( $flypanelbutton && $flypanelbutton.data('flypaneltype')=='icon' && $flypanelbutton.data('flypanel')=='open' ) {

				$flyicon.html( _hoot_customizer_data.iconslist ).data('controlgroup', $flypanelbutton);

				var $flyiconIcons = $flyicon.find('i'),
					$input = $flypanelbutton.siblings('input.hoot-customizer-control-icon'),
					selected = $input.val(),
					$icondisplay = $flypanelbutton.children('i');

				$flypanelbutton.addClass('flygroup-open');

				if(selected)
					$flyicon.find('i.'+selected).addClass('selected');

				$flyiconIcons.click( function(event){
					var iconvalue = $(this).data('value');
					$flyiconIcons.removeClass('selected');
					$(this).addClass('selected');
					$input.val( iconvalue ).trigger('change');
					$icondisplay.removeClass().addClass('fa ' + iconvalue );
					$('.hoot-flypanel-back').trigger('click');
				});

				$body.addClass('hoot-displaying-flyicon');
				$body.data('flypaneltype','icon');
			}
		});

		$body.on( "closeflypanel", function() {
			$body.removeClass('hoot-displaying-flyicon');
			var controlGroup = $flyicon.data('controlgroup');
			if (controlGroup)
				$(controlGroup).removeClass('flygroup-open');
			if($body.data('flypaneltype')=='icon') {
				$body.data('flypaneltype','');
			}
		});

		$('.hoot-customizer-control-icon-remove').click( function(event){
			var input = $(this).siblings('input.hoot-customizer-control-icon'),
				icondisplay = $(this).siblings('.hoot-customizer-control-icon-picked').children('i');
			input.val('').trigger('change');
			icondisplay.removeClass();
			// $('.hoot-flypanel-back').trigger('click'); // redundant
		});


	}

});
