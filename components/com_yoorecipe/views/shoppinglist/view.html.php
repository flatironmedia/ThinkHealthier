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
class YooRecipeViewShoppingList extends JViewLegacy
{
	
	protected $form;
	protected $item;
	
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		
		$user = JFactory::getUser();
		
		if ($user->guest == 1) {
			$app = JFactory::getApplication();
			$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()),false));
			return;
		}
		
		// Initialise variables.
		$input 				= JFactory::getApplication()->input;
		$shoppingListModel	= JModelLegacy::getInstance('shoppinglist','YooRecipeModel');
		
		$form 		= $this->get('Form');
		$id 		= $input->get('id', 0, 'INT');
		$item 		= $shoppingListModel->getShoppingListById($id, $get_details = true);
		$form->bind($item);

		// Assign the Data
		$this->form 	= $form;
		$this->item 	= $item;
		
		// Breadcrumbs
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		$pathway->addItem(JText::_('COM_YOORECIPE_YOUR_SHOPPINGLISTS'), JRoute::_('index.php?option=com_yoorecipe&view=shoppinglists', true));
		if (isset($item)) {
			$pathway->addItem($item->title, JUri::current());
		}
		
		// Prepare document
		$this->_prepareDocument();
		
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
			
			if ($menuParams->get('menu-meta_description')) {
				$this->document->setDescription($menuParams->get('menu-meta_description'));
			}

			if ($menuParams->get('menu-meta_keywords')) {
				$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
			}
			
			if ($menuParams->get('robots')) {
				$this->document->setMetadata('robots', $menuParams->get('robots'));
			}
		}
		
		$this->document->setTitle(JText::sprintf('COM_YOORECIPE_SHOPPINGLIST_TITLE', $this->item->title));
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
	}
}