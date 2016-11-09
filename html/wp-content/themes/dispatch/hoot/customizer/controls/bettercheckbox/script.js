jQuery(document).ready(function($) {
	"use strict";


	/*** Multi Check Boxes ***/
	$('.customize-control-bettercheckbox .bettercheckbox-multi').each(function(){

		var $control = $(this),
			$multi = $control.find('input[type="checkbox"]'),
			$input = $control.find('input[type="hidden"]');

		$multi.on('change', function(){
			var multiValues = $multi.filter(':checked').map(function(){
				return this.value;
			}).get().join(',');
			$input.val(multiValues).trigger('change');
		});

	});


});