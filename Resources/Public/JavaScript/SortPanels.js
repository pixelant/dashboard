define(['jquery', 'jquery-ui/sortable'], function ($) {
	
	var SortPanels = {
		sortableContainers: '.t3js-sortable',
		contentIdentifier: '.t3js-sortable'
	};

	SortPanels.initialize = function() {
		console.log('SortPanels initialize START');
		$(SortPanels.sortableContainers).sortable({
			connectWith: SortPanels.sortableContainers,
			handle: ".panel-heading",
			/*distance: 20,*/
			cursor: 'move',
			helper: 'clone',
			tolerance: 'pointer',
			start: function(e, ui) {
				SortPanels.onSortStart($(this), ui);
				$(this).addClass('t3-is-dragged');
			}
		}).disableSelection();
		console.log('SortPanels initialize END');
	};

	/**
	 * Called when an item is about to be moved
	 */
	SortPanels.onSortStart = function($container, ui) {
		var $item = $(ui.item),
			$helper = $(ui.helper),
			$placeholder = $(ui.placeholder);

		$placeholder.height($item.height());
	};

	/**
	 * initialize function
	 */
	return function() {
		SortPanels.initialize();
		return SortPanels;
	}();
});