jQuery( document ).ready(function() {

	initDragDrop = function() {
	
		jQuery('div.draggable').draggable({cursor: "crosshair", revert: true});
		jQuery('div.droppable').droppable({
			accept: 'div.draggable',
			hoverClass: "drop-hover",
			drop: function( event, ui ) {
				date = jQuery( this ).data('date');
				if (ui.draggable.hasClass('recipe-queue')) {
					insertMeal(ui.draggable, date);
				} else{
					if (ui.draggable.hasClass('mp-'+date)) {
						hideLoading();
					} else {
						jQuery(this).append(ui.draggable);
						updateMealDate(ui.draggable, date);
					}
				}
			}
		});
	}
	
	updateMealDate = function (elt, date) {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateMealDate&format=raw', 
			data: {
				'meal_id': elt.data('meal_id'), 
				'recipe_id': elt.data('recipe_id'),
				'nb_servings': parseInt(jQuery('#mp_servings_'+elt.data('meal_id')).html()),
				'date': date,
			},
		}).done(function(json) {
			if (json.status) {
				elt.remove();
				jQuery('#meal_ctnr_'+date).append(json.html);
				jQuery('div.draggable').draggable({cursor: "crosshair", revert: true});
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	updateMealServings = function(meal_id, increment, elt) {
	
		nb_servings = parseInt(jQuery('#mp_servings_'+meal_id).html()) + increment;
		if (nb_servings < 1) {
			return false;
		}
		
		showLoading();
		elt.disabled = true;
		
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateMealServings&format=raw', 
			data: {
				'meal_id': meal_id, 
				'nb_servings': nb_servings
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mp_servings_'+meal_id).html(json.nb_servings);
			}
			elt.disabled = false;
			hideLoading();
		}).error(function(json) {
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
			elt.disabled = false;
			hideLoading();
		});
	}
	
	insertMeal = function (el, date) {
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=insertMeal&format=raw',
			data: {
				'recipe_id': el.data('recipe_id'),
				'date': date,
				'nb_servings': el.data('nb_servings')
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#meal_ctnr_'+date).append(json.html);
			}
			hideLoading();
			jQuery('div.draggable').draggable({cursor: "crosshair", revert: true});
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	deleteMeal = function(id) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=deleteMeal&format=raw',
			data: {'id': id},
		}).done(function(json) {
			if (json.status) {
				jQuery('#mp_id_'+id).popover('hide');
				jQuery('#mp_id_'+id).fadeOut().remove();
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
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
				jQuery('#recipe_queue_'+recipe_id).fadeOut().remove();
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	loadMealPlanDay = function(mode) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=loadMealPlanDay&format=raw',
			data: {
				'mode': mode,
				'start_date': jQuery('#start_date').val(),
				'end_date': jQuery('#end_date').val()
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#start_date').val(json.new_start_date);
				jQuery('#end_date').val(json.new_end_date);
				if (mode == 'prev') {
					jQuery('#mealplanner-container').prepend(json.html);
					jQuery('#meal_'+json.new_start_date).fadeIn();
					jQuery('#meal_'+json.old_end_date).fadeOut().remove();						
				} else {
					jQuery('#mealplanner-container').append(json.html);
					jQuery('#meal_'+json.old_start_date).fadeOut().remove();
					jQuery('#meal_'+json.new_end_date).fadeIn();
				}
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	searchQueuedRecipes = function() {
		showLoading();
		var x = new Request({
			url: 'index.php?option=com_yoorecipe&task=searchQueuedRecipes&format=raw', 
			method: 'post', 
			data: {
				'search_word': jQuery('#search_word').val()
			},
			onSuccess: function(result){
				json = JSON.decode(result);
				if (json.status) {
					jQuery('#queue-container').html(json.html);
					initDragDrop();
				}
				hideLoading();
			},
			onFailure: function(){
				alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
				hideLoading();
			}                
		}).send();
	}
	
	if (jQuery('#search_word')) {
		jQuery('#search_word').bind('keydown', function (e) {
			if (e.key == 'enter') { e.stopPropagation(); searchQueuedRecipes();}
		});
	}
	
	printMeals = function() {
	
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=printMeals&format=raw',
			data: {
				'print_start_date': jQuery('#startdate').val(),
				'print_end_date': jQuery('#enddate').val(),
				'return_url': jQuery('#return_url').val()
			},
		}).done(function(json) {
			if (json.status) {
				window.open(json.url);
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
		});
	}
	
	submitForm = function() {
		showLoading();
		$('adminForm').submit();
	}
	
	initDragDrop();
});