var editShoppingListDetailFormValidator;
jQuery( document ).ready(function() {
	
	deleteShoppingListDetail = function(sld_id) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=deleteShoppingListDetail&format=raw',
			data: {'id': sld_id}
		}).done(function(json) {
			if (json.status) {
				jQuery('#ingr_'+sld_id).fadeOut().remove();
			}
			hideLoading();
		}).error(function(json) {
		hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	loadUpdateShoppingListDetailModal = function(sld_id){
		jQuery('#sld_quantity').val(jQuery('#sld_quantity_'+sld_id).val());
		jQuery('#sld_description').val(jQuery('#sld_description_'+sld_id).val());
		jQuery('#sld_id').val(sld_id);
		jQuery('#modal-edit-shoppinglist-detail').modal('show');
	}
	
	loadCreateShoppingListDetailModal = function(){
		jQuery('#sld_quantity').val('');
		jQuery('#sld_description').val('');
		jQuery('#sld_id').val('');
		jQuery('#modal-edit-shoppinglist-detail').modal('show');
	}
	
	createShoppingListDetail = function(quantity, description) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=createShoppingListDetail&format=raw',
			data: {
				'sl_id': jQuery('#sl_id').val(), 
				'quantity': quantity,
				'description': description
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#shoppinglist-ul').append(json.html);
				jQuery('#modal-edit-shoppinglist-detail').modal('hide');
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	updateShoppingListDetail = function(sld_id, quantity, description) {
		
		showLoading();
		jQuery.ajax({
			method: 'POST',
			url: 'index.php?option=com_yoorecipe&task=updateShoppingListDetail&format=raw',
			data: {
				'id': sld_id,
				'quantity': quantity,
				'description': description
			},
		}).done(function(json) {
			if (json.status) {
				jQuery('#sld_quantity_'+sld_id).val(quantity);
				jQuery('#sld_description_'+sld_id).val(description);
				jQuery('#sld_label_'+sld_id).text(quantity+' '+description);
				jQuery('#modal-edit-shoppinglist-detail').modal('hide');
			}
			hideLoading();
		}).error(function(json) {
			hideLoading();
			alert(Joomla.JText._('COM_YOORECIPE_ERROR_OCCURED', true));
		});
	}
	
	Locale.use(jQuery('#language_tag').val());
	editShoppingListDetailFormValidator = new Form.Validator.Inline('edit-shoppinglist-detail-form', {
		stopOnFailure: true,
		useTitles: false,
		errorPrefix: '',
		onFormValidate: function(passed, form, event) {
			if (passed) {
				var sld_id = jQuery('#sld_id').val();
				var quantity = jQuery('#sld_quantity').val();
				var description = jQuery('#sld_description').val();

				if (sld_id > 0) {
					updateShoppingListDetail(sld_id, quantity, description);
				} else {
					createShoppingListDetail(quantity, description);
				}
			}
		}
	});
	
	editShoppingListDetailFormValidator.add('validate-fraction', {
	
		errorMsg: Joomla.JText._('COM_YOORECIPE_FRACTION', true),
		test: function(field){
			decimal_pattern = new RegExp(/^\d*[,\.]?\d*$/g);
			fraction_pattern = new RegExp(/^(\d*)[\s]?\d*(\/)?\d*$/g);
			if (decimal_pattern.test(field.get('value')) || fraction_pattern.test(field.get('value'))) {
				return true;
			}else {
				return false;
			}
		}
	});
});