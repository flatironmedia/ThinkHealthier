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
class YooRecipeViewYooRecipes extends JViewLegacy
{
	/**
	 * YooRecipes view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
		// Get recipe model
		$modelYooRecipe		= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$categoriesModel	= JModelLegacy::getInstance('categories','YooRecipeModel');
		
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
		
		// Get cross categories
		foreach ($this->items as $recipe ) :
			$recipe->categories = $categoriesModel->getRecipeCategories($recipe->id);
		endforeach;
 
		// Load the category helper.
		YooRecipeHelper::addSubmenu('recipes');
		
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
		// Init variables
		$user 		= JFactory::getUser();
		
		JToolBarHelper::title(JText::_('COM_YOORECIPE_MANAGER_YOORECIPES'), 'yoorecipe');
		
		JToolBarHelper::addNew('yoorecipe.add');
		if (count($this->items) > 0) {
			
			$canEdit 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
			$canDelete 		= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.delete', 'com_yoorecipe');
			$canEditState	= $user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit.state', 'com_yoorecipe');
			
			if ($canEdit) {
				JToolBarHelper::editList('yoorecipe.edit'); 
				JToolBarHelper::divider();
			}
			
			if ($canEditState) {

				JToolBarHelper::publish('yoorecipes.publish');
				JToolBarHelper::unpublish('yoorecipes.unpublish');
				JToolBarHelper::custom('yoorecipes.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);    
				JToolBarHelper::divider();
				JToolBarHelper::custom('yoorecipes.validate', 'publish.png', 'publish.png', 'COM_YOORECIPE_JLIB_HTML_VALIDATE_ITEMS', true);
				JToolBarHelper::divider();
				JToolbarHelper::checkin('yoorecipes.checkin');
			}
			
			if ($canDelete) {
				JToolBarHelper::deleteList('', 'yoorecipes.delete');
			}
		}
		JToolBarHelper::preferences('com_yoorecipe');
		
		JHtmlSidebar::setAction('index.php?option=com_yoorecipe&view=units');
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_yoorecipe'), 'value', 'text', $this->state->get('filter.category_id'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('optionsutils.publishedOptions'), 'value', 'text', $this->state->get('filter.published'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('COM_YOORECIPE_SELECT_VALIDATED'),
			'filter_validated',
			JHtml::_('select.options', JHtml::_('optionsutils.validatedOptions'), 'value', 'text', $this->state->get('filter.validated'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('COM_YOORECIPE_SELECT_AUTHOR'),
			'filter_created_by',
			JHtml::_('select.options', JHtml::_('optionsutils.createdByOptions'), 'value', 'text', $this->state->get('filter.created_by'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);
		
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_LANGUAGE'),
			'filter_language',
			JHtml::_('select.options', JHtml::_('optionsutils.installedLanguages'), 'value', 'text', $this->state->get('filter.language'))
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
			 'r.id' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_ID'),
			 'r.title' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_TITLE'),
			 'r.featured' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_FEATURED'), 
			 'c.title' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CATEGORY'), 
			 'access_level' => JText::_('JGRID_HEADING_ACCESS'),
			 'r.creation_date' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CREATION_DATE'), 
			 'author_name' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_CREATED_BY'), 
			 'r.difficulty' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_DIFFICULTY'), 
			 'r.cost' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_COST'), 
			 'r.preparation_time' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PREPARATION_TIME'), 
			 'r.cook_time ' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_COOK_TIME'),
			 'r.wait_time' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_WAIT_TIME'),
			 'r.published' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PUBLISHED'), 
			 'r.validated' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_VALIDATED'), 
			 'r.nb_views' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_NB_VIEWS'),
			 'r.note' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_GLOBAL_NOTE'), 
			 'r.picture' => JText::_('COM_YOORECIPE_YOORECIPE_HEADING_PICTURE'), 
			 'r.language' => JText::_('JGRID_HEADING_LANGUAGE'),
		);
	}

}