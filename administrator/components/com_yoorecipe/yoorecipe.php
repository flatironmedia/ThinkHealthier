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
JLoader::register('YooRecipeHelper', dirname(__FILE__).'/helpers/yoorecipe.php');
JLoader::register('JHtmlImportUserDataUtils', dirname(__FILE__).'/helpers/html/importuserdatautils.php');
JLoader::register('JHtmlExportUserDataUtils', dirname(__FILE__).'/helpers/html/exportuserdatautils.php');
JLoader::register('JHtmlLangUtils', dirname(__FILE__).'/helpers/html/langutils.php');
JLoader::register('JHtmlIngredientUtils', dirname(__FILE__).'/helpers/html/ingredientutils.php');
JLoader::register('JHtmlYooCategory', dirname(__FILE__).'/helpers/html/yoocategory.php');
JLoader::register('JHtmlDateTimeUtils', dirname(__FILE__).'/helpers/html/datetimeutils.php');
JLoader::register('JHtmlOptionsUtils', dirname(__FILE__).'/helpers/html/optionsutils.php');
JLoader::register('JHtmlRecipeUtils', dirname(__FILE__).'/helpers/html/recipeutils.php');
JLoader::register('JHtmlEmailUtils', dirname(__FILE__).'/helpers/html/emailutils.php');
JLoader::register('JHtmlYooRecipeAdminUtils', dirname(__FILE__).'/helpers/html/yoorecipeadminutils.php');
// JLoader::register('JHtmlYooRecipeDataUtils', dirname(__FILE__).'/helpers/html/datautils.php'); // TODO remanier
JLoader::register('JHtmlImageCropperUtils', dirname(__FILE__).'/helpers/html/imagecropperutils.php');
JLoader::register('JHtmlYooRecipeImpexUtils', dirname(__FILE__).'/helpers/html/impexutils.php');
JLoader::register('JHtmlYooRecipeUtils', JPATH_SITE.'/components/com_yoorecipe/helpers/html/yoorecipeutils.php');

JLoader::register('ImportModeEnum', dirname(__FILE__).'/helpers/enums/importmodeenum.php');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_yoorecipe')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Set some global property
$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-48-yoorecipe {background-image: url(../media/com_yoorecipe/images/tux-48x48.png);}');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by YooRecipe
$controller = JControllerLegacy::getInstance('YooRecipe');

// Perform the Request task
$input 	= JFactory::getApplication()->input;
$controller->execute($input->get('task', '', 'STRING'));
 
// Redirect if set by the controller
$controller->redirect();