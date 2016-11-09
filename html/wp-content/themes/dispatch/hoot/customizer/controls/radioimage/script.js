jQuery(document).ready(function($) {
	"use strict";


	/*** Radioimage Control ***/

	$('.customize-control-radioimage').each(function(){

		var $radios = $(this).find('input'),
			$labels = $(this).find('.hoot-customize-radioimage');

		$radios.on('change',function(){
			$labels.removeClass('radiocheck');
			$(this).parent('.hoot-customize-radioimage').addClass('radiocheck');
		});

	});


});