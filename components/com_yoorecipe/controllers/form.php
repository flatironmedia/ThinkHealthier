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
 
// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * YooRecipe Controller
 */
class YooRecipeControllerForm extends JControllerForm
{
	/**
	* Cancel recipe edition
	*/
	function cancel ($cachable = false)
	{
		// set default view if not set
		$app = JFactory::getApplication();
		$app->redirect('index.php?option=com_yoorecipe&view=recipes&layout=myrecipes');
	}
	
	/**
	 * Method to check if you can edit an existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   12.2
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option) || JFactory::getUser()->authorise('core.edit.own', $this->option);
	}
	
	/**
	* Save recipe
	*/
	public function save($key = NULL, $urlVar = NULL) {
		
		// Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JInvalid_Token'));
		
		$params = JComponentHelper::getParams('com_yoorecipe');
		$input 	= JFactory::getApplication()->input;
		$data 	= $input->get('jform', array(), 'ARRAY');
		$post 	= $input->get('post', array(), 'ARRAY');
		
		// Check uploaded picture and video
		$picture = $input->get('picture', '', 'STRING');
		if ($input->get('picture', '', 'STRING') !== '1') {
			$data['picture'] = JHtml::_('imageutils.uploadRecipePicture','picture');
		}
		
		// Status parameters
		if ($params->get('auto_publish', 1)) {
			$data['published'] = 1;
		}
		
		if ($params->get('auto_validate', 0)) {
			$data['validated'] = 1;
		} else if (!isset($data['validated'])) {
			$data['validated'] = 0;
		}
		
		if (!isset($data['language'])) {
			$language = JFactory::getLanguage();
			$data['language'] = $language->getTag();
		}
		
        // Initialise variables.
        $task = $this->getTask();
		
        // The save2copy task needs to be handled slightly differently.
        switch ($task) {

			case 'apply':
				$input->post->set('jform', $data);
			break;
			
			case 'save':
				$input->post->set('jform', $data);
			break;
		}
		
		parent::save();
		
		if ($task == 'save') {
			$this->setRedirect('index.php?option=com_yoorecipe&view=recipes&layout=myrecipes');
		}
	}
	
	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param   JModelLegacy  $model      The data model object.
	 * @param   array         $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$recordId = $model->getState($this->context . '.id');
		
		$is_new = ($validData['id'] == 0);
		
		$input 	= JFactory::getApplication()->input;
		$data 	= $input->get('jform', array(), 'ARRAY');
		$table 	= $model->getTable();
		
		// Get models
		$crossCategoryModel 	= JModelLegacy::getInstance('crossCategory', 'YooRecipeModel');
		$ingredientsGroupModel 	= JModelLegacy::getInstance('ingredientsgroup', 'YooRecipeModel');
		$ingredientsModel 		= JModelLegacy::getInstance('ingredients', 'YooRecipeModel');
		$ingredientModel 		= JModelLegacy::getInstance('ingredient', 'YooRecipeModel');
		$yoorecipeModel 		= JModelLegacy::getInstance('yoorecipe', 'YooRecipeModel');
		$seasonsModel			= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		$seasonModel			= JModelLegacy::getInstance('season', 'YooRecipeModel');
		
		// Save ingredient groups and ingredients
		$ingredient_groups	= JHTMLIngredientUtils::buildIngredientGroupsFromRequest($input, $recordId);
		$ingredients		= JHTMLIngredientUtils::buildIngredientsFromRequest($input, $recordId);
		
		$delete_groups = array();
		
		// Take care of ingredient groups
		$ingredient_groups_map = array();
		foreach ($ingredient_groups as $ingredient_group) {
		
			$ingredient_groups_map[$ingredient_group->index] = $ingredient_group;
			$action = $ingredient_group->action;
			unset($ingredient_group->action);
			unset($ingredient_group->index);
			
			switch ($action) {
				case 'I':
					unset($ingredient_group->id);
					$ingredient_group->id = $ingredientsGroupModel->insertYoorecipeIngredientsGroupsObj($ingredient_group);
				break;
				
				case 'U':
					$ingredientsGroupModel->updateYoorecipeIngredientsGroupsObj($ingredient_group);
				break;
				
				case 'D':
					$ingredientsGroupModel->deleteIngredientsGroup($ingredient_group->id);
					$delete_groups[] = $ingredient_group->id;
				break;
			}
		}
		
		// Take care of ingredients
		foreach ($ingredients as $ingredient) {
		
			$ingredient->group_id = $ingredient_groups_map[$ingredient->group_index]->id;
			unset($ingredient->group_index);
			
			$action = $ingredient->action;
			unset($ingredient->action);
			
			if (in_array($ingredient->group_id, $delete_groups)) {
				$action = 'D';
			}
			
			switch ($action) {
				case 'I':
					unset($ingredient->id);
					$ingredient->id = $ingredientModel->insertIngredientObj($ingredient);
				break;
				
				case 'U':
					$ingredientModel->updateIngredientObj($ingredient);
				break;
				
				case 'D':
					$ingredientsModel->deleteIngredientByRecipeAndIngredientId($recordId, $ingredient->id);
				break;
			}
		}
		
		// Save categories
		$crossCategoryModel->saveRecipeCategories($data['category_id'], $recordId);

		// Save multiple seasons
		$seasonsModel->deleteSeasonsByRecipeId($recordId);

		if (isset($data['seasons'])) {
			
			$seasonids = $data['seasons'];
			foreach($seasonids as $seasonid) {
				$seasonModel->insertSeason($recordId, $seasonid);
			}
		}
		
		$recipe = $yoorecipeModel->getRecipeById($recordId);
			
		if($is_new) {
			// Trigger events if needed
			JPluginHelper::importPlugin( 'yoorecipe' );
			$dispatcher = JDispatcher::getInstance();
			$results 	= $dispatcher->trigger( 'onRecipeCreate', array('com_yoorecipe.recipe', &$recipe) );
		}
		
		// If save and close, redirect user to item page
		if ($this->getTask() == 'save') {
			$this->setRedirect('index.php?option=com_yoorecipe&view=recipes&layout=myrecipes');
		} else {
			$this->setRedirect(JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit&id='.$recordId.":".$validData['alias'], false));
		}
		
		return;
	}
}