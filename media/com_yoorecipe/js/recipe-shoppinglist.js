jQuery( document ).ready(function() {
	
	addRecipeToShoppingList = function(sl_id) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=addRecipeToShoppingList&format=raw',
			data: {'sl_id': sl_id,
				'recipe_id': $('id_recipe').value,
				'nb_persons': $('nb_persons_sl').value
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#modal-add-to-shopping-list').modal('hide');
				
				jQuery('#see-shoppinglist-lnk').attr('href', json.href); 
				jQuery('#modal-shopping-list-updated').modal('show');
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	createShoppingList = function() {
		showLoading();
		var title = jQuery('#shoppinglist_title').val();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=createShoppingList&format=raw',
			data: {
				'title':title,
				'fromRecipe': '1'
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#modal-add-to-shopping-list-body').append(json.html);
				jQuery('#modal-add-to-shopping-list').modal('show');
				jQuery('#modal-create-shopping-list').modal('hide');
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	loadAddToShoppingListModal = function() {
		
		nb_persons = jQuery('#nb_persons').val();
		if (jQuery('#slider') != undefined) {
			nb_persons = jQuery('#slider').val();
		} else if (s != undefined) {
			nb_persons = s.getValue();
		}
		jQuery('#nb_persons_sl').val(nb_persons);
		jQuery('#modal-add-to-shopping-list').modal('show');
	}
	
	loadCreateShoppingListModal = function() {
		jQuery('#modal-add-to-shopping-list').modal('hide');
		jQuery('#modal-create-shopping-list').modal('show');
	}
	
	Locale.use(jQuery('#language_tag').val());
	createShoppingListFormValidator = new Form.Validator.Inline('create-shopping-list-form', {
		stopOnFailure: true,
		useTitles: false,
		errorPrefix: '',
		ignoreHidden:false,
		onFormValidate: function(passed, form, event) {
			if (passed) {
				createShoppingList();
			}
		}
	});
});