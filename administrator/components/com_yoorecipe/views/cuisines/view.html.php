<?php
/*------------------------------------------------------------------------
# com_yoorecipe - YooRecipe! Joomla 2.5 recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2013 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
 /**
 * YooRecipes Cuisines View
 */
class YooRecipeViewCuisines extends JViewLegacy
{
	/**
	 * YooRecipes view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get data from the model
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Load the category helper.
		YooRecipeHelper::addSubmenu('cuisines');
		
		// Set the toolbar
		$this->addToolBar();
		
		// Add sidebar
		$this->sidebar = JHtmlSidebar::render();
		
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
		$user = JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_YOORECIPE_CUISINES'));
		
		$canCreate 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.create', 'com_yoorecipe');
		if ($canCreate) {
			JToolBarHelper::addNew('cuisine.add');
		}
		
		if (count($this->items) > 0) {
			
			$canEdit 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
			$canDelete 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.delete', 'com_yoorecipe');
			$canEditState	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit.state', 'com_yoorecipe');
			
			if ($canEdit) {
				JToolBarHelper::editList('cuisine.edit');
				JToolBarHelper::divider();
			}
			
			if ($canEditState) {
				JToolBarHelper::publish('cuisines.publish');
				JToolBarHelper::unpublish('cuisines.unpublish');
				JToolBarHelper::divider();
			}
			
			if ($canDelete) {
				JToolBarHelper::deleteList('', 'cuisines.delete');
			}
		}
		JToolBarHelper::preferences('com_yoorecipe');
	
		JHtmlSidebar::setAction('index.php?option=com_yoorecipe&view=cuisines');
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('optionsutils.publishedOptions'), 'value', 'text', $this->state->get('filter.published'))
		);
	}
	
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_YOORECIPE_CUISINES'));
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'c.code' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CODE'),
			'c.published' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PUBLISHED'),
		);
	}
}