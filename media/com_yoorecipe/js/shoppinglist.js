jQuery( document ).ready(function() {
	
	updateShoppingListDetailStatus = function(sld_id, el) {
		showLoading();
		
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateShoppingListDetailStatus&format=raw',
			data: {
				'id': sld_id,
				'status':el.checked
			},
		}).done(function(json) {
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
});