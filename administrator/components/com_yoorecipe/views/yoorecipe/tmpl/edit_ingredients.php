<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidation');
JHtmlBehavior::framework();

$recipe = $this->item;

$document		= JFactory::getDocument();
$params 		= JComponentHelper::getParams('com_yoorecipe');
$use_fractions	= $params->get('use_fractions', 0);

//adding nutritionix styling
$document->addStyleSheet('/administrator/components/com_yoorecipe/css/style-popup.css');

// Init ingredients object
$json_ingr_groups = array();

if (isset($recipe->groups)) {
	
	$groups = $recipe->groups;
	foreach ($groups as $group_index => $group) {
		
		$json_ingredients = array();
		foreach ($group->ingredients as $ingredient_index => $ingredient) {
		
			$quantity 			= ($use_fractions) ? JHTMLIngredientUtils::decimalToFraction(round($ingredient->quantity, 2)) : $ingredient->quantity;
			$quantity_string 	= ($quantity == 0) ? '' : $quantity;
			
			$json_ingredients[] = '{"id":"'.$ingredient->id.'", "quantity":"'.$quantity_string.'", "unit":"'.$ingredient->unit.'", "description":"'.$ingredient->description.'", "index":"'.$ingredient_index.'", "group_index":"'.$group_index.'" }';
		}
		$json_ingr_groups[] = "{'id': '".$group->id."', 'label': '".$group->label."', 'index': '".$group_index."', 'ingredients' : [".implode(",", $json_ingredients)."]}";
	}
}

$script = "jQuery( document ).ready(function() {
	
	options = { 'ingredient_groups': [".implode(",", $json_ingr_groups)."]};
	recipe = new Recipe(options);
	
	showLoading = function() {
		jQuery('div.huge-ajax-loading').css('display', 'block');
	}

	hideLoading = function() {
		jQuery('div.huge-ajax-loading').css('display', 'none');
	}
	
	jQuery.noConflict();
	jQuery('.sortable').sortable({ 
	placeholder: 'ui-sortable-placeholder',
		axis: 'y',
		containment: 'parent'
	});
});";
$document->addScriptDeclaration($script);
$nb_ingredients = 0;
?>

<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('nb_persons'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('nb_persons'); ?>
		<?php echo $this->form->getInput('serving_type_id');  ?>
	</div>
</div>

<?php echo $this->form->renderField('use_slider'); ?>

<div id="ingr_container">
	<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_INGREDIENTS'); ?></h2>
	<div class="row-fluid">
		<div class="span6">
			<div id="ingredient_group_container" class="row-fluid sortable">
<?php
			// Loop over ingredients
			if (isset($recipe->groups)) { 
				
				foreach ($recipe->groups as $group_index => $group) {

					$html = array();
					$html[] = '<div class="row-fluid ingredient_group" name="ingredient_group_'.$group_index.'" data-index="'.$group_index.'" data-group-id="'.$group->id.'">';

					$html[] = '<div class="control-group">';
					$html[] = '<div class="control-label"><strong>'.JText::_('COM_YOORECIPE_INGREDIENT_GROUP').'</strong></div>';
					$html[] = '<div class="controls">';
					$html[] = '<input type="text" name="group[]" class="required" placeholder="'.JText::_('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER').'" value="'.$group->label.'"/>';
					$html[] = '<input type="hidden" name="group_ordering[]" value="'.$group_index.'"/>';
					$html[] = '<input type="hidden" name="group_id[]" value="'.$group->id.'"/>';
					$html[] = '<input type="hidden" name="group_action[]" value="U"/>';
					$html[] = '<button type="button" class="btn" onclick="recipe.removeGroup('.$group_index.')"><i class="icon-trash"></i> </button>';
					$html[] = '<i class="icon-move"></i>';
					$html[] = '</div>';
					$html[] = '</div>';
						
					$html[] = '<div class="ingredients">';
					$html[] = '<table class="table table-striped" id="yoorecipe_table">';
					
					$html[] = '<tfoot>';
					$html[] = '<tr class="add-ingredient">';
					$html[] = '<td><input type="text" class="input-mini" name="add_quantity_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_QUANTITY_PLACEHOLDER').'" value=""/></td>';
					$html[] = '<td><input type="text" class="input-medium" name="add_unit_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_UNIT_PLACEHOLDER').'" value=""/></td>';
                    $html[] = '<td><input type="text" class="input-medium" name="add_description_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER').'" value="" onblur="recipe.addIngredientToGroup('.$group_index.')"/></td>';
					$html[] = '<td><input type="text" class="input-medium" name="add_standard_ingredient_'.$group_index.'" placeholder="Standard Ingredient..." value="" readonly /></td>';
					$html[] = '<td><button type="button" id="yoorecipe_add_ingredient_button" class="btn" onclick="recipe.addIngredientToGroup('.$group_index.')">'.JText::_('COM_YOORECIPE_INGREDIENTS_ADD').'</button></td>';
					$html[] = '</tr>';
					$html[] = '</tfoot>';
					
					$html[] = '<tbody class="sortable">';
					foreach ($group->ingredients as $ing_index => $ingredient) {
					
						$quantity = ($use_fractions) ? JHTMLIngredientUtils::decimalToFraction(round($ingredient->quantity, 2)) : $ingredient->quantity;
						$quantity_string = ($quantity == 0) ? '' : $quantity;
					
						$html[] = '<tr class="sortable-row" name="ingredient_'.$ing_index.'" data-ingredient-id="'.$ingredient->id.'">';
						$html[] = '<td><input type="text" class="input-mini validate-fraction" name="quantity[]" placeholder="'.JText::_('COM_YOORECIPE_QUANTITY_PLACEHOLDER').'" value="'.$quantity_string.'"/></td>';
						$html[] = '<td><input type="text" class="input-medium" name="unit[]" placeholder="'.JText::_('COM_YOORECIPE_UNIT_PLACEHOLDER').'" value="'.$ingredient->unit.'"/></td>';
                        $html[] = '<td><input type="text" class="input-medium required" name="description[]" placeholder="'.JText::_('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER').'" value="'.$ingredient->description.'"/></td>';
						$html[] = '<td><input type="text" class="input-medium" name="standard_ingredients[]" placeholder="Standard Ingredient" value="'.$ingredient->standard_ingredient.'" readonly /></td>';
						$html[] = '<td>';
						$html[] = '<input type="hidden" name="ingredient_id[]" value="'.$ingredient->id.'"/>';
						$html[] = '<input type="hidden" name="group_index[]" value="'.$group_index.'"/>';
                        $html[] = '<input type="hidden" name="ingredient_action[]" value="U"/>';
						$html[] = '<input type="hidden" name="nutrition_ids[]" value="'.$ingredient->nutrition_id.'"/>';
						$html[] = '<button type="button" class="btn remove-ingredient yoorecipe-delete-on-click-trigger" onclick="recipe.removeIngredient('.$group_index.','.$ing_index.')"><i class="icon-trash"></i> </button>';
						$html[] = '<i class="icon-move"></i>';
						$html[] = '</td>';
						$html[] = '</tr>';
						
						$nb_ingredients++;
					}
					$html[] = '</tbody>';
					$html[] = '</table>';
					$html[] = '</div>';

					$html[] = '</div>';
					echo implode("\n", $html);
					
				} // End foreach ($recipe->groups as $group_index => $group) {

			} // End if (isset($recipe->groups)) {
            else
            {

            }
?>
			</div>
		</div>
	</div>
	<input type="text" placeholder="<?php echo JText::_('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER'); ?>" id="add_ingredient_group" name="add_ingredient_group" class="input"/>
	<button type="button" class="btn add-ingredient-group" id="yoorecipe_add_ingredient"><?php echo JText::_('COM_YOORECIPE_ADD_INGREDIENT_GROUP'); ?></button>
</div>

<input type="hidden" name="nb_ingredients" id="nb_ingredients" class="btn validate-ingredients" value="<?php echo $nb_ingredients; ?>"/>

<br>
<br>
<br>
<div id="ingredient_form">

    <input type="hidden" name="ing_id" id="ing_id"/>
    <input type="hidden" name="ing_recipe_id" id="ing_recipe_id" value="<?php echo $this->item->id; ?>"/>

    <!-- OGOSense :: Strandard Label -->
    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('Look up ingredient'); ?>
        </div>
        <div class="controls">
            <input style="width: 280px;" type="text" name="std_label" id="std_label" />
            <button onclick="offsetSerach(0, 10); return false;" class="btn" id="serach-ingredient" data-popup-target="#popup-result">Search</button>
        </div>
    </div>
    <div>
        <a name="resultArea"></a>
        <a name="resultArea2"></a>
        <div id="popup-result" class="popup">
            <div class="popup-body">
                <div class="popup-content">
                    <div id="result"></div>
                </div>
            </div>
        </div>
        <div class="popup-overlay"></div>
    </div>

    <input type="hidden" name="offset" id="offset" value="10"/>
    <input type="hidden" name="limit" id="limit" value="0"/>

    <script type="text/javascript">
        jQuery(document).ready(function(){

            if(!jQuery('input[name="group[]"]').val()){

                jQuery('#add_ingredient_group').val('Recipe');
                jQuery('#yoorecipe_add_ingredient').click();
            }

            jQuery('[data-popup-target]').click(function () {
                jQuery('html').addClass('overlay');
                var activePopup = jQuery(this).attr('data-popup-target');
                jQuery(activePopup).addClass('visible');
            });

            jQuery(document).keyup(function (e) {
                if (e.keyCode == 27 && jQuery('html').hasClass('overlay')) {
                    clearPopup();
                }
            });

            jQuery('.popup-exit').click(function () {
                clearPopup();
            });

            jQuery('.popup-overlay').click(function () {
                clearPopup();
            });

            function clearPopup() {
                jQuery('.popup.visible').addClass('transitioning').removeClass('visible');
                jQuery('html').removeClass('overlay');

                setTimeout(function () {
                    jQuery('.popup').removeClass('transitioning');
                }, 200);
            }
            // Offset AJAX Search
            offsetSerach = function(offset, limit) {
                jQuery('#result').html('<strong>Processing your request...</strong>');
                jQuery('html,body').animate({scrollTop: jQuery("a[name='resultArea']").offset().top}, 'fast');
                jQuery.post(
                    'index.php?option=com_yoorecipe&task=getIngredientsNutritionix&format=raw',
                    {
                        'query'  : jQuery('#std_label').val(),
                        'offset' : offset,
                        'limit'  : limit
                    },
                    function(searchResult) {
                        jQuery('#result').html(searchResult);
                        jQuery('html,body').animate({scrollTop: jQuery("a[name='resultArea2']").offset().top}, 'fast');
                    }
                );
                return false;
            }
            // Click on search if ENTER is pressed
            jQuery("#std_label").keyup(function(event){
                if(event.keyCode == 13){
                    jQuery("#serach-ingredient").click();
                }
            });
            // On label click event
            processLabelClick = function(el, ing_id) {
                var arr   = jQuery(el).text().split('-');
                var label = arr[0].trim().toLowerCase();
                if(ing_id == 0) {
                    var desc  = jQuery('#ing_description');
                    desc.val(label);
                    desc.attr("data-selectedid", jQuery(el).attr('data-nutid'));
                    jQuery('#real_ingredient').val(jQuery(el).text());
                    clearPopup();
                } else {
                    var desc  = jQuery('#ing_description_' + ing_id);
                    desc.val(label);
                    desc.attr("data-selectedid", jQuery(el).attr('data-nutid'));
                    jQuery('#real_ingredient_' + ing_id).val(jQuery(el).text());
                    clearPopup();
                }
            }

            // AJAX search
            searchIngFunction = function(el, offset, limit) {
                jQuery('html').addClass('overlay');
                var activePopup = jQuery("#serach-ingredient_" + el).attr('data-popup-target');
                jQuery(activePopup).addClass('visible');

                var result = jQuery('#result_' + el);
                result.html('<strong>Processing your request...</strong>');
                jQuery.post(
                    'index.php?option=com_yoorecipe&task=getIngredientsNutritionix&format=raw',
                    {
                        'query'         : jQuery('#std_label_' + el).val(),
                        'ingredient_id' : el,
                        'offset' : offset,
                        'limit'  : limit
                    },
                    function(searchResult) {
                        result.html(searchResult);
                    }
                );
                return false;
            }
            deleteFromNutritionFields = function(nut_val) {
                serving_size = parseFloat(jQuery('#jform_serving_size').val()) - parseFloat(nut_val['serving_size']);
                kcal = parseFloat(jQuery('#jform_kcal').val()) - parseFloat(nut_val['kcal']);
                fat = parseFloat(jQuery('#jform_fat').val()) - parseFloat(nut_val['fat']);
                saturated_fat = parseFloat(jQuery('#jform_saturated_fat').val()) - parseFloat(nut_val['saturated_fat']);
                proteins = parseFloat(jQuery('#jform_proteins').val()) - parseFloat(nut_val['proteins']);
                carbs = parseFloat(jQuery('#jform_carbs').val()) - parseFloat(nut_val['carbs']);
                sugar = parseFloat(jQuery('#jform_sugar').val()) - parseFloat(nut_val['sugar']);
                fibers = parseFloat(jQuery('#jform_fibers').val()) - parseFloat(nut_val['fibers']);
                cholesterol = parseFloat(jQuery('#jform_cholesterol').val()) - parseFloat(nut_val['cholesterol']);
                salt = parseFloat(jQuery('#jform_salt').val()) - parseFloat(nut_val['salt']);
                kjoule = parseFloat(jQuery('#jform_kjoule').val()) - parseFloat(nut_val['kjoule']);

                jQuery('#jform_serving_size').val(parseFloat(serving_size).toFixed(2));
                jQuery('#jform_kcal').val(parseFloat(kcal).toFixed(2));
                jQuery('#jform_fat').val(parseFloat(fat).toFixed(2));
                jQuery('#jform_saturated_fat').val(parseFloat(saturated_fat).toFixed(2));
                jQuery('#jform_proteins').val(parseFloat(proteins).toFixed(2));
                jQuery('#jform_carbs').val(parseFloat(carbs).toFixed(2));
                jQuery('#jform_sugar').val(parseFloat(sugar).toFixed(2));
                jQuery('#jform_fibers').val(parseFloat(fibers).toFixed(2));
                jQuery('#jform_cholesterol').val(parseFloat(cholesterol).toFixed(2));
                jQuery('#jform_salt').val(parseFloat(salt).toFixed(2));
                jQuery('#jform_kjoule').val(parseFloat(kjoule).toFixed(2));                
            }
            addToNutritionFields = function(nut_val) {

                serving_size = parseFloat(jQuery('#jform_serving_size').val()) + parseFloat(nut_val['serving_size']);
                kcal = parseFloat(jQuery('#jform_kcal').val()) + parseFloat(nut_val['kcal']);
                fat = parseFloat(jQuery('#jform_fat').val()) + parseFloat(nut_val['fat']);
                saturated_fat = parseFloat(jQuery('#jform_saturated_fat').val()) + parseFloat(nut_val['saturated_fat']);
                proteins = parseFloat(jQuery('#jform_proteins').val()) + parseFloat(nut_val['proteins']);
                carbs = parseFloat(jQuery('#jform_carbs').val()) + parseFloat(nut_val['carbs']);
                sugar = parseFloat(jQuery('#jform_sugar').val()) + parseFloat(nut_val['sugar']);
                fibers = parseFloat(jQuery('#jform_fibers').val()) + parseFloat(nut_val['fibers']);
                cholesterol = parseFloat(jQuery('#jform_cholesterol').val()) + parseFloat(nut_val['cholesterol']);
                salt = parseFloat(jQuery('#jform_salt').val()) + parseFloat(nut_val['salt']);
                kjoule = parseFloat(jQuery('#jform_kjoule').val()) + parseFloat(nut_val['kjoule']);
                
                jQuery('#jform_serving_size').val(parseFloat(serving_size).toFixed(2));
                jQuery('#jform_kcal').val(parseFloat(kcal).toFixed(2));
                jQuery('#jform_fat').val(parseFloat(fat).toFixed(2));
                jQuery('#jform_saturated_fat').val(parseFloat(saturated_fat).toFixed(2));
                jQuery('#jform_proteins').val(parseFloat(proteins).toFixed(2));
                jQuery('#jform_carbs').val(parseFloat(carbs).toFixed(2));
                jQuery('#jform_sugar').val(parseFloat(sugar).toFixed(2));
                jQuery('#jform_fibers').val(parseFloat(fibers).toFixed(2));
                jQuery('#jform_cholesterol').val(parseFloat(cholesterol).toFixed(2));
                jQuery('#jform_salt').val(parseFloat(salt).toFixed(2));
                jQuery('#jform_kjoule').val(parseFloat(kjoule).toFixed(2));
            }
            insertIngredient = function(){
                showLoading();
                description = jQuery('#ing_description').val();
                quantity = jQuery('#ing_quantity').val();
                unit = jQuery('#ing_unit option:selected').val();
                ing_recipe_id = jQuery('#ing_recipe_id').val();
                standard_ingredient = jQuery('#real_ingredient').val();

                if(description && quantity){              
                    jQuery.ajax(
                    {
                        type: "POST",
                        url: "index.php?option=com_yoorecipe&task=insertIngredient&format=raw",
                        data:
                        {
                            'recipe_id'     : ing_recipe_id,
                            'ingredient_id' : jQuery('#ing_description').attr('data-selectedid'),
                            'quantity'      : quantity
                        },
                        success: function(result)
                        {
                            if(result['success'] == 'true'){

                                addToNutritionFields(result['nutrition']);
                                recipe_group = jQuery('#ing_group_id option:selected').val();
                                jQuery('input[name=add_quantity_' + recipe_group + ']').val(quantity);
                                jQuery('input[name=add_unit_' + recipe_group + ']').val(unit);
                                jQuery('input[name=add_description_' + recipe_group + ']').val(description);
                                //jQuery('input[name=add_standard_ingredient_' + recipe_group + ']').val(standard_ingredient);
                                recipe.addIngredientToGroup(recipe_group);
                            }
                        },
                        dataType: "json"
                    });
                }
                else{
                    alert('Description or Quantity missing!');
                    jQuery('#ing_description').focus();
                }
                hideLoading();
                return false;
            }
        });
    </script>

    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('Standard ingredient'); ?>
        </div>
        <div class="controls">
            <!--<input type="text" style="width:350px;" name="real_ingredient" id="real_ingredient" readonly="true"/>-->
            <textarea style="width: 350px;" name="real_ingredient" id="real_ingredient" readonly="true"></textarea>
        </div>
    </div>
    <!-- OGOSense :: End -->

    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('COM_YOORECIPE_INGREDIENTS_DESCRIPTION'); ?>
        </div>
        <div class="controls">
            <input type="text" style="width:350px;" name="ing_description" id="ing_description" data-selectedid=""/>
            <!-- <textarea style="width:350px;" name="ing_description" id="ing_description" data-selectedid="" class="required"></textarea> -->
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('COM_YOORECIPE_INGREDIENTS_QUANTITY'); ?>
        </div>
        <div class="controls">
            <input type="text" name="ing_quantity" value="1" id="ing_quantity" class="validate-fraction" placeholder="<?php echo JText::_('COM_YOORECIPE_QTY'); ?>"/>
        </div>
    </div>
    <?php 
                //die('<pre>'.print_r($this->units, true).'</pre>');
                //echo('<pre>'.print_r("OGO Sense Test", true).'</pre>');?>
    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('COM_YOORECIPE_INGREDIENTS_UNIT'); ?>
        </div>
        <div class="controls">
            <select name="ing_unit" id="ing_unit" >
                <?php
                foreach ($this->units as $unit) {
                    echo '<option value=\''.$unit->label.'\'>'.JText::_($unit->label).'</option>';
                } ?>
            </select>
        </div>
    </div>

    <div class="control-group">
        <div class="control-label">
            <?php echo JText::_('COM_YOORECIPE_INGREDIENTS_GROUP'); ?>
        </div>
        <div class="controls">
            <select name="ing_group_id" id="ing_group_id" class="required">
                <?php   
                if(empty($this->groups))
                    echo '<option value="0"> Recipe </option>';
                foreach($this->groups as $key => $group) {
                    echo '<option value="',$key,'">',JText::_($group->label),'</option>';
                } ?>
            </select>
        </div>
    </div>

    <button id="submit-form" class="btn btn-primary controls" onclick="insertIngredient();return false;"><?php echo JText::_('COM_YOORECIPE_INGREDIENTS_ADD'); ?></button>
</div>