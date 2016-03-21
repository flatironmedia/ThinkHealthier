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
 
// import Joomla modelform library
jimport('joomla.application.component.modeladmin');
 
/**
 * YooRecipe Model
 */
class YooRecipeModelYooRecipe extends JModelAdmin
{

	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_yoorecipe.recipe';
	
	private $checked_out;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'YooRecipe', $prefix = 'YooRecipeTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input = JFactory::getApplication()->input;
		$filter  = JFilterInput::getInstance();

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['title'] == $origTable->title)
			{
				$data['title'] = JString::increment($data['title']);
				$data['alias'] = JString::increment($data['alias'], 'dash');
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['state'] = 0;
		}

		return parent::save($data);
	}

	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true) 
	{
		// Get the form.
		$form = $this->loadForm('com_yoorecipe.yoorecipe', 'yoorecipe', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	 /**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string       Script files
	 */
	public function getScript() {
		return 'administrator/components/com_yoorecipe/models/forms/yoorecipe.js';
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_yoorecipe.edit.yoorecipe.data', array());
		if (empty($data)) 
		{
	
			$seasonsModel		= JModelLegacy::getInstance('seasons','YooRecipeModel');
			$crossCategoryModel	= JModelLegacy::getInstance('crossCategory','YooRecipeModel');
			
			$data = $this->getItem();
			$data->category_id 	= $crossCategoryModel->getRecipeCategoriesIds($data->id);
			$data->seasons	 	= $seasonsModel->getRecipeSeasonsIds($data->id);
		}
		return $data;
	}
	
	/**
	 * Method to get a category.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk))
		{
			if (!empty($result->id))
			{
				$result->tags = new JHelperTags;
				$result->tags->getTagIds($result->id, 'com_yoorecipe.recipe');
				// $result->metadata['tags'] = $result->tags;
			}			
			}

		return $result;
	}
	
	/**
	 * getAllCategories
	 * TODO use native model
	 */
	public function getAllCategories() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the joomla native categories table
		$query->select('*');
		$query->from('#__categories');
		$query->where('extension = '.$db->quote('com_yoorecipe'));
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	 * getYooRecipeAssets
	 */
	public function getYooRecipeAssets() {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// From the joomla native assets table
		$query->select('*');
		$query->from('#__assets');
		$query->where('name LIKE '.$db->quote('%yoorecipe%'));
		
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	/**
	* deleteRecipeById
	*/
	public function deleteRecipeById($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete ingredients
		$crossCategoryModel 	= JModelLegacy::getInstance('crosscategory', 'YooRecipeModel' );
		$ingredientsModel 		= JModelLegacy::getInstance('ingredients', 'YooRecipeModel' );
		$ingredientsGroupsModel = JModelLegacy::getInstance('ingredientsgroups', 'YooRecipeModel' );
		$reviewsModel 			= JModelLegacy::getInstance('reviews', 'YooRecipeModel' );
		$favouritesModel 		= JModelLegacy::getInstance('favourites', 'YooRecipeModel' );
		$seasonsModel			= JModelLegacy::getInstance('seasons', 'YooRecipeModel');
		
		// Delete dependencies
		$ingredientsModel->deleteIngredientsByRecipeId($recipe_id);
		$ingredientsGroupsModel->deleteIngredientGroupsByRecipeId($recipe_id);
		$crossCategoryModel->deleteCrossCategoriesByRecipeId($recipe_id);
		$reviewsModel->deleteReviewsByRecipeId($recipe_id);
		$this->deleteAssetsOfRecipeId($recipe_id);
		$favouritesModel->deleteFavoritesByRecipeId($recipe_id);
		$this->deletePictureByRecipeId($recipe_id);
		$seasonsModel->deleteSeasonsByRecipeId($recipe_id);
		
		// Delete recipe
		$query->delete('#__yoorecipe');
		$query->where('id = '.$db->quote($recipe_id));
		$db->setQuery($query);
		$result = $db->execute();
		
		if ($result) {
			JPluginHelper::importPlugin( 'yoorecipe' );
			$dispatcher = JDispatcher::getInstance();
			$results 	= $dispatcher->trigger( 'onRecipeDelete', array($this->option.'.'.$this->name, &$recipe_id) );
		}
		
		return $result;
	}

	/**
	* deletePictureByRecipeId
	*/
	private function deletePictureByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('picture');
		$query->from('#__yoorecipe');
		$query->where('id = '.$db->quote($recipe_id));
	
		$db->setQuery($query);
		$picture_path = $db->loadResult();
		
		if (!empty($picture_path)) {
			$path_parts = pathinfo(JPATH_SITE.'/'.$picture_path);
			array_map('unlink', glob($path_parts['dirname'].'/*'.$path_parts['filename'].'*'));
		}
	}
	
	/**
	* deleteAssetsOfRecipeId
	*/
	private function deleteAssetsOfRecipeId($recipe_id) {
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Delete cross categories
		$query->delete('#__assets');
		$query->where('name = '.$db->quote('#__yoorecipe.'.$recipe_id));
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	* getRecipeById
	*/
	public function getRecipeById($recipe_id, $config = array()) {
	
		$user = JFactory::getUser();
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select('r.id, r.access, r.title, r.alias, r.description, r.created_by, r.preparation, r.notes, r.serving_type_id');
		$query->select('r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins, r.fibers, r.salt');
		$query->select('r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up');
		$query->select('r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published, r.cuisine');
		$query->select('r.validated, r.featured, r.nb_views, r.note, r.language, l.title as language_title, st.code as servings_type_code');
		$query->select('r.metakey, r.metadata, r.use_slider, r.price');

		$query->select('CASE WHEN CHARACTER_LENGTH(r.alias) THEN CONCAT_WS(\':\', r.id, r.alias) ELSE r.id END as slug');
		$query->select('CASE WHEN CHARACTER_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug');
		$query->select('CASE WHEN fr.recipe_id = r.id THEN 1 ELSE 0 END as favourite');
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$query->select('ua.username AS author_name');
		} else if ($showAuthorName == 'name') {
			$query->select('ua.name AS author_name');
		}
		$query->select('ua.email AS author_email');
		
		$query->join('LEFT', '#__users ua ON ua.id = r.created_by');
		$query->from('#__yoorecipe as r');
		
		// Join over cross categories
		$query->join('LEFT', '#__yoorecipe_categories cc on cc.recipe_id = r.id');
		$query->join('LEFT', '#__categories c on cc.cat_id  = c.id');
		
		// Join over favourites
		$query->join('LEFT', '#__yoorecipe_favourites AS fr ON fr.recipe_id = r.id AND fr.user_id = '.$db->quote($user->id));
		
		// Join over languages
		$query->join('LEFT', '#__languages AS l ON l.lang_code = r.language');
		
		// Join over serving types
		$query->join('LEFT', '#__yoorecipe_serving_types st ON st.id = r.serving_type_id');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = r.access');
		
		// Where id = ...
		$query->where('r.id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		$recipe = $db->loadObject();
		
		if (isset($config['ingredients'])) {
			
			$ingredientsGroupsModel		= JModelLegacy::getInstance('ingredientsgroups','YooRecipeModel');
			$ingredientsModel			= JModelLegacy::getInstance('ingredients','YooRecipeModel');
			
			$recipe->groups = $ingredientsGroupsModel->getIngredientsGroupsByRecipeId($recipe->id);
			foreach ($recipe->groups as $group) {
				$group->ingredients = $ingredientsModel->getIngredientsByGroupId($group->id);
			}
		}
		
		if (isset($config['categories'])) {
			$crossCategoryModel	= JModelLegacy::getInstance('crossCategory','YooRecipeModel');
			$recipe->categories = $crossCategoryModel->getRecipeCategoriesIds($recipe_id);
		}
		
		if (isset($config['seasons'])) {
			$seasonsModel		= JModelLegacy::getInstance('seasons','YooRecipeModel');
			$recipe->seasons 	= $seasonsModel->getRecipeSeasonsIds($recipe_id);
		}
		
		if (isset($config['ratings'])) {
			$reviewsModel		= JModelLegacy::getInstance('reviews', 'YooRecipeModel');
			$recipe->ratings 	= $reviewsModel->getReviewsByRecipeId($recipe_id);
		}
		
		return $recipe;
	}
	
	/**
	 * incrementViewCountOfRecipe
	 */
	public function incrementViewCountOfRecipe($recipe_id, $old_nb_views)
	{
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// From the recipe category table
		$query->update('#__yoorecipe');
		$query->set('nb_views = '.$db->quote($old_nb_views+1));
		$query->where('id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	 * getAuthorByRecipeId
	 */
	function getAuthorByRecipeId($recipe_id) {
	
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('created_by');
		$query->from('#__yoorecipe');
		$query->where('id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Update the global note of a given recipe
	 */
	public function updateRecipeGlobalNote($recipe_id) {
	
		// Calculate ponderated note
		$recipe = $this->getRecipeById($recipe_id, $config = array('ratings' => 1));
		
		$sum = 0;
		$global_note = null;
		$cnt = 0;
		
		if (count($recipe->ratings) > 0) {
		
			foreach ($recipe->ratings as $rating) {
				if ($rating->published && $rating->abuse == 0) {
					$sum += $rating->note;
					$cnt++;
				}
			}
			
			$global_note = ($cnt > 0) ? round( (float) $sum / $cnt, 2) : null;
		}	
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->update('#__yoorecipe');
		$query->set('note = '.$db->quote($global_note));
		$query->where('id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->execute();
	}
	
	/**
	* getSimilarRecipes
	*/
	public function getSimilarRecipes($recipe, $filter_languages = true) {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Component parameters
		$yooRecipeparams = JComponentHelper::getParams('com_yoorecipe');
		$nb_similar_recipes = $yooRecipeparams->get('nb_similar_recipes', 3);
		
		// From the recipe table
		$query->select('r.id, r.access, r.title, r.alias, r.description, r.created_by, r.preparation, r.notes, r.serving_type_id'.
				', r.nb_persons, r.difficulty, r.cost, r.sugar, r.carbs, r.fat, r.saturated_fat, r.cholesterol, r.proteins, r.fibers, r.salt'.
				', r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up'.
				', r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published'.
				', r.validated, r.featured, r.nb_views, r.note, r.language');
				
		$query->from('#__yoorecipe r');
		
		// Join over cross categories
		$query->join('LEFT', '#__yoorecipe_categories cc on cc.recipe_id = r.id');
		
		// Filter categories
		$category_ids = array();
		foreach ($recipe->categories as $category) {
			$category_ids[] = $db->quote($category->id);
		}
		$query->where('cc.cat_id IN ('.implode(',', $category_ids).')');
		
		// Filter by recipe title
		$title_chunks = explode(' ',$recipe->title);
		$regexps = array();
		foreach ($title_chunks as $chunk) {
			if (strlen($chunk) > 3) {
				$regexps[] = $chunk;
			}
		}
		if (!empty($regexps)) {
			$query->where('r.title REGEXP '.$db->quote(implode("|", $regexps)));
		}
		
		// Filter by recipe status
		$query->where('r.published = 1');
		$query->where('r.validated = 1');
		$query->where('r.id != '.$db->quote($recipe->id));
		
		if ($filter_languages) {
			$query->where('r.language IN ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
		
		$query->group('r.id');
		$query->order('r.title asc');
		$db->setQuery($query, 0, $nb_similar_recipes);
		
		return $db->loadObjectList();
	}
	
	/**
	* getRecipeLanguageById
	*/
	public function getRecipeLanguageById($recipe_id) {
	
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query->select('language');
		$query->from('#__yoorecipe');
		$query->where('id = '.$db->quote($recipe_id));
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * insertRecipeObj
	 */
	public function insertRecipeObj($recipeObject) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__yoorecipe', $recipeObject, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateRecipeObj
	 */
	public function updateRecipeObj($recipeObject) {
		
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__yoorecipe', $recipeObject, 'id', true);
	}
	
	/**
	 * insertCategoriesObj
	 * @param	object		The recipe object
	 */
	public function insertCategoriesObj($categoriesObj) {
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__categories', $categoriesObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateCategoriesObj
	 */
	public function updateCategoriesObj($categoriesObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__categories', $categoriesObj, 'id', true);
	}
	
	/**
	 * insertAssetsObj
	 */
	public function insertAssetsObj($assetsObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		$result = $db->insertObject('#__assets', $assetsObj, 'id');
		
		if ($result === false) {
			return false;
		} else {
			return $db->insertid();
		}
	}
	
	/**
	 * updateAssetsObj
	 */
	public function updateAssetsObj($assetsObj) {
	
		// Create a new query object
		$db = JFactory::getDBO();
		return $db->updateObject('#__assets', $assetsObj, 'id', true);
	}

	public function getUnits(){
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__yoorecipe_units'))
			->order('id ASC');
		$db->setQuery($query);
		$temp = $db->loadObjectList();

		return $temp;
    }

    public function getGroups($recipe_id){
    	$db = JFactory::getDbo();

    	$query = $db->getQuery(true);

    	$query->select('*')
    		->from($db->quoteName('#__yoorecipe_ingredients_groups'))
    		->where($db->quoteName('recipe_id'). ' = '.$recipe_id);
	    $db->setQuery($query);
	    $temp = $db->loadObjectList();

	    return $temp;
    }
	
}