jQuery( document ).ready(function() {
	
	jQuery(".fancybox").fancybox();
		
	addRecipeToQueue = function(recipe_id) {
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=addRecipeToQueue&format=raw',
			data: {'recipe_id': recipe_id},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mealplanner_'+recipe_id).html(json.html2);
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
		});
	}
	
	removeRecipeFromQueue = function(recipe_id) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=removeRecipeFromQueue&format=raw',
			data: {'recipe_id': recipe_id},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mealplanner_'+recipe_id).html(json.html2);
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
		});
	}
});