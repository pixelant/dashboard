/**
 * Initialize the drag and drop functionality
 */
define(['jquery', 'draggableGridList', 'jquery-ui/draggable'], function ($) {

	var GridList = {
		sortableContainers: '.grid-container',
		contentIdentifier: '.grid-item'
	};

	GridList.initialize = function() {
		$(".gridster ul").gridList({
			direction: 'vertical',
			lanes: 3,
			cellHeight: 250
		},
		{
			handle: '.panel-heading'
		});
	};

	/**
	 * initialize function
	 */
	return function() {
		GridList.initialize();
		return GridList;
	}();
});
