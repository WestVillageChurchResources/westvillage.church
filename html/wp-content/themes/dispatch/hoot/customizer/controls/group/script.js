jQuery(document).ready(function($) {
	"use strict";


	/*** Prepare Groups ***/

	$( ".hoot-customize-control-groupstart" ).each( function( index ) {
		var id = $(this).attr('id'),
			moveBlocks = $(this).nextUntil( '.hoot-customize-control-groupend', "li" );
		moveBlocks.addClass('hoot-customize-control-group-blocks').attr('data-controlgroup', id);
	});


	/*** Fly Groups ***/

	var $body = $('body');

	$body.on( "openflypanel", function() {
		var $flypanelbutton = $body.data('flypanelbutton');
		if( $flypanelbutton && $flypanelbutton.data('flypaneltype')=='group' && $flypanelbutton.data('flypanel')=='open' ) {
			var $groupstart = $flypanelbutton.parent('.hoot-customize-control-groupstart');
			$groupstart.addClass('flygroup-open');
			var moveBlocks = $groupstart.nextUntil( '.hoot-customize-control-groupend', "li" );
			$('#hoot-flygroup-content').html('').append(moveBlocks).wrapInner('<ul></ul>');
			$body.addClass('hoot-displaying-flygroup');
			$body.data('flypaneltype','group');
		}
	});

	$body.on( "closeflypanel", function() {
		$body.removeClass('hoot-displaying-flygroup');
		if($body.data('flypaneltype')=='group') {
			$('#hoot-flygroup-content > ul > li').each( function() {
				var controlGroup = $(this).data('controlgroup');
				$(this).insertBefore('#'+controlGroup+'-end');
				$('#'+controlGroup).removeClass('flygroup-open');
			});
			$body.data('flypaneltype','');
		}
	});


});
