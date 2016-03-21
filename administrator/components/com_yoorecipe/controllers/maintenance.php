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
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
jimport( 'joomla.filesystem.file' );
 
/**
 * YooRecipes Controller
 */
class YooRecipeControllerMaintenance extends JControllerAdmin
{

	protected $_task;
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	YooRecipeControllerImpex
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'Maintenance', $prefix = 'YooRecipeModel', $config = array()) 
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
	
	/**
	* deleteOldMealEntries
	*/
	public function deleteOldMealEntries() {
	
		$input 		= JFactory::getApplication()->input;
		$nb_days 	= $input->get('nb_days', 30, 'INT');
		
		$maintenanceModel = JModelLegacy::getInstance('maintenance', 'YooRecipeModel');
		$maintenanceModel->deleteOldMealEntries($nb_days);
		
		$this->setRedirect('index.php?option=com_yoorecipe&view=maintenance');
	}
	
	/**
	* migrateServingTypes
	*/
	public function migrateServingTypes() {
	
		$db = JFactory::getDbo();
		
		$db->setQuery("truncate #__yoorecipe_serving_types");
		$db->execute();
		
		$db->setQuery("select distinct language as language from `#__yoorecipe`");
		$distinct_languages = $db->loadObjectList();
		
		$serving_types = array();
		$serving_types['P'] = array('code' => 'PERSONS', 'ordering' => 1);
		$serving_types['B'] = array('code' => 'BATCHES', 'ordering' => 2);
		$serving_types['S'] = array('code' => 'SERVINGS', 'ordering' => 3);
		$serving_types['D'] = array('code' => 'DOZENS', 'ordering' => 4);
		
		foreach ($serving_types as $key => $serving_type) {
			$db->setQuery("INSERT INTO `#__yoorecipe_serving_types` (`code`, `ordering`, `published`, `creation_date`) values (".$db->quote($serving_type['code']).", ".$serving_type['ordering'].", 1, CURDATE())");
			$db->execute();
			$serving_types[$key] = $db->insertid();
		}
		
		// Update recipes
		$db->setQuery("update `#__yoorecipe` set serving_type_id = ".$serving_types['P']." where servings_type = 'P'");
		$db->execute();
		$db->setQuery("update `#__yoorecipe` set serving_type_id = ".$serving_types['B']." where servings_type = 'B'");
		$db->execute();
		$db->setQuery("update `#__yoorecipe` set serving_type_id = ".$serving_types['S']." where servings_type = 'S'");
		$db->execute();
		$db->setQuery("update `#__yoorecipe` set serving_type_id = ".$serving_types['D']." where servings_type = 'D'");
		$db->execute();
		
		$this->setRedirect('index.php?option=com_yoorecipe&view=maintenance');
	}
	
	/**
	* migrateTags
	*/
	public function migrateTags() {
		
		// Copy yoorecipe tags to Joomla tags
		$db = JFactory::getDbo();
		$db->setQuery("select t.tag_value, r.created_by, t.recipe_id, r.language from `#__yoorecipe_tags` t inner join `#__yoorecipe` r on r.id = t.recipe_id group by t.tag_value");
		$yoorecipe_tags = $db->loadObjectList();
		jimport('joomla.filter.output');
		
		foreach ($yoorecipe_tags as $yoorecipe_tag) {
			
			$yoorecipe_tag_safe = JFilterOutput::stringURLSafe($yoorecipe_tag->tag_value);					
			$db->setQuery("insert into `#__tags` (`parent_id`, `level`, `path`, `title`, `alias`, `published`, `checked_out`, `created_user_id`, `created_time`, `language`, `version`, `publish_up`, `publish_down`, `access`) values 
			(1, 1, ".$db->quote($yoorecipe_tag_safe).",".$db->quote($yoorecipe_tag->tag_value).",".$db->quote($yoorecipe_tag_safe).", 1, 0, ".$yoorecipe_tag->created_by.", NOW(), ".$db->quote($yoorecipe_tag->language).", 1, '0000-00-00 00:00:00','0000-00-00 00:00:00', 1)");
			$db->execute();
			$yoorecipe_tag->new_id = $db->insertid();
		}
		
		// Get yoorecipe content type id
		$db->setQuery("select type_id from `#__content_types` where type_alias = 'com_yoorecipe.recipe'");
		$yoorecipe_content_type_id = $db->loadResult();
		
		// Insert ucm content
		$db->setQuery("select * from `#__yoorecipe`");
		$all_recipes = $db->loadObjectList();
		
		$recipes_map = array();
		foreach ($all_recipes as $all_recipe) {
			
			$db->setQuery("insert into `#__ucm_content` (`core_type_alias`, `core_title`, `core_alias`, `core_body`, `core_state`, `core_checked_out_user_id`, `core_access`, `core_featured`, `core_created_user_id`, `core_created_time`, `core_language`, `core_publish_up`, `core_publish_down`, `core_content_item_id`, `core_images`, `core_hits`, `core_version`, `core_type_id`) 
			values ('com_yoorecipe.recipe', ".$db->quote($all_recipe->title).",".$db->quote($all_recipe->alias).",".$db->quote($all_recipe->description).",1, 0, 1, ".$db->quote($all_recipe->featured).",".$db->quote($all_recipe->created_by).", NOW(),".$db->quote($all_recipe->language).",".$db->quote($all_recipe->publish_up).",".$db->quote($all_recipe->publish_down).",".$db->quote($all_recipe->id).",".$db->quote($all_recipe->picture).",".$db->quote($all_recipe->nb_views).",1,".$yoorecipe_content_type_id.")");
			$db->execute();
			$recipes_map[$all_recipe->id] = $db->insertid();
		}
		
		// Insert content item tag map
		foreach ($yoorecipe_tags as $yoorecipe_tag) {
			
			$core_content_id = $recipes_map[$yoorecipe_tag->recipe_id];
			$tag_id = $yoorecipe_tag->new_id;
			$db->setQuery("insert into `#__contentitem_tag_map` (`type_alias`, `core_content_id`, `content_item_id`, `tag_id`, `tag_date`, `type_id`) values ('com_yoorecipe.recipe', ".$core_content_id.", ".$yoorecipe_tag->recipe_id.", ".$tag_id.", NOW(), ".$yoorecipe_content_type_id.")");
			$db->execute();
		}
		
		// Rebuild table order
		jimport('joomla.database.table');
		JTable::addIncludePath(JPATH_ROOT.'/administrator/components/com_tags/tables');
		$tagsTable = JTable::getInstance('Tag', 'TagsTable', array('ordering'));
		$tagsTable->rebuild();
	
		$this->setRedirect('index.php?option=com_yoorecipe&view=maintenance');
	}
	
	/**
	* migrateIngredients
	*/
	public function migrateIngredients() {
		
		JHtmlIngredientUtils::migrateIngredients();
		$this->setRedirect('index.php?option=com_yoorecipe&view=maintenance');
	}
	
}