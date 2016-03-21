var yoorecipeSearchFormValidator;
jQuery( document ).ready(function() {
	
	Locale.use(jQuery('#language_tag').val());
	yoorecipeSearchFormValidator = new Form.Validator.Inline('adminForm', {
		stopOnFailure: true,
		useTitles: false,
		errorPrefix: '',
		onFormValidate: function(passed, form, event) {
			if (passed) {
				showLoading();
				form.submit();
			}
		}
	});
	
	window.addEvent('keydown', function(event){
		if (event.key == 'enter') { yoorecipeSearchFormValidator.validate(); event.stopPropagation();}
	});
	
	// Prevent conflicts with mootools
	jQuery.ui.slider.prototype.widgetEventPrefix = 'slider';
	
	jQuery("#slider-kcal").slider({
		range: true,
		min: 0,
		max: 500,
		values: [ 0, 500 ],
		slide: function( event, ui ) {
			jQuery("#kcal").val(ui.values[0]+" - "+ui.values[1]);
		}
	});
	jQuery("#kcal").val(jQuery("#slider-kcal" ).slider("values", 0)+" - "+jQuery("#slider-kcal").slider("values", 1));
	
	jQuery( "#slider-carbs" ).slider({
		range: true,
		min: 0,
		max: 400,
		values: [ 0, 400 ],
		slide: function( event, ui ) {
			jQuery("#carbs").val(ui.values[0]+" - "+ui.values[1]);
		}
	});
	jQuery("#carbs").val(jQuery("#slider-carbs" ).slider("values", 0)+" - "+jQuery("#slider-carbs").slider("values", 1));
	
	jQuery( "#slider-fat" ).slider({
		range: true,
		min: 0,
		max: 100,
		values: [ 0, 100 ],
		slide: function( event, ui ) {
			jQuery("#fat").val(ui.values[0]+" - "+ui.values[1]);
		}
	});
	jQuery("#fat").val(jQuery("#slider-fat" ).slider("values", 0)+" - "+jQuery("#slider-fat").slider("values", 1));
	
	jQuery( "#slider-fat" ).slider({
		range: true,
		min: 0,
		max: 100,
		values: [ 0, 100 ],
		slide: function( event, ui ) {
			jQuery("#fat").val(ui.values[0]+" - "+ui.values[1]);
		}
	});
	jQuery("#fat").val(jQuery("#slider-fat" ).slider("values", 0)+" - "+jQuery("#slider-fat").slider("values", 1));
	
	jQuery( "#slider-proteins" ).slider({
		range: true,
		min: 0,
		max: 100,
		values: [ 0, 100 ],
		slide: function( event, ui ) {
			jQuery("#proteins").val(ui.values[0]+" - "+ui.values[1]);
		}
	});
	jQuery("#proteins").val(jQuery("#slider-proteins" ).slider("values", 0)+" - "+jQuery("#slider-proteins").slider("values", 1));
	
	
});