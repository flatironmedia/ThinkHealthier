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
class YooRecipeViewCategories extends JViewLegacy
{
	
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		$user				= JFactory::getUser();
		$app				= JFactory::getApplication();
		$menu				= $app->getMenu();
		$active 			= $menu->getActive();
		
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$rating_origin	 	= $yooRecipeparams->get('rating_origin', 'yoorecipe');
		
		// Get the yoorecipe model
		$categoriesModel 		= JModelLegacy::getInstance('categories','YooRecipeModel');
		$reviewsModel			= JModelLegacy::getInstance('reviews','YooRecipeModel');
		$ingredientsModel 		= JModelLegacy::getInstance('ingredients','YooRecipeModel');
		$mainModel 				= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons','YooRecipeModel');
		$mealPlannerQueueModel 	= JModelLegacy::getInstance('mealplannerqueue','YooRecipeModel');
		$ingredientsGroupsModel	= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
		$komentoModel 			= JModelLegacy::getInstance('komento', 'YooRecipeModel');
		
		// Get category identifier to view
		$input 		= JFactory::getApplication()->input;
		$categoryId = $input->get('id', 0, 'INT');

		$language = JFactory::getLanguage();
		$categoriesModel->getState()->set('filter.language', $language->getTag());
		
		// Assign all recipes to the view
		$this->user					= $user;	
		$this->items 				= $categoriesModel->getItems();
		$this->pagination 			= $this->get('Pagination');
		$this->category 			= $categoriesModel->getCategoryById($categoryId);
		$this->category->nb_recipes = $categoriesModel->getNbRecipesByCategoryId($this->category->id, $recursive = true);
		
		// Check access level is ok.
		$view_levels = $user->getAuthorisedViewLevels();
		if (in_array($this->category->access, $view_levels)) {

			if ($yooRecipeparams->get('show_subcategories', 1)) {
			
				$this->subcategories	= $categoriesModel->getCategoriesByParentId($categoryId);
				
				// Get number of published recipes for each subcategory
				foreach ($this->subcategories as $category) {
					$category->nb_recipes = $categoriesModel->getNbRecipesByCategoryId($category->id, $recursive = true);
				}
			}
			
			// Get recipe's ingredients and ratings
			foreach ($this->items as $recipe)
			{
				$recipe->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($recipe->id);
				foreach ($recipe->groups as $group) {
					$group->ingredients = $ingredientsModel->getIngredientsByGroupId($group->id);
				}
				
				if ($rating_origin == 'komento') {
					$recipe->note = $komentoModel->getRecipeNote($recipe->id);
				}
				
				$recipe->ratings		= $reviewsModel->getReviewsByRecipeIdOrderedByDateDesc($recipe->id, $published = null, $abuse = null);
				$recipe->seasons		= $seasonsModel->getRecipeSeasonsIds($recipe->id);
				$recipe->categories		= $categoriesModel->getRecipeCategories($recipe->id);
				$recipe->is_queued		= $mealPlannerQueueModel->isRecipeQueued($recipe->id, $user->id);
				
				// Get tags
				$recipe->tags = new JHelperTags;
				$recipe->tags->getItemTags('com_yoorecipe.recipe' , $recipe->id);

				// Calculate authorisations
				$recipe->canEdit		= $this->user->guest != 1 && ($this->user->authorise('core.edit', 'com_yoorecipe') || ($this->user->authorise('core.edit.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id)) ;
				$recipe->canDelete	 	= $this->user->guest != 1 && ($this->user->authorise('core.delete', 'com_yoorecipe') || ($this->user->authorise('core.delete.own', 'com_yoorecipe') && $recipe->created_by == $this->user->id));
				
				//--- Xander@OGOSense Recipe image batch modification ---//
				if(!$recipe->picture){
					if(!JFile::exists('/images/com_yoorecipe/recipes/recipe'.$recipe->id.'.jpg')){
						$recipe->picture = 'images/com_yoorecipe/recipes/recipe'.$recipe->id.'.jpg';
					}
				}
			}
		} else {
			$this->no_access = true;
		}
		
		// Set Params defined in menu (if applicable)
		if(isset($active)) {
			$this->menuParams 	= $active->params;
			$this->pageclass_sfx = htmlspecialchars($this->menuParams->get('pageclass_sfx'));
		}
		else {
			$this->menuParams = new JRegistry();
			$this->pageclass_sfx = '';
			
			// Add breadcrumbs
			JHTML::_('behavior.modal');
			$app = JFactory::getApplication();
			$pathway = $app->getPathway();
			$pathway->addItem($this->category->title, JUri::current());
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// In case no recipes found, get categories instead
		if (count($this->items) == 0) {
			$this->categories 	= $this->get('AllPublishedCategories');
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
		$params	= JFactory::getApplication()->getParams();
		$menu	= $menus->getActive();
		
		$title = $this->menuParams->get('page_title', '');
		
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
			$title = $this->category->title;
		}
		$this->document->setTitle($title);

		if ($this->category->metadesc) {
			$this->document->setDescription($this->category->metadesc);
		}
		elseif (!$this->category->metadesc && $params->get('menu-meta_description')) {
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($this->category->metakey) {
			$this->document->setMetadata('keywords', $this->category->metakey);
		}
		elseif (!$this->category->metakey && $params->get('menu-meta_keywords')) {
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}
		
		if ($params->get('robots')) {
			$this->document->setMetadata('robots', $params->get('robots'));
		}
		
		JText::script('COM_YOORECIPE_CONFIRM_DELETE');
		JText::script('COM_YOORECIPE_CONFIRM_DELETE_INGREDIENT');
		JText::script('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION');
	}
}