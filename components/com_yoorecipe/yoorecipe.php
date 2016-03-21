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

if(!defined('DS')){
    define('DS',DIRECTORY_SEPARATOR);
}

// Require helpers files
JLoader::register('FinderIndexerStemmer', JPATH_ADMINISTRATOR.'/components/com_finder/helpers/indexer/stemmer.php');
JLoader::register('JHtmlIngredientUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/ingredientutils.php');
JLoader::register('JHtmlYooRecipeAdminUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/yoorecipeadminutils.php');
JLoader::register('JHtmlImageCropperUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/imagecropperutils.php');
JLoader::register('JHtmlDateTimeUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/datetimeutils.php');
JLoader::register('JHtmlEmailUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/emailutils.php');
JLoader::register('JHtmlRecipeUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/recipeutils.php');
JLoader::register('JHtmlOptionsUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/optionsutils.php');
JLoader::register('JHtmlLangUtils', JPATH_ADMINISTRATOR.'/components/com_yoorecipe/helpers/html/langutils.php');
JLoader::register('JHtmlYooRecipeHelperRoute', dirname(__FILE__) .'/helpers/html/yoorecipehelperroute.php');
JLoader::register('JHtmlYooRecipeIcon', dirname(__FILE__) .'/helpers/html/yoorecipeicon.php');
JLoader::register('JHtmlYooRecipePagination', dirname(__FILE__).'/helpers/html/yoorecipepagination.php');
JLoader::register('JHtmlYooRecipeUtils',  dirname(__FILE__).'/helpers/html/yoorecipeutils.php');
JLoader::register('JHtmlShoppingListUtils',  dirname(__FILE__).'/helpers/html/shoppinglistutils.php');
JLoader::register('JHtmlMealsUtils',  dirname(__FILE__).'/helpers/html/mealsutils.php');
JLoader::register('JHtmlImageUtils',  dirname(__FILE__).'/helpers/html/imageutils.php');

// Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_yoorecipe', JPATH_ADMINISTRATOR, $lang->getTag(), true);
$lang->load('', JPATH_ADMINISTRATOR, $lang->getTag(), true);

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by YooRecipe
$controller = JControllerLegacy::getInstance('YooRecipe');
$controller->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');
 
// Load models and fields
JForm::addFormPath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/forms');
JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_yoorecipe/models/fields');

// Perform the Request task
$input 	= JFactory::getApplication()->input;
$controller->execute($input->get('task', '', 'STRING'));
 
// Redirect if set by the controller
$controller->redirect();