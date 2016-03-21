jQuery( document ).ready(function() {
	
	editShoppingListTitle = function(sl_id){
		jQuery('#sl_title_'+sl_id).hide();
		jQuery('#title_'+sl_id).val(jQuery('#sl_title_'+sl_id).text());
		jQuery('#title_'+sl_id).fadeIn();
		jQuery('#title_'+sl_id).bind('keydown', function (event) {
			if (event.key == 'enter') {
				event.stopPropagation(); updateShoppingListTitle(sl_id); return false;
			}
		});
	}
	
	updateShoppingListTitle = function(sl_id){
		
		var title = jQuery('#title_'+sl_id).val();
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateShoppingListTitle&format=raw',
			data: {
				'id':sl_id,
				'title':title
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#title_'+sl_id).fadeOut('slow', function() {
					jQuery('#sl_title_'+sl_id).html(json.html);
					jQuery('#sl_title_'+sl_id).fadeIn();
				});
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	deleteShoppingList = function(sl_id) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=deleteShoppingList&format=raw',
			data: {'id':sl_id},
		}).done(function(json) {
			if (json.status) {
				jQuery('#shoppinglist_'+sl_id).fadeOut().remove();
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
			data: {'title':title},
		}).done(function(json) {
			if (json.status) {
				jQuery('#shoppinglists-body').append(json.html);
				jQuery('#modal-create-shopping-list').modal('hide');	
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	loadCreateShoppingListModal = function() {
		jQuery('#modal-create-shopping-list').modal('show');
	}
});