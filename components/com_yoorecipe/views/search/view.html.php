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
class YooRecipeViewSearch extends JViewLegacy
{
	function display($tpl = null) 
	{
		$app	= JFactory::getApplication();
		$input 	= $app->input;
		$menu	= $app->getMenu();
		$active = $menu->getActive();
		
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		
		$mainModel					= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$categoriesModel 			= JModelLegacy::getInstance('categories','YooRecipeModel');
		$seasonsModel 				= JModelLegacy::getInstance('seasons','YooRecipeModel');
		$mealPlannerQueueModel 		= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		$ingredientsModel			= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$ingredientsGroupsModel		= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		
		// Get data from form
		$this->searchword 			= $input->get('searchword','', 'STRING');
		$this->withIngredients 		= $input->get('withIngredients', array(), 'ARRAY');
		$this->withoutIngredients 	= $input->get('withoutIngredients', array(), 'ARRAY');
		$this->searchCategories 	= $input->get('searchCategories', array(), 'ARRAY');
		$this->searchSeasons	 	= $input->get('searchSeasons', '', 'STRING');
		$this->searchPerformed 		= $input->get('searchPerformed', 0, 'INT');
		
		$search_author				= $input->get('search_author', '', 'STRING');
		$search_max_prep_hours		= $input->get('search_max_prep_hours', 0, 'INT');
		$search_max_prep_minutes	= $input->get('search_max_prep_minutes', 0, 'INT');
		$search_max_cook_hours		= $input->get('search_max_cook_hours', 0, 'INT');
		$search_max_cook_minutes	= $input->get('search_max_cook_minutes', 0, 'INT');
		$search_min_rate			= $input->get('search_min_rate', 0, 'INT');
		
		$search_kcal		= $input->get('search_kcal', '', 'STRING');
		$search_carbs		= $input->get('search_carbs', '', 'STRING');
		$search_fat			= $input->get('search_fat', '', 'STRING');
		$search_proteins	= $input->get('search_proteins', '', 'STRING');
				
		// Prepare fields to return in view
		$this->error 	= null;
		$this->items	= null;
		$this->state	= $this->get('state');
		$this->user		= JFactory::getUser();
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : new JRegistry();
		
		// Get yoorecipe model
		$model = $this->getModel('search');

		if ($this->getLayout() == 'search')	{
			$this->categories = $categoriesModel->getAllPublishedCategories();
		}
		
		// Get all recipe authors
		$this->authors = $model->getAuthors();
		$this->pagination = $this->get('Pagination');
		
		if ($this->getLayout() == 'results') {
			
			// $exactMatches 	= $this->get('ExactMatchItems');
			// $anyMatches		= $this->get('AnyMatchItems');
			// $this->items	= array_merge($exactMatches, $anyMatches);
			// $this->items	= array_unique($this->items, SORT_REGULAR);
			
			$this->items = $this->get('Items');
			
			foreach ($this->items as $recipe)
			{	
				$recipe->groups			= $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($recipe->id);
				foreach ($recipe->groups as $group) {
					$group->ingredients 	= $ingredientsModel->getIngredientsByGroupId($group->id);
				}
				
				$recipe->seasons	 	= $seasonsModel->getRecipeSeasonsIds($recipe->id);
				$recipe->categories 	= $categoriesModel->getRecipeCategories($recipe->id);
				$recipe->is_queued		= $mealPlannerQueueModel->isRecipeQueued($recipe->id, $this->user->id);
				
				// Get tags
				$recipe->tags = new JHelperTags;
				$recipe->tags->getItemTags('com_yoorecipe.recipe' , $recipe->id);
				
				// Calculate authorisations
				$recipe->canEdit		= $this->user->authorise('core.admin', 'com_yoorecipe') || ($this->user->guest != 1 && ($this->user->authorise('core.edit', 'com_yoorecipe') || ($this->user->authorise('core.edit.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id)));
				$recipe->canDelete	 	= $this->user->authorise('core.admin', 'com_yoorecipe') || ($this->user->guest != 1 && ($this->user->authorise('core.delete', 'com_yoorecipe') || ($this->user->authorise('core.delete.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id)));
			}
			
			if (count($this->items) == 1) 
			{
				$app = JFactory::getApplication();
				$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $this->items[0]->slug, $this->items[0]->catslug) , false);
				$app->redirect($url);
			}
		}
		
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->menuParams->get('pageclass_sfx'));
		
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
		}
		
		JText::script('COM_YOORECIPE_CONFIRM_DELETE');
		JText::script('COM_YOORECIPE_CONFIRM_DELETE_INGREDIENT');
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
	}
}