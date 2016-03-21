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
class YooRecipeViewLandingPage extends JViewLegacy
{
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		$app				= JFactory::getApplication();
		$menu				= $app->getMenu();
		$active 			= $menu->getActive();
		
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$rating_origin		= $yooRecipeparams->get('rating_origin', 'yoorecipe');
		$use_mealplanner	= $yooRecipeparams->get('use_mealplanner', 1);
		
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : new JRegistry();
		$show_recipes		= !is_null($this->menuParams) ? $this->menuParams->get('show_recipes', 1) : 1;
		
		// Get models
		$landingPageModel 		= $this->getModel('landingpage');
		$yoorecipesModel 		= JModelLegacy::getInstance('yoorecipes','YooRecipeModel');
		$mainModel 				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons','YooRecipeModel');
		$reviewsModel			= JModelLegacy::getInstance('reviews','YooRecipeModel');
		$categoriesModel 		= JModelLegacy::getInstance('categories','YooRecipeModel');
		$ingredientsModel		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$mealPlannerQueueModel 	= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		$ingredientsGroupsModel = JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		$komentoModel 			= JModelLegacy::getInstance('komento', 'YooRecipeModel');
		
		// Get A to Z recipes by letter
		$this->recipeStartLetters = $landingPageModel->getAtoZRecipes('');

		// Get All sub Categories of level 1
		$this->subcategories = $categoriesModel->getCategoriesByParentId(1);
		
		// Get number of published recipes for each subcategory
		foreach ($this->subcategories as $category) {
			$category->nb_recipes = $categoriesModel->getNbRecipesByCategoryId($category->id, $recursive = true);
		}
		//die('<pre>'.print_r($this->subcategories, true).'</pre>');
		// Get user
		$this->user	= JFactory::getUser();
		
		// Assign elements to the view
		if ($this->getLayout() == 'letters') {
			
			$input 				= JFactory::getApplication()->input;
			$letter 			= $input->get('l', '', 'STRING');
			
			$language = JFactory::getLanguage();
			$yoorecipesModel->getState()->set('filter.language', $language->getTag());
			$this->items 		= $yoorecipesModel->getRecipesByLetter($letter);
			$this->crtLetter	= $letter == 'dash' ? JText::_('COM_YOORECIPE_A_NUMBER') : strtoupper($letter);
			
		} else if ($show_recipes) {
			
			$recipesModel 	= JModelLegacy::getInstance('recipes', 'YooRecipeModel');
			$recipe_types 	= $this->menuParams->get('recipe_types', 'featured');
			$app->input->set('layout', $recipe_types); // recipes model needs a layout
			
			$state	= $recipesModel->getState();
			switch ($recipe_types) {
				case 'featured':
					$this->sectionLabel = 'COM_YOORECIPE_LANDINGPAGE_FEATURED';
				break;
				case 'mostpopular':
					$this->sectionLabel = 'COM_YOORECIPE_LANDINGPAGE_MOSTPOPULAR';
					$state->set('orderCol', 'note');
					$state->set('drn', 'desc');
				break;
				case 'mostread':
					$this->sectionLabel = 'COM_YOORECIPE_LANDINGPAGE_MOSTREAD';
					$state->set('orderCol', 'nb_views');
					$state->set('drn', 'desc');
				break;
				case 'mostrecents':
					$this->sectionLabel = 'COM_YOORECIPE_LANDINGPAGE_MOSTRECENTS';
					$state->set('orderCol', 'creation_date');
					$state->set('drn', 'desc');
				break;
				default:
					$this->sectionLabel = '';
				break;
			}
			
			$state->set('filter.validated', 1);
			$state->set('filter.published', 1);
			
			$recipesModel->setState($state);
			
			$this->items 		= $recipesModel->getItems();
			$this->pagination 	= $recipesModel->getPagination();
		}
		
		// Get recipe's ingredients
		if ($show_recipes && $this->items) {
		
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
				
				// Get tags
				$recipe->tags = new JHelperTags;
				$recipe->tags->getItemTags('com_yoorecipe.recipe' , $recipe->id);
				
				if ($use_mealplanner){
					$recipe->is_queued		= $mealPlannerQueueModel->isRecipeQueued($recipe->id, $this->user->id);
				}
				
				// Calculate authorisations
				$recipe->canEdit		= $this->user->authorise('core.admin', 'com_yoorecipe') || ($this->user->guest != 1 && ($this->user->authorise('core.edit', 'com_yoorecipe') || ($this->user->authorise('core.edit.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id))) ;
				$recipe->canDelete	 	= $this->user->authorise('core.admin', 'com_yoorecipe') || ($this->user->guest != 1 && ($this->user->authorise('core.delete', 'com_yoorecipe') || ($this->user->authorise('core.delete.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id)));
			}
		}
		
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
		$menu = $menus->getActive();
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
			if (!empty($title)) {
				$this->document->setTitle($title);
			}
			
			if ($menuParams->get('menu-meta_description'))
			{
				$this->document->setDescription($menuParams->get('menu-meta_description'));
			}

			if ($menuParams->get('menu-meta_keywords')) 
			{
				$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
			}
			
			if ($menuParams->get('robots')) 
			{
				$this->document->setMetadata('robots', $menuParams->get('robots'));
			}
		}
		
		JText::script('COM_YOORECIPE_CONFIRM_DELETE');
		JText::script('COM_YOORECIPE_CONFIRM_DELETE_INGREDIENT');
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
		
		// Add breadcrumbs
		JHTML::_('behavior.modal');
		$app = JFactory::getApplication();
		$pathway = $app->getPathway();
		
		if ($this->getLayout() == 'letters') {
			$pathway->addItem(JText::sprintf('COM_YOORECIPE_YOORECIPE_START_WITH', $this->crtLetter), JUri::current());
		}
	}
}