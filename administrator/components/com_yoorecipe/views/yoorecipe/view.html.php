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
 * YooRecipe View
 */
class YooRecipeViewYooRecipe extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var		form
	 */
	protected $form = null;
	protected $ingredients = null;
	
	/**
	 * display method of YooRecipe view
	 * @return void
	 */
	public function display($tpl = null) 
	{
		// Get the Data
		$form 	= $this->get('Form');
		$item 	= $this->get('Item');
		$script	= $this->get('Script');
		$units	= $this->get('Units');
		
		// Get Models
		$mainModel				= $this->getModel('yoorecipe');
		$ingredientsModel		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$ingredientsGroupsModel	= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');

		// Get the data
		if($item->id)
			$groups = $mainModel->getGroups($item->id);


		
		// Get ingredients
		if (isset($item->id)) {
			$item->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($item->id);
			foreach ($item->groups as $group) {
				$group->ingredients 	= $ingredientsModel->getIngredientsByGroupId($group->id);
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Assign the Data
		$this->form 		= $form;
		$this->item 		= $item;
		$this->script 		= $script;
		$this->units		= $units;
		$this->groups		= $item->groups;
		
		// Set the toolbar
		$this->addToolBar();
 
		// Display the template
		parent::display($tpl);
 
		// Set the document
		$this->setDocument();
	}
 
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
		$input 	= JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
		
		JToolBarHelper::title($isNew ? JText::_('COM_YOORECIPE_MANAGER_YOORECIPE_NEW') : JText::_('COM_YOORECIPE_MANAGER_YOORECIPE_EDIT'), 'yoorecipe');
		
		JToolBarHelper::apply('yoorecipe.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('yoorecipe.save');
		JToolBarHelper::custom('yoorecipe.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::custom('yoorecipe.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		JToolBarHelper::cancel('yoorecipe.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
	}
	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$isNew = ($this->item->id < 1);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_YOORECIPE_YOORECIPE_CREATING') : JText::_('COM_YOORECIPE_YOORECIPE_EDITING'));
		
		JText::script('COM_YOORECIPE_FORM_VALIDATION_FAILED');
		JText::script('COM_YOORECIPE_INGREDIENT_GROUP');
		JText::script('COM_YOORECIPE_GROUP_NAME_PLACEHOLDER');
		JText::script('COM_YOORECIPE_QUANTITY_PLACEHOLDER');
		JText::script('COM_YOORECIPE_UNIT_PLACEHOLDER');
		JText::script('COM_YOORECIPE_DESCRIPTION_PLACEHOLDER');
		JText::script('COM_YOORECIPE_FRACTION');
		JText::script('COM_YOORECIPE_INGREDIENTS_ADD');
		
		$document->addScript(JURI::root()."administrator/components/com_yoorecipe/views/yoorecipe/submitbutton.js");
	}
}