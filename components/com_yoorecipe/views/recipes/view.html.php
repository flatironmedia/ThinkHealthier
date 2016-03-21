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
class YooRecipeViewRecipes extends JViewLegacy
{

	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		$input 		= JFactory::getApplication()->input;
		$app		= JFactory::getApplication();
		$menu		= $app->getMenu();
		$active 	= $menu->getActive();
		
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$rating_origin		= $yooRecipeparams->get('rating_origin', 'yoorecipe');
		
		$user_id			= $input->get('id', '', 'STRING');
		$month_id 			= preg_split('/:/',$input->get('month_id', '', 'STRING'));
		$layout				= $input->get('layout', 'allrecipes', 'STRING');
		
		// Get the yoorecipe model
		$mainModel				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$reviewsModel			= JModelLegacy::getInstance('reviews', 'YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		$categoriesModel		= JModelLegacy::getInstance('categories','YooRecipeModel');
		$ingredientsModel		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$mealPlannerQueueModel 	= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		$ingredientsGroupsModel = JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		$komentoModel 			= JModelLegacy::getInstance('komento', 'YooRecipeModel');
		
		$state 		= $this->get('State');
		
		// Default case
		$state->set('filter.validated', 1);
		$state->set('filter.published', 1);
				
		$user = JFactory::getUser();
		switch ($layout) {
			
			case 'allrecipes':
			case 'archive':
				$state->set('list.limit', 0);
			break;
			
			case 'favourites':
				$state->set('filter.favourites', 1);
			break;
			
			case 'featured':
				$state->set('filter.featured', 1);
			break;
			
			case 'mostpopular':
				$state->set('filter.reviewed', 1);
				$state->set('orderCol', 'note');
				$state->set('drn', 'desc');
			break;
			
			case 'mostread':
				$state->set('orderCol', 'nb_views');
				$state->set('drn', 'desc');
			break;
			
			case 'mostreviewed':
				$state->set('filter.reviewed', 1);
				$state->set('orderCol', 'nb_reviews');
				$state->set('drn', 'desc');
			break;
			
			case 'mostrecents':
				$state->set('orderCol', 'creation_date');
				$state->set('drn', 'desc');
			break;
			
			case 'myrecipes':
			if ($user->guest == 1) {
					$app = JFactory::getApplication();
					$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()),false));
					return;
				}
				
				$state->set('filter.created_by', $user->id);
				$state->set('filter.validated', null);
				$state->set('filter.published', null);
			break;
			
			case 'seasons':
				$month_id 	= preg_split('/:/', $input->get('month_id', '', 'STRING'));
				
				$state->set('filter.season_id', $month_id[0]);
				$state->set('orderCol', 'title');
				$state->set('drn', 'asc');
			break;
			
			case 'chef':
				$user_id = $input->get('id', 0, 'INT');
				$state->set('filter.created_by', $user_id);
			break;
			
			case 'cuisine':
				$cuisine = $input->get('id', '', 'STRING');
				$state->set('filter.cuisine', $cuisine);
				
				$this->cuisine = $cuisine;
			break;
			
			case 'wall':
				$cuisine 	 	= $input->get('cuisine', '', 'STRING');
				$category_id 	= $input->get('category_id', 0, 'INT');
				$recipe_time 	= $input->get('recipe_time', 0, 'INT');
				$filter_search 	= $input->get('filter_search', '', 'STRING');
				$order_col 		= $input->get('order_col', '', 'STRING');
			
			
				$state->set('filter.cuisine', $cuisine);
				$state->set('filter.category_id', $category_id);
				$state->set('filter.recipe_time', $recipe_time);
				$state->set('filter.search', $filter_search);
				$state->set('filter.order_col', $order_col);
				
			 break;
		}
		
		// Assign all objects to the view
		$this->items 		= $this->get('Items');
		$this->pagination 	= $this->get('Pagination');
		$this->state 		= $state;
		
		$this->month_id		= $month_id[0];
		$this->id			= $user_id;
		
		// Get recipe's ingredients

		foreach ($this->items as $recipe)
		{
			$recipe->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($recipe->id);
			foreach ($recipe->groups as $group) {
				$group->ingredients = $ingredientsModel->getIngredientsByGroupId($group->id);
			}
			
			if ($rating_origin == 'komento') {
				$recipe->note = $komentoModel->getRecipeNote($recipe->id);
			}
			
			$recipe->ratings 		= $reviewsModel->getReviewsByRecipeIdOrderedByDateDesc($recipe->id, $published = null, $abuse = null);
			$recipe->seasons	 	= $seasonsModel->getRecipeSeasonsIds($recipe->id);
			$recipe->categories		= $categoriesModel->getRecipeCategories($recipe->id);
			$recipe->is_queued		= $mealPlannerQueueModel->isRecipeQueued($recipe->id, $user->id);
			
			// Get tags
			$recipe->tags = new JHelperTags;
			$recipe->tags->getItemTags('com_yoorecipe.recipe' , $recipe->id);
			
			// Calculate authorisations
			$recipe->canEdit		= $user->authorise('core.admin', 'com_yoorecipe') || ($user->guest != 1 && ($user->authorise('core.edit', 'com_yoorecipe') || ($user->authorise('core.edit.own', 'com_yoorecipe') && $recipe->created_by == $user->id)));
			$recipe->canDelete	 	= $user->authorise('core.admin', 'com_yoorecipe') || ($user->guest != 1 && ($user->authorise('core.delete', 'com_yoorecipe') || ($user->authorise('core.delete.own', 'com_yoorecipe') && $recipe->created_by == $user->id)));
		}
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : new JRegistry();
		
		// In case recipe not found, get categories instead
		$this->categories = $categoriesModel->getAllPublishedCategories();
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
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
			
			// Set document title
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