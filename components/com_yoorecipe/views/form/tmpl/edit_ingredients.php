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
		<div id="ingredient_group_container" class="row-fluid sortable">
<?php
			// Loop over ingredients
			if (isset($recipe->groups)) { 
				
				foreach ($recipe->groups as $group_index => $group) {

					$html = array();
					$html[] = '<div class="ingredient_group" name="ingredient_group_'.$group_index.'" data-index="'.$group_index.'" data-group-id="'.$group->id.'">';

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
					$html[] = '<table class="table table-striped">';
					
					$html[] = '<tfoot>';
					$html[] = '<tr class="add-ingredient">';
					$html[] = '<td><input type="text" class="input-mini" name="add_quantity_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_QUANTITY_PLACEHOLDER').'" value=""/></td>';
					$html[] = '<td><input type="text" class="input-medium" name="add_unit_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_UNIT_PLACEHOLDER').'" value=""/></td>';
					$html[] = '<td><input type="text" class="input-medium" name="add_description_'.$group_index.'" placeholder="'.JText::_('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER').'" value="" onblur="recipe.addIngredientToGroup('.$group_index.')"/></td>';
					$html[] = '<td><button type="button" class="btn" onclick="recipe.addIngredientToGroup('.$group_index.')">'.JText::_('COM_YOORECIPE_INGREDIENTS_ADD').'</button></td>';
					$html[] = '</tr>';
					$html[] = '</tfoot>';
					
					$html[] = '<tbody class="sortable">';
					foreach ($group->ingredients as $ing_index => $ingredient) {
					
						$quantity = ($use_fractions) ? JHTMLIngredientUtils::decimalToFraction(round($ingredient->quantity, 2)) : $ingredient->quantity;
						$quantity_string = ($quantity == 0) ? '' : $quantity;
					
						$html[] = '<tr class="sortable-row" name="ingredient_'.$ing_index.'" data-ingredient-id="'.$ingredient->id.'">';
						$html[] = '<td><input type="text" class="input-mini validate-fraction" name="quantity[]" placeholder="'.JText::_('COM_YOORECIPE_QUANTITY_PLACEHOLDER').'" value="'.$quantity_string.'"/></td>';
						$html[] = '<td><input type="text" class="input-medium" name="unit[]" placeholder="'.JText::_('COM_YOORECIPE_UNIT_PLACEHOLDER').'" value="'.$ingredient->unit.'"/></td>';
						$html[] = '<td><input type="text" class="input-medium required" name="description[]" placeholder="'.JText::_('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER').'" value="'.$ingredient->description.'"/>';
						$html[] = '<input type="hidden" name="ingredient_id[]" value="'.$ingredient->id.'"/>';
						$html[] = '<input type="hidden" name="group_index[]" value="'.$group_index.'"/>';
						$html[] = '<input type="hidden" name="ingredient_action[]" value="U"/>';
						$html[] = '</td><td>';
						$html[] = '<button type="button" class="btn remove-ingredient" onclick="recipe.removeIngredient('.$group_index.','.$ing_index.')"><i class="icon-trash"></i> </button>';
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
?>
		</div>
		<input type="text" placeholder="<?php echo JText::_('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER'); ?>" id="add_ingredient_group" name="add_ingredient_group" class="input"/>
		<button type="button" class="btn add-ingredient-group"><?php echo JText::_('COM_YOORECIPE_ADD_INGREDIENT_GROUP'); ?></button>
	</div>
</div>

<input type="hidden" name="nb_ingredients" id="nb_ingredients" class="btn validate-ingredients" value="<?php echo $nb_ingredients; ?>"/>