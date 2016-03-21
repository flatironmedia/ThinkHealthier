jQuery( document ).ready(function() {
		
	// Generic
	showLoading = function() {
		jQuery('div.huge-ajax-loading').css('display','block');
	}

	hideLoading = function() {
		jQuery('div.huge-ajax-loading').css('display','none');
	}
	
	deleteRecipe = function(id) {
		
		var result = confirm(Joomla.JText._('COM_YOORECIPE_CONFIRM_DELETE'));
		if (!result) {
			return false;
		}

		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=deleteRecipe&format=raw&id='+id,
		}).done(function(json) {
			if (json.status) {
				jQuery('#div_recipe_'+id).fadeOut().remove();
			} else {
				alert(json.msg);
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION'));
		});
	}
	
	updateLimitBox = function(elt) {
		var els = jQuery('select.yoorecipe-limitbox'),i,l=els.length;
		for(i=0;i<l;i++){
			els[i].selectedIndex = elt.selectedIndex;
			els[i].options[elt.selectedIndex].value = elt.options[elt.selectedIndex].value;
			els[i].options[elt.selectedIndex].text = elt.options[elt.selectedIndex].text;
		}
	}
	
	addToFavourites = function(recipe_id) {
	
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=addToFavourites&format=raw', 
			data: {'recipe_id':recipe_id},
		}).done(function(json) {
			if (json.status) {
				jQuery('#fav_'+recipe_id).removeClass('ajax-loading').html(json.html);
			}
		}).error(function(json) {
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED'));
		});
	}
	
	removeFromFavourites = function(recipe_id) {
	
		jQuery('#fav_'+recipe_id).empty().addClass('ajax-loading');
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=removeFromFavourites&format=raw', 
			data: {'recipe_id':recipe_id},
		}).done(function(json) {
		if (json.status) {
				jQuery('#fav_'+recipe_id).removeClass('ajax-loading').html(json.html);
			}
		}).error(function(json) {
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED'));
		});
	}
	
	addRecipeToQueue = function(recipe_id) {
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=addRecipeToQueue&format=raw',
			data: {
				'recipe_id': recipe_id
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mealplanner_'+recipe_id).html(json.html)
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
			data: {
				'recipe_id': recipe_id
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mealplanner_'+recipe_id).html(json.html);
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
		});
	}
	
	com_yoorecipe_reportReview = function(recipe_id, comment_id) {
	
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=reportReview&format=raw', 
			data: {'recipeId':recipe_id,'commentId':comment_id},
		}).done(function(json) {
			hideLoading();
			if (json.status) {
				jQuery('#yoorecipe_comment_'+comment_id).addClass('greyedout');
			} else {
				alert(json.msg);
			}
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED'));
		});
	}
	
	com_yoorecipe_deleteComment = function(recipe_id, comment_id) {
	
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=deleteComment&format=raw', 
			data: {'recipeId':recipe_id,'commentId':comment_id},
		}).done(function(json) {
			hideLoading();
			if (json.status) {
				jQuery('#yoorecipe_comment_'+comment_id).remove();
				jQuery('#div-recipe-rating').html(json.html);
			} else {
				alert(json.msg);
			}
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED'));
		});
	}
});