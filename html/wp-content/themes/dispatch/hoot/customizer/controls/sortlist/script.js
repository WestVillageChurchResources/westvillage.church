jQuery(document).ready(function($) {
	"use strict";

	$('ul.hoot-control-sortlist').each(function(){


		/*** Prepare Sortlist ***/

		var $self = $(this),
			$listItems = $self.children('li'),
			$listItemHeads = $listItems.children('.hoot-sortlistitem-head'),
			$listItemVisibility = $listItemHeads.children('.sortlistitem-display'),
			$listItemOptions = $listItems.children('.hoot-sortlistitem-options');

		if ( !$self.is('.default-open') ) {
			$listItemOptions.hide();
		}

		$listItemHeads.on('click', function(e){
			$(this).children('.sortlistitem-expand').toggleClass('options-open');
			$(this).siblings('.hoot-sortlistitem-options').slideToggle('fast');
		});

		$listItemVisibility.on('click', function(e){
			e.stopPropagation();
			var $liContainer = $(this).closest('li.hoot-control-sortlistitem');
			$liContainer.toggleClass('deactivated');
			var hideValue = ( $liContainer.is('.deactivated') ) ? '1' : '0';
			$(this).siblings('input.hoot-control-sortlistitem-hide').val(hideValue).trigger('change');
		});


		/*** Sortlist Control ***/

		var $optionsform = $self.find('input, textarea, select'),
			$input = $self.siblings('input.hoot-customizer-control-sortlist'),
			updateSortable = function(){
				$optionsform = $self.find('input, textarea, select'); // Get updated list item order
				// JSON.stringify( $optionsform.serializeArray() ) :: serializeArray does not create a multidimensional array. It simpy creates array with name/value pairs
				// Hence use $optionsform.serialize(). For more notes on this issue, see php file.
				$input.val( $optionsform.serialize() ).trigger('change');
			};

		$optionsform.on('change', updateSortable);

		if ( $self.is('.sortable') ) {
			$self.sortable({
				handle: ".sortlistitem-sort",
				placeholder: "hoot-control-sortlistitem-placeholder",
				update: function(event, ui) {
					updateSortable();
				},
				// start: function(e, ui){
				// 	ui.placeholder.height(ui.item.height());
				// },
				forcePlaceholderSize: true,
			});
		}

	});

});