jQuery( document ).ready(function() {
		
	jQuery('#masonry-container').masonry({
		isAnimated: true,
		itemSelector : '.item-masonry',
		columnWidth : 10
	});
});