<?php
/**
 * @version		$Id: view.html.php 21023 2011-03-28 10:55:01Z infograf768 $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML Article View class for the Content component
 *
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since		1.5
 */
class YooRecipeViewForm extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null) 
	{
		// Initialise variables.
		$input 				= JFactory::getApplication()->input;
		$user				= JFactory::getUser();
		$recipe_id 			= $input->get('id', 0, 'INT');
		$is_admin			= $user->authorise('core.admin', 'com_yoorecipe');
		$filter_languages 	= $is_admin ? false : true;
		
		// Get models
		$crossCategoryModel		= JModelLegacy::getInstance('crosscategory','YooRecipeModel');
		$mainModel				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons','YooRecipeModel');
		$ingredientsModel		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$ingredientsGroupsModel	= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		
		// Get data
		$form 				= $this->get('Form');
		$item 				= $mainModel->getItem($recipe_id);
		$item->category_id 	= $crossCategoryModel->getRecipeCategoriesIds($item->id);	
		$item->seasons	 	= $seasonsModel->getRecipeSeasonsIds($item->id);
		
		// Calculate authorisations
		if (empty($item->id)) {
			$authorised = $user->authorise('core.create', 'com_yoorecipe');
		}
		else {
			$authorised = $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe') || ($user->authorise('core.edit.own', 'com_yoorecipe') && $item->created_by == $user->id);
		}

		if ($authorised !== true) {
		
			if ($user->guest == 1) {
				$app = JFactory::getApplication();
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()),false));
				return;
			}
			
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
 
		// Authorizations ok, let's retrieve ingredients
		if (!empty($item->id)) {
		
			$item->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($item->id);
			foreach ($item->groups as $group) {
				$group->ingredients = $ingredientsModel->getIngredientsByGroupId($group->id);
			}
			$form->bind($item);
		}
		
		$item->tags = new JHelperTags;
		$item->tags->getItemTags('com_yoorecipe.recipe' , $item->id);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Assign the Data
		$this->form 		= $form;
		$this->item 		= $item;
		
		// Prepare document
		$this->_prepareDocument();

		// Display the template
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

		$document = JFactory::getDocument();
		
		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu 		= $menus->getActive();
		if ($this->item) {
			// User comes from recipe page: he is editing
			$this->document->setTitle($this->item->title.' - '.JText::_('COM_YOORECIPE_EDITION'));
			$this->document->setDescription(strip_tags($this->item->description));
		} 
		else if ($menu)	{

			// User comes from menu
			$menuParams = $menu->params;
			$menuParams->def('page_heading', $menuParams->get('page_title', $menu->title));
			$title = $menuParams->get('page_title', '');
			if (!empty($title)) {
				$this->document->setTitle($title);
			}
			
			if ($menuParams->get('menu-meta_description')){
				$this->document->setDescription($menuParams->get('menu-meta_description'));
			}

			if ($menuParams->get('menu-meta_keywords')) {
				$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
			}
			
			if ($menuParams->get('robots')) {
				$this->document->setMetadata('robots', $menuParams->get('robots'));
			}
		}
		
		JText::script('COM_YOORECIPE_FORM_VALIDATION_FAILED');
		JText::script('COM_YOORECIPE_INGREDIENT_GROUP');
		JText::script('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER');
		JText::script('COM_YOORECIPE_QUANTITY_PLACEHOLDER');
		JText::script('COM_YOORECIPE_UNIT_PLACEHOLDER');
		JText::script('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER');
		JText::script('COM_YOORECIPE_FRACTION');
		JText::script('COM_YOORECIPE_INGREDIENTS_ADD');
	}
}