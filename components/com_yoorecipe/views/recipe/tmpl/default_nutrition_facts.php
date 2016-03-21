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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$recipe 	= $this->recipe;
if ($recipe->kjoule == 0 && $recipe->kcal == 0 && $recipe->sugar == 0 && $recipe->carbs == 0 && $recipe->proteins == 0 && $recipe->fat == 0 && $recipe->saturated_fat == 0 &&  $recipe->cholesterol == 0 &&
		$recipe->fibers == 0 && $recipe->salt == 0 && !$recipe->diet && !$recipe->veggie && !$recipe->gluten_free && !$recipe->lactose_free) {
	echo '';
} else {

	$html = array();
	$html[] = '<h3>'.JText::_('COM_YOORECIPE_RECIPES_NUTRITION_FACTS').'</h3>';

	$joules			= $recipe->kjoule == 0 ? 		'' : $recipe->kjoule.' '.JText::_('COM_YOORECIPE_YOORECIPE_KJOULE_LABEL');
	$calories		= $recipe->kcal == 0 ? 			'' : $recipe->kcal.' '.JText::_('COM_YOORECIPE_CALORIES');
	$sugar			= $recipe->sugar == 0 ? 		'' : $recipe->sugar.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$carbs			= $recipe->carbs == 0 ? 		'' : $recipe->carbs.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$proteins		= $recipe->proteins == 0 ? 		'' : $recipe->proteins.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$fat 			= $recipe->fat == 0 ? 			'' : $recipe->fat.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$sfat			= $recipe->saturated_fat == 0 ? '' : $recipe->saturated_fat.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$cholesterol	= $recipe->cholesterol == 0 ? 	'' : $recipe->cholesterol.JText::_('COM_YOORECIPE_MILLIGRAMS_SYMBOL');
	$fibers			= $recipe->fibers == 0 ? 		'' : $recipe->fibers.JText::_('COM_YOORECIPE_GRAMS_SYMBOL');
	$salt			= $recipe->salt == 0 ? 			'' : $recipe->salt.JText::_('COM_YOORECIPE_MILLIGRAMS_SYMBOL');

	$recipe_flags = array();
	if ($recipe->diet) {
		$recipe_flags[] = JText::_('COM_YOORECIPE_TYPE_DIET');
	}
	if ($recipe->veggie) {
		$recipe_flags[] = JText::_('COM_YOORECIPE_TYPE_VEGGIE');
	}
	if ($recipe->gluten_free) {
		$recipe_flags[] = strtolower(JText::_('COM_YOORECIPE_YOORECIPE_GLUTEN_FREE_LABEL'));
	}
	if ($recipe->lactose_free) {
		$recipe_flags[] = strtolower(JText::_('COM_YOORECIPE_YOORECIPE_LACTOSE_FREE_LABEL'));
	}

	if (sizeof($recipe_flags) > 0) {
		$html[] = '<div>';
		$html[] = '('.implode(", ",$recipe_flags).')';
		$html[] = '</div>';
	}

	$html[] = '<div itemprop="nutrition" itemscope itemtype="http://schema.org/NutritionInformation">';
	if (isset($recipe->serving_size)) {
		$html[] = '<div itemprop="servingSize">'.JText::sprintf('COM_YOORECIPE_NUTRITION_FACTS_SERVING_SIZE_IS', $recipe->serving_size).'</div>';
	}
	
	$html[] = '<div>'.JText::_('COM_YOORECIPE_NUTRITION_FACTS_AMOUNT_PER_SERVING').'</div>';
	$html[] = '<div class="row-fluid">';
	$html[] = '<div class="span6">';
	$html[] = '<ul>';
	if ($calories != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_KCAL_LABEL').': <span itemprop="calories">'. $calories.'</span></li>';
	}
	if ($joules != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_KJOULE_LABEL').': '. $joules.'</li>';
	}
	if ($fibers != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_FIBERS_LABEL').': <span itemprop="fiberContent">'. $fibers.'</span></li>';
	}
	if ($salt != '') {	
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_SALT_LABEL').': <span itemprop="sodiumContent">'. $salt.'</span></li>';
	}
	$html[] = '</ul>';
	$html[] = '</div>';

	$html[] = '<div class="span6">';
	$html[] = '<ul>';
	if ($carbs != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_CARBS_LABEL').': <span itemprop="carbohydrateContent">'. $carbs.'</span></li>';
	}
	if ($sugar != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_SUGAR_LABEL').': <span itemprop="sugarContent">'. $sugar.'</span></li>';
	}
	if ($fat != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_FAT_LABEL').': <span itemprop="fatContent">'. $fat.'</span></li>';
	}
	if ($sfat != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_SATURATED_FAT_LABEL').': <span itemprop="saturatedFatContent">'. $sfat.'</span></li>';
	}
	if ($cholesterol != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_CHOLESTEROL_LABEL').': <span itemprop="cholesterolContent">'. $cholesterol.'</span></li>';
	}
	if ($proteins != '') {
		$html[] = '<li>'.JText::_('COM_YOORECIPE_YOORECIPE_PROTEINS_LABEL').': <span itemprop="proteinContent">'. $proteins.'</span></li>';
	}
	$html[] = '</ul>';
	$html[] = '</div>';
	
	$html[] = '</div>';
	$html[] = '</div>';

	echo implode("\n", $html);
}