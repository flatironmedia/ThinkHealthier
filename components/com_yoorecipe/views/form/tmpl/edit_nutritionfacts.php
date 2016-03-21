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

// Get config parameters
$params 				= JComponentHelper::getParams('com_yoorecipe');
$use_recipe_settings 	= $params->get('use_recipe_settings', 1);

$use_nutrition_facts	= $params->get('use_nutrition_facts', 1);

if($use_nutrition_facts) {
	
	$nutrition_facts = array();
	
	$show_kjoule 			= $use_recipe_settings ? $params->get('show_kjoule', 1) : $params->get('show_kjoule_fe', 1);
	$show_kcal	 			= $use_recipe_settings ? $params->get('show_kcal', 1) : $params->get('show_kcal_fe', 1);
	$show_sugar				= $use_recipe_settings ? $params->get('show_sugar', 1) : $params->get('show_sugar_fe', 1);
	$show_carbs				= $use_recipe_settings ? $params->get('show_carbs', 1) : $params->get('show_carbs_fe', 1);
	$show_fat	 			= $use_recipe_settings ? $params->get('show_fat', 1) : $params->get('show_fat_fe', 1);
	$show_sfat	 			= $use_recipe_settings ? $params->get('show_sfat', 1) : $params->get('show_sfat_fe', 1);
	$show_cholesterol		= $use_recipe_settings ? $params->get('show_cholesterol', 1) : $params->get('show_cholesterol_fe', 1);
	$show_proteins	 		= $use_recipe_settings ? $params->get('show_proteins', 1) : $params->get('show_proteins_fe', 1);
	$show_fibers	 		= $use_recipe_settings ? $params->get('show_fibers', 1) : $params->get('show_fibers_fe', 1);
	$show_salt	 			= $use_recipe_settings ? $params->get('show_salt', 1) : $params->get('show_salt_fe', 1);
	
	$show_kjoule ? 		$nutrition_facts[] = $this->form->renderField('kjoule') : '';
	$show_kcal ? 		$nutrition_facts[] = $this->form->renderField('kcal') : '';
	$show_sugar ? 		$nutrition_facts[] = $this->form->renderField('sugar') : '';
	$show_carbs ? 		$nutrition_facts[] = $this->form->renderField('carbs') : '';
	$show_fat ? 		$nutrition_facts[] = $this->form->renderField('fat') : '';
	$show_sfat ? 		$nutrition_facts[] = $this->form->renderField('saturated_fat') : '';
	$show_cholesterol ? $nutrition_facts[] = $this->form->renderField('cholesterol') : '';
	$show_proteins ? 	$nutrition_facts[] = $this->form->renderField('proteins') : '';
	$show_fibers ? 		$nutrition_facts[] = $this->form->renderField('fibers') : '';
	$show_salt ? 		$nutrition_facts[] = $this->form->renderField('salt') : '';
}
?>
<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_NUTRITION_FACTS'); ?></h2>
<div class="row-fluid">
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('serving_size'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('serving_size'); ?>
	</div>
</div>
	
<?php 
	$i = 0;
	foreach($nutrition_facts as $i => $nutrition_fact) {
	
		if($i % 2 == 0){
			echo '</div><div class="row-fluid">';
		}
		
		echo '<div class="span6">';
		echo $nutrition_fact;
		echo '</div>';
	
	}
?>
</div>