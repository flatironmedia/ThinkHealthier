<?php
/*----------------------------------------------------------------------
# YooRock! YooRecipe Module 1.0.0
# ----------------------------------------------------------------------
# Copyright (C) 2011 YooRock. All Rights Reserved.
# Coded by: YooRock!
# License: GNU GPL v2
# Website: http://www.yoorecipe.com
------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.');
 
// include the helper file
require_once dirname(__FILE__).'/helper.php';
require_once JPATH_ROOT.'/components/com_yoorecipe/helpers/html/yoorecipeutils.php';
require_once JPATH_ROOT.'/administrator/components/com_yoorecipe/helpers/html/datetimeutils.php';
require_once JPATH_ROOT.'/administrator/components/com_yoorecipe/models/komento.php';
require_once JPATH_ROOT.'/components/com_yoorecipe/helpers/html/yoorecipehelperroute.php';
require_once JPATH_SITE.'/components/com_yoorecipe/helpers/html/imageutils.php';
require_once JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/imagecropperutils.php';
 
// Load com_yoorecipe language file
$lang = JFactory::getLanguage();
$lang->load('com_yoorecipe', JPATH_ADMINISTRATOR, $lang->getTag(), true);

// Get the items to display from the helper
$items = ModYooRecipeHelper::getRecipes($params);

// Take care of ratings origin
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
$rating_origin	 	= $yooRecipeparams->get('rating_origin', 'yoorecipe');

if ($rating_origin == 'komento') {
	
	$komentoModel = JModelLegacy::getInstance('komento', 'YooRecipeModel');
	foreach ($items as $item) {
		$item->note = round($komentoModel->getRecipeNote($item->id), 1);
	}
}

// Get ingredients if needed
if ($params->get('show_ingredients', 1)) {
	foreach ($items as $item) {
		$item->groups = ModYooRecipeHelper::getIngredientsGroupsByRecipeId($item->id);
		foreach ($item->groups as $group) {
			$group->ingredients = ModYooRecipeHelper::getIngredientsByGroupId($group->id);
		}
	}
}

//--- Xander@OGOSense Recipe image batch modification ---//
foreach ($items as $item) {
	if(!$item->picture){
		if(!JFile::exists('/images/com_yoorecipe/recipes/recipe'.$item->id.'.jpg')){
			$item->picture = 'images/com_yoorecipe/recipes/recipe'.$item->id.'.jpg';
		}
	}
}

// include the template for display
$layout = $params->get('display', 'accordion');
require(JModuleHelper::getLayoutPath('mod_yoorecipe', $layout));