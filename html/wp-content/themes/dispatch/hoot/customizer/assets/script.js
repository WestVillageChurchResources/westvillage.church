jQuery(document).ready(function($) {
	"use strict";


	/*** Older Customizer Styles ***/

	if ( (typeof _hoot_customizer_data != 'undefined') ) {
		if ( (typeof _hoot_customizer_data.bcomp != 'undefined') && ( _hoot_customizer_data.bcomp == 'yes' ) )
			$('body').addClass('hoot-bcomp');
		if ( (typeof _hoot_customizer_data.scomp != 'undefined') && ( _hoot_customizer_data.scomp == 'yes' ) )
			$('body').addClass('hoot-scomp');
	}


	/*** Info Buttons ***/

	$('#customize-info').html('').append($('#hoot-info-buttons')).show();
	// $('#customize-controls .customize-info .accordion-section-title, #customize-controls .customize-section-title h3', 'body:not(.hoot-bcomp)').addClass('hoot-titlearea-info-buttons').html('').append( $('#hoot-info-buttons').html() );
	$('#customize-controls .customize-info .accordion-section-title', 'body:not(.hoot-bcomp)').addClass('hoot-titlearea-info-buttons').html('').append( $('#hoot-info-buttons').html() );
	$('.hoot-info-buttons a').click(function(e){
		var ahref = $(this).attr('href'),
			win = window.open(ahref, '_blank');
		if(win){
			win.focus();
			e.preventDefault();
		}
	})


	/*** Section Icons ***/

	$('#customize-theme-controls li.control-section:not(.control-panel)').children('h3').prepend('<i class="dashicons dashicons-arrow-right-alt2 control-section-accordian-icon"></i>');


	/*** Panel Icons ***/

	if ( (typeof _hoot_customizer_data != 'undefined') && (typeof _hoot_customizer_data.panelicons != 'undefined') ) {
		$('#customize-theme-controls > ul > li.control-panel').each( function(){
			var panelIdentifier = $(this).attr('id').replace("accordion-panel-", "");
			if(_hoot_customizer_data.panelicons[panelIdentifier])
				$(this).children('h3').prepend('<i class="' + _hoot_customizer_data.panelicons[panelIdentifier] + '"></i>');
		});
	}


	/*** Panel Icon Menu ***/

	var $inav = $('#hoot-inav');

	// Add icon nav
	$inav.appendTo( "#customize-controls .wp-full-overlay-sidebar-content" );

	// Reorder icon list
	var inavList = '';
	$('#customize-theme-controls > ul > li.control-panel').each( function(){
		var panelID = $(this).attr('id'),
			$listItem = $inav.find("li[data-panel='" + panelID + "']");
		if($listItem.length)
			inavList += $("<div />").append($listItem).html();
	});
	var remains = $inav.children('#hoot-inav-icons').html();
	$inav.children('#hoot-inav-icons').empty().html(inavList + remains);


	/*** Panel Change ***/

	var $panelContainers = $('.control-panel-content'),
		$controlPanel = $('#customize-theme-controls > ul > li.control-panel'),
		$inavItem = $inav.find('li.hoot-inav-icon');

	// @bcomp
	$('.control-subsection', 'body.hoot-bcomp').click( function(event){
		$panelContainers.removeClass('section-open');
		if ( $(this).is('.open') )
			$(this).parent('.control-panel-content').addClass('section-open');
	});

	// @bcomp
	$('.control-panel-back, .hoot-inav-icon:not(#hoot-inav-premium), .change-theme', 'body.hoot-bcomp').click( function(event){
		$panelContainers.removeClass('section-open'); 
		$('.current-panel .open ul.accordion-section-content').hide();
	});

	$(".hoot-inav-icon").click( function(e){
		var target = $(this).data('panel');
		if ( $('body > .wp-full-overlay').is('.in-sub-panel.section-open') ) {
			// section open > click inav icon of same sub-panel > clicking h3 didnt work > hence .section-open is still there (irrelevant for bcomp as bcomp doesnt add .section-open to .wp-full-overlay)
			$('.current-panel .control-subsection.open .customize-section-back').click();
			// WP v4.3.0 only: section open > click inav of other sub-panel > .section-open stays > hence .wp-full-overlay.section-open .wp-full-overlay-sidebar-content { visibility: hidden; }
			$('body > .wp-full-overlay').removeClass('section-open');
		}
		$('#' + target + ' > h3').click(); // in non-bcomp, this does not bring back to control-panel when section-panel open within same control-panel. Hence we do above first. Also, it helps with premium fly not showing when section-open in non-bcomp
		e.stopPropagation(); // Stop click propogation to body (which closes any open fly)
	});
	$('.control-panel-back, .customize-panel-back, .customize-section-back, .hoot-inav-icon:not(#hoot-inav-premium), .change-theme, .control-panel h3').click( function(e){
		$inavItem.removeClass('active');
		var current = $controlPanel.filter('.current-panel').attr('id');
		$inavItem.filter('[data-panel=' + current + ']').addClass('active');
	});


	/*** Fly Panels - generic ***/
	// This code doesnt 'do' anything. It just acts as framework for other flypanel types.

	var $body = $("body"),
		$flypanelButtons = $('.hoot-flypanel-button'),
		initFly = function() {
			$flypanelButtons.click( function(event){
				if( $body.data('flypanel')=='open' && $(this).data('flypanel')=='open' ) {
					closeFly();
				} else {
					closeFly();
					openFly($(this));
				}
				event.stopPropagation();
			});
			$('.hoot-flypanel-back').click( function(event){
				closeFly();
				event.stopPropagation();
			});
			$('.hoot-flypanel').click( function(event){
				event.stopPropagation();
			});
			$body.click( function(event){
				if ( ! $(event.target).closest('.media-modal').length )
					closeFly();
			});
		},
		closeFly = function(){
			$body.data('flypanel','close');
			$body.data('flypanelbutton','');
			$flypanelButtons.data('flypanel','close');
			$body.trigger('closeflypanel');
		},
		openFly = function($flypanelButton){
			$body.data('flypanel','open');
			$body.data('flypanelbutton',$flypanelButton);
			$flypanelButton.data('flypanel','open');
			$body.trigger('openflypanel');
		};

	initFly();


});


(function($){
$(window).load(function() {


	/*** Customizer Footer ***/

	$('.change-theme').appendTo('#customize-footer-actions').html('<span class="dashicons dashicons-randomize"></span><span class="change-theme-label">Theme</span>');

	$('.stretch-sidebar').appendTo('#customize-footer-actions').click( function(event){
		var $overlay = $('.wp-full-overlay');
		$overlay.toggleClass('stretched');
		if($overlay.is('.stretched'))
			$(this).addClass('active');
		else
			$(this).removeClass('active');
	});
	$('.collapse-sidebar').click( function(event){
		$('.wp-full-overlay').removeClass('stretched');
		$('.stretch-sidebar').removeClass('active');
	});


});
})(jQuery);