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
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the YooRecipe Component
 */
class YooRecipeViewShoppingLists extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		// Get component options and variables
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$user = JFactory::getUser();
		
		if ($user->guest == 1) {
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()),false));
			return;
		}
	
		// Init variables
		$app		= JFactory::getApplication();
		$menu 		= $app->getMenu();
		$active 	= $menu->getActive();
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : new JRegistry();
		
		// Breadcrumbs
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS'), JUri::current());
			
		// Prepare document
		$this->_prepareDocument();
		
		$shoppingListsModel = JModelLegacy::getInstance('shoppinglists','YooRecipeModel');
		$items 				= $shoppingListsModel->getShoppingListsByUserId($user->id);
		
		// Assign data to view
		$this->items = $items;
		
		// Display the view
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu 		= $menus->getActive();
		if ($menu)
		{
			$menuParams = $menu->params;
			$menuParams->def('page_heading', $menuParams->get('page_title', $menu->title));
			$title = $menuParams->get('page_title', '');
			// Check for empty title and add site name if param is set
			if (empty($title))
			{
				$title = $app->getCfg('sitename');
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
			{
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}

			if (empty($title))
			{
				$title = $app->getCfg('sitename');
			}
			$this->document->setTitle($title);
			
			if ($menuParams->get('menu-meta_description')) {
				$this->document->setDescription($menuParams->get('menu-meta_description'));
			}

			if ($menuParams->get('menu-meta_keywords')) {
				$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
			}
			
			if ($menuParams->get('robots')) {
				$this->document->setMetadata('robots', $menuParams->get('robots'));
			}
			
			//Escape strings for HTML output
			$this->pageclass_sfx = htmlspecialchars($menuParams->get('pageclass_sfx'));
		}
		else {
			$this->document->setTitle(JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS'));
			$this->pageclass_sfx = '';
		}
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
	}
}