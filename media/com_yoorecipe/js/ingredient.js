var recipe;
jQuery.noConflict();
jQuery( document ).ready(function() {
	
	Recipe = function(options) {
		this.html = '';
		this.ingredient_groups = [];
		this.init(options);
	}
	
	Recipe.prototype = {
		
		init:function(options) {
			recipe = this;
			this.html = jQuery('#ingr_container');
			this.html.on("click",".add-ingredient-group", function(event){
				event.preventDefault();
				recipe.addIngredientGroup();
			});
			this._initPrototypes(options);
		},
		
		addIngredientGroup: function() {
			group_index = this.ingredient_groups.length;
			ingredient_group = new IngredientGroup(0, jQuery('#add_ingredient_group').val(), group_index);
			this.ingredient_groups.push(ingredient_group);
			jQuery('#ingredient_group_container').append(ingredient_group.getHTML());
			jQuery('input[name="add_quantity_'+group_index+'"]').focus();
		},
		
		addIngredientToGroup: function(group_index) {
		
			quantity 	= jQuery('input[name="add_quantity_'+group_index+'"]');
			unit		= jQuery('input[name="add_unit_'+group_index+'"]');
			description = jQuery('input[name="add_description_'+group_index+'"]');
			
			// var result = adminFormValidator.validateField(quantity) && adminFormValidator.validateField(unit) && adminFormValidator.validateField(description);
			
			ing_index = this.ingredient_groups[group_index].ingredients.length;
			ingredient = new Ingredient(0, quantity.val(), unit.val(), description.val(), group_index, ing_index);
			this.ingredient_groups[group_index].addIngredient(ingredient);
			
			jQuery('#nb_ingredients').val(parseInt(jQuery('#nb_ingredients').val()+1));
			jQuery('div[name="ingredient_group_'+group_index+'"] > div.ingredients > table > tbody.sortable').append(ingredient.getHTML());
			
			// Clean line
			quantity.val('');
			unit.val('');
			description.val('');
			quantity.focus();
			
			// jQuery('.sortable').sortable("refresh");
			jQuery('.sortable').sortable({ 
				placeholder: 'ui-sortable-placeholder',
				axis: 'y',
				containment: 'parent'
			});
		},
		
		_initPrototypes: function(options) {
		
			for(var i = 0; i < options.ingredient_groups.length; i++) {
			
				var ingredient_group = options.ingredient_groups[i];
				this.ingredient_groups.push(new IngredientGroup(ingredient_group.id, ingredient_group.label, ingredient_group.index));
				for(var j = 0; j < ingredient_group.ingredients.length; j++) {
					ingredient = ingredient_group.ingredients[j];
					this.ingredient_groups[i].addIngredient(new Ingredient(ingredient.id, ingredient.quantity, ingredient.unit, ingredient.description, ingredient.group_index, ingredient.index));
				}
			}
		},
		
		removeGroup: function(group_index) {
			ig_div = jQuery('div[name="ingredient_group_'+group_index+'"]');
			if (ig_div.data('group-id') != 0) {
				ig_div.find('input[name*="group_action"]').val('D');
				ig_div.find('input[name*="group"]').removeClass('required');
				ig_div.fadeTo(1000, 0.3);
			} else {
				ig_div.fadeOut().remove();
			}
		},
		
		cancelRemoveGroup: function(group_index) {
			ig_div = jQuery('div[name="ingredient_group_'+group_index+'"]');
			if (ig_div.data('group-id') != 0) {
				ig_div.find('input[name*="group_action"]').val('U');
				ig_div.find('input[name*="group"]').addClass('required');
				ig_div.fadeIn(1000, 0.3);
			} 
		},
		
		removeIngredient: function(group_index, ing_index) {
			ingr_div = jQuery('div[name="ingredient_group_'+group_index+'"]').find('tr[name="ingredient_'+ing_index+'"]');
			
			/* Xander deleting nutrition values for ingredient */
			quantity = ingr_div.find('input[name*="quantity"]').val();
			nutrition_id = ingr_div.find('input[name*="nutrition_ids"]').val();
			jQuery.ajax(
            {
                type: "POST",
                url: "index.php?option=com_yoorecipe&task=deleteIngredient&format=raw",
                data:
                {
                    'quantity'     : quantity,
                    'nutrition_id' : nutrition_id
                },
                success: function(result)
                {
                    if(result['success'] == 'true'){
                    	deleteFromNutritionFields(result['nutrition']);
                    }
                },
                dataType: "json"
            });
            /* Xander end */
			if (ingr_div.data('recipe-id') != 0) {
				ingr_div.find('input[name*="ingredient_action"]').val('D');
				ingr_div.find('input[name*="quantity"]').removeClass('validate-fraction');
				ingr_div.find('input[name*="description"]').removeClass('required');
				ingr_div.fadeTo(1000, 0.3);
			} else {
				ingr_div.fadeOut().remove();
			}

			


			jQuery('#nb_ingredients').val(parseInt(jQuery('#nb_ingredients').val()-1));
		},
		
		getGroups: function() {
			return this.ingredient_groups;
		}
	}
	
	IngredientGroup = function(id, label, index) {
		this.id		= id;
		this.label 	= label;
		this.index 	= index;
		this.html 	= '';
		this.ingredients = [];
		
		this.init();
	}
	
	IngredientGroup.prototype = {
	
		init: function() {
			ingredient_group = this;
		},
		addIngredient: function(ingredient) {
			this.ingredients.push(ingredient);
		},
		getHTML: function() {
		
			var html = '<div class="ingredient_group" name="ingredient_group_'+this.index+'" data-index="'+this.index+'" data-group-id="'+this.id+'">';
			html += '<div class="control-group">';
			html += '<div class="control-label"><strong>'+Joomla.JText._('COM_YOORECIPE_INGREDIENT_GROUP')+'</strong></div>';
			html += '<div class="controls">';
			html += '<input type="text" name="group[]" class="required" placeholder="'+Joomla.JText._('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER')+'" value="'+this.label+'"/>';
			html += '<input type="hidden" name="group_ordering[]" value="'+this.index+'"/>';
			html += '<input type="hidden" name="group_id[]" value=""/>';
			html += '<input type="hidden" name="group_action[]" value="I"/>';
			html += '<button type="button" class="btn" onclick="recipe.removeGroup('+this.index+')"><i class="icon-trash"></i> </button>';
			html += '<i class="icon-move"></i>';
			html += '</div>';
			html += '</div>';
			
			html += '<div class="ingredients">';
			html += '<table class="table table-striped">';
			
			html += '<tfoot>';
			html += '<tr class="add-ingredient">';
			html += '<td><input type="text" class="input-mini validate-fraction" name="add_quantity_'+this.index+'" placeholder="'+Joomla.JText._('COM_YOORECIPE_QUANTITY_PLACEHOLDER')+'" value=""/></td>';
			html += '<td><input type="text" class="input-medium" name="add_unit_'+this.index+'" placeholder="'+Joomla.JText._('COM_YOORECIPE_UNIT_PLACEHOLDER')+'" value=""/></td>';
			html += '<td><input type="text" class="input-medium" name="add_description_'+this.index+'" placeholder="'+Joomla.JText._('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER')+'" value="" onblur="recipe.addIngredientToGroup('+this.index+')"/></td>';
			html += '<td><button type="button" class="btn" onclick="recipe.addIngredientToGroup('+this.index+')">'+Joomla.JText._('COM_YOORECIPE_INGREDIENTS_ADD')+'</button></td>';
			html += '</tr>';
			html += '</tfoot>';
			
			html += '<tbody class="sortable">';
			
			for(var i = 0; i < this.ingredients.length; i++) {
				html += this.ingredients[i].getHTML();
			}
			html += '</tbody>';
			html += '</table>';
			html += '</div>';
			
			html += '</div>';
			
			this.html = html;
			return this.html;
		}
	}
		
	Ingredient = function(id, quantity, unit, description, group_index, index) {
		
		this.html 			= '';
		this.id 			= id;
		this.quantity 		= quantity;
		this.unit 			= unit;
		this.description	= description;
		this.index			= index;
		this.group_index	= group_index;
		
		this.init();
	}

	Ingredient.prototype = {
		
		_action 		: 'I',
		
		init: function() {
			ingredient = this;
		},
		
		setAction: function(action) {
			this._action = action;
		},
		getHTML: function() {
			
			if (this.id != 0) {this._action = 'U';}
			var html = '<tr class="sortable-row" name="ingredient_'+this.index+'" data-ingredient-id="'+this.id+'">';
			html += '<td><input type="text" class="input-mini validate-fraction" name="quantity[]" placeholder="'+Joomla.JText._('COM_YOORECIPE_QUANTITY_PLACEHOLDER')+'" value="'+this.quantity+'"/></td>';
			html += '<td><input type="text" class="input-medium" name="unit[]" placeholder="'+Joomla.JText._('COM_YOORECIPE_UNIT_PLACEHOLDER')+'" value="'+this.unit+'"/></td>';
			html += '<td><input type="text" class="input-medium required" name="description[]" placeholder="'+Joomla.JText._('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER')+'" value="'+this.description+'"/>';
			/* Xander added a field by default */
			html += '<td><input type="text" class="input-medium" name="standard_ingredients[]" placeholder="Standard Ingredient..." value="'+ jQuery('#real_ingredient').val() +'" readonly />';
			html += '<input type="hidden" name="ingredient_id[]" value="'+this.id+'"/>';
			html += '<input type="hidden" name="group_index[]" value="'+this.group_index+'"/>';
			html += '<input type="hidden" name="ingredient_action[]" value="'+this._action+'"/>';
			/* Xander added a field by default */
			html += '<input type="hidden" name="nutrition_ids[]" value="' + jQuery('#ing_description').attr('data-selectedid') + '"/>';
			html += '</td>';
			html += '<td>';
			html += '<button type="button" class="btn remove-ingredient" onclick="recipe.removeIngredient('+this.group_index+','+this.index+')"><i class="icon-trash"></i> </button>';
			html += '<i class="icon-move"></i>';
			html += '</td>';
			html += '</tr>';
			
			this.html = html;
			return this.html;
		}
	};
});