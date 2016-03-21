<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2012 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
 /**
 * YooRecipes View
 */
class YooRecipeViewServingTypes extends JViewLegacy
{
	/**
	 * YooRecipes view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Check data is installed
		// if (!JHtml::_('yoorecipedatautils.checkIngredientsServingTypesInitialized')) { // TODO remanier
			// JError::raiseError(500, 'INGREDIENT UNITS NOT INITIALIZED');
			// return false;
		// }
		
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
		YooRecipeHelper::addSubmenu('servingtypes');
		
		// Set the toolbar
		$this->addToolBar();
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
		
		JToolBarHelper::title(JText::_('COM_YOORECIPE_MANAGER_YOORECIPE_SERVING_TYPES'), 'yoorecipe');
		
		$canCreate 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.create', 'com_yoorecipe');
		if ($canCreate) {
			JToolBarHelper::addNew('servingtype.add');
		}
		
		if (count($this->items) > 0) {
			
			$canEdit 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
			$canDelete 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.delete', 'com_yoorecipe');
			$canEditState	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit.state', 'com_yoorecipe');
			
			if ($canEdit) {
				JToolBarHelper::editList('servingtype.edit');
				JToolBarHelper::divider();
			}
			
			if ($canEditState) {
				JToolBarHelper::publish('servingtypes.publish');
				JToolBarHelper::unpublish('servingtypes.unpublish');
				JToolBarHelper::divider();
			}
			
			if ($canDelete) {
				JToolBarHelper::deleteList('', 'servingtypes.delete');
			}
		}
		JToolBarHelper::preferences('com_yoorecipe');
		
		JHtmlSidebar::setAction('index.php?option=com_yoorecipe&view=servingtypes');
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_UNIT_CODE'),
			'filter_code',
			JHtml::_('select.options', JHtml::_('optionsutils.servingtypesOptions'), 'value', 'text', $this->state->get('filter.code'))
		);
		
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
		$document->setTitle(JText::_('COM_YOORECIPE_ADMINISTRATION'));
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
			'u.id' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_ID'),
			'u.lang' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_LANG'),
			'u.code' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CODE'),
			'u.label' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_LABEL'),
			'u.published' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PUBLISHED'),
			'u.creation_date' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CREATION_DATE'),
		);
	}
}