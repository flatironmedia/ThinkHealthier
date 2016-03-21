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
 * YooRecipes View
 */
class YooRecipeViewComments extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;
	
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
		YooRecipeHelper::addSubmenu('comments');
		
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
		JToolBarHelper::title(JText::_('COM_YOORECIPE_MANAGER_YOORECIPES'), 'yoorecipe');
		
		if (count($this->items) > 0) {
			
			$canEdit 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
			$canDelete 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.delete', 'com_yoorecipe');
			$canEditState	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit.state', 'com_yoorecipe');
			
			if ($canEdit) {
				JToolBarHelper::editList('comment.edit');
				JToolBarHelper::divider();
			}
			
			if ($canEditState) {
				JToolBarHelper::publish('comments.publish');
				JToolBarHelper::unpublish('comments.unpublish');
				JToolBarHelper::divider();
			}
			
			if ($canDelete) {
				JToolBarHelper::deleteList('', 'comments.delete');
			}
		}
		JToolBarHelper::preferences('com_yoorecipe');
		
		
		JHtmlSidebar::setAction('index.php?option=com_yoorecipe&view=comments');
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_yoorecipe'), 'value', 'text', $this->state->get('filter.category_id'))
			);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_RECIPE'),
			'filter_recipe_id',
			JHtml::_('select.options', JHtml::_('optionsutils.recipeOptions', $this->state), 'value', 'text', $this->state->get('filter.recipe_id'))
		);
			
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('optionsutils.publishedOptions'), 'value', 'text', $this->state->get('filter.published'))
		);	
			
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_OFFENSIVE'),
			'filter_offensive',
			JHtml::_('select.options', JHtml::_('optionsutils.offensiveOptions'), 'value', 'text', $this->state->get('filter.offensive'))
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
			'rat.id' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_ID'),
			'rat.comment' => JText::_('COM_YOORECIPE_REVIEWS_HEADING_COMMENT'),
			'r.title' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_TITLE'),
			'rat.note' => JText::_('COM_YOORECIPE_REVIEWS_HEADING_NOTE'),
			'rat.author' => JText::_('COM_YOORECIPE_REVIEWS_HEADING_AUTHOR'),
			'rat.email' => JText::_('COM_YOORECIPE_REVIEWS_HEADING_EMAIL'),
			'rat.published' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PUBLISHED'),
			'rat.abuse' => JText::_('COM_YOORECIPE_REVIEWS_HEADING_OFFENSIVE'),
			'rat.creation_date' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CREATION_DATE'),
		);
	}
}