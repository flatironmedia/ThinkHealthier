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

abstract class JHtmlOptionsUtils
{
	/**
	 * @var	array	Cached array of the recipe items.
	 */
	protected static $items = array();
	
	/**
	* publishedOptions
	*/
	public static function publishedOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();
		if (!array_key_exists('published', $config) || $config['published']) {
			$options[]	= JHtml::_('select.option', '1', JText::_('JPUBLISHED'));
		}
		if (!array_key_exists('unpublished', $config) || $config['unpublished']) {
			$options[]	= JHtml::_('select.option', '0',  JText::_('JUNPUBLISHED'));
		}
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[]	= JHtml::_('select.option', '*',  JText::_('JALL'));
		}
		return $options;
	}
	
	/**
	 * Returns a list of authors who created recipes
	 */
	public static function createdByOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();
		
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select('distinct u.id, u.username');
		$query->from('#__yoorecipe as r');
		$query->join('LEFT', '#__users u on u.id = r.created_by');
		$query->order('r.created_by asc');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as &$item) {
			$options[] = JHtml::_('select.option', $item->id, $item->username);
		}
		
		return $options;
	}
	
	/**
	 * Returns an array of ingredient units state filter options.
	 *
	 * @param	array			An array of configuration options.
	 *							This array can contain a list of key/value pairs where values are boolean
	 *							and keys can be taken from 'published', 'unpublished', 'all'.
	 *							These pairs determine which values are displayed.
	 * @return	string			The HTML code for the select tag
	 *
	 * @since	1.6
	 */
	public static function servingTypesOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();
			
		// Create a new query object.		
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		// Select the required fields from the table.
		$query->select('distinct st.code');
		$query->from('#__yoorecipe_serving_types as st');
		$query->order('st.code asc');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as &$item) {
			$options[] = JHtml::_('select.option', $item->code, $item->code.' ');
		}
		
		return $options;
	}
	
	/**
	 * Returns an array of installed languages
	 *
	 * @param	array			An array of configuration options.
	 *							This array can contain a list of key/value pairs where values are boolean
	 *							and keys can be taken from 'published', 'unpublished', 'all'.
	 *							These pairs determine which values are displayed.
	 * @return	string			The HTML code for the select tag
	 *
	 * @since	1.6
	 */
	public static function installedLanguages($config = array())
	{
		// Build the active state filter options.
		$options	= array();
		$languages = JLanguage::getKnownLanguages();
		foreach ($languages as $tag => $language) {
			$options[] = JHtml::_('select.option', $tag, $language['name']);
		}
		return $options;
	}
	
	/**
	* offensiveOptions
	*/
	public static function offensiveOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();
		if (!array_key_exists('offensive', $config) || $config['offensive']) {
			$options[]	= JHtml::_('select.option', '1', JText::_('COM_YOORECIPE_OFFENSIVE'));
		}
		if (!array_key_exists('notoffensive', $config) || $config['notoffensive']) {
			$options[]	= JHtml::_('select.option', '0', JText::_('COM_YOORECIPE_NOT_OFFENSIVE'));
		}
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[]	= JHtml::_('select.option', '*', JText::_('JALL'));
		}
		return $options;
	}
	
	/**
	 * Returns an array of recipes
	 *
	 * @param	string	The extension option.
	 * @param	array	An array of configuration options. By default, only published and unpulbished categories are returned.
	 *
	 * @return	array
	 */
	public static function recipeOptions( $config = array('filter.published' => array(0,1)))
	{
		$hash = md5('recipes.'.serialize($config));

		if (!isset(self::$items[$hash])) {
			$config	= (array) $config;
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select('r.id, r.title');
			$query->from('#__yoorecipe AS r');

			// Filter on the published state
			if (isset($config['filter.published'])) {
				if (is_numeric($config['filter.published'])) {
					$query->where('r.published = '.(int) $config['filter.published']);
				} else if (is_array($config['filter.published'])) {
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('r.published IN ('.implode(',', $config['filter.published']).')');
				}
			}
			// Filter on the category state
			if (isset($config['filter.category_id']) && $config['filter.category_id'] != 0) {
			
				$query->join('LEFT', '#__yoorecipe_categories cc on cc.recipe_id = r.id');
				$query->where('cc.cat_id = '.(int) $config['filter.category_id']);
			}

			$query->order('r.title');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			// Assemble the list options.
			self::$items[$hash] = array();

			foreach ($items as &$item) {
				self::$items[$hash][] = JHtml::_('select.option', $item->id, $item->title);
			}
		}

		return self::$items[$hash];
	}
	
	/**
	* validatedOptions
	*/
	public static function validatedOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();
		if (!array_key_exists('validated', $config) || $config['validated']) {
			$options[]	= JHtml::_('select.option', '1', JText::_('COM_YOORECIPE_VALIDATED_OPTION'));
		}
		if (!array_key_exists('notvalidated', $config) || $config['notvalidated']) {
			$options[]	= JHtml::_('select.option', '0', JText::_('COM_YOORECIPE_NOTVALIDATED_OPTION'));
		}
		if (!array_key_exists('all', $config) || $config['all']) {
			$options[]	= JHtml::_('select.option', '*', JText::_('JALL'));
		}
		return $options;
	}
	
	/**
	* nbServingsOptions
	*/
	public static function nbServingsOptions()
	{
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$max_servings		= $yooRecipeparams->get('max_servings', 10);
		
		$options	= array();
		for ($i = 1 ; $i <= $max_servings ; $i++) {
			$options[]	= JHtml::_('select.option', $i, $i);
		}
		return $options;
	}
	
	/**
	* cuisineOptions
	*/
	public static function cuisineOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('code');
		$query->from('#__yoorecipe_cuisines');
		$query->where('published = 1');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach ($items as &$item) {
			$options[] = JHtml::_('select.option', $item->code, JText::_('COM_YOORECIPE_CUISINE_'.$item->code));
		}
		
		return $options;
	}
	
	/**
	* recipeTimeOptions
	*/
	public static function recipeTimeOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();

		$options[] = JHtml::_('select.option', 15, JText::_('COM_YOORECIPE_RECIPE_TIME_UNDER_15'));
		$options[] = JHtml::_('select.option', 30, JText::_('COM_YOORECIPE_RECIPE_TIME_UNDER_30'));
		$options[] = JHtml::_('select.option', 60, JText::_('COM_YOORECIPE_RECIPE_TIME_UNDER_60'));
		$options[] = JHtml::_('select.option', 90, JText::_('COM_YOORECIPE_RECIPE_TIME_UNDER_90'));
		
		return $options;
	}	
	
	/**
	* categoriesOptions
	*/
	public static function categoriesOptions($config = array())
	{
	
		$categoriesModel = JModelLegacy::getInstance('categories','YooRecipeModel');
		$categories 	 = $categoriesModel->getAllPublishedCategories();
		
		// Build the active state filter options.
		$options	= array();

		foreach ($categories as $category) {
			$options[] = JHtml::_('select.option', $category->id, htmlspecialchars($category->title));
		}
		
		return $options;
	}
	
	/**
	* orderOptions
	*/
	public static function orderOptions($config = array())
	{
		// Build the active state filter options.
		$options	= array();

		$options[] = JHtml::_('select.option', 'creation_date', JText::_('COM_YOORECIPE_ORDER_RECENT'));
		$options[] = JHtml::_('select.option', 'category_id', JText::_('COM_YOORECIPE_ORDER_CATEGORY'));
		$options[] = JHtml::_('select.option', 'cuisine', JText::_('COM_YOORECIPE_ORDER_CUISINE'));
		
		return $options;
	}	
}