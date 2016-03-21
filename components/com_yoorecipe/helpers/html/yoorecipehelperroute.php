<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_yoorecipe
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * YooRecipe Component Route Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_yoorecipe
 */
abstract class JHtmlYooRecipeHelperRoute
{
	/**
	 * @param	int	The route of the recipe item
	 * @param	int The route of the category item
	 */
	public static function getRecipeRoute($id, $catid = 0, $item_id = 0)
	{
		$url = 'index.php?option=com_yoorecipe&view=recipe&id='.$id;
		if (!empty($item_id) && is_numeric($item_id) && $item_id > 0) {
			$url .= '&Itemid='.$item_id;
		}
		
		return $url;
	}

	/**
	 * @param	int The route of the category item
	 */
	public static function getCategoryRoute($slug)
	{
		return 'index.php?option=com_yoorecipe&view=categories&id='.$slug;
	}
	
	/**
	 * @param	int The route of the category item
	 */
	public static function getUserRoute($user_id)
	{
		return 'index.php?option=com_yoorecipe&view=recipes&layout=chef&id='.$user_id;
	}
	
	/**
	 * @param	int The route of the season item
	 */
	public static function getSeasonRoute($slug)
	{
		$chunks = preg_split('/:/',$slug);
		return 'index.php?option=com_yoorecipe&view=recipes&layout=seasons&month_id='.$chunks[0];
	}
	
		
	/**
	 * @param	int The route of the shoppingList item
	 */
	public static function getShoppingListRoute($shoppingList_id)
	{
		return 'index.php?option=com_yoorecipe&view=shoppinglist&id='.$shoppingList_id;
	}
	
	/**
	 * @param	int The route of the shoppingList item
	 */
	public static function getShoppingListPrintRoute($shoppingList_id)
	{
		return 'index.php?option=com_yoorecipe&view=shoppinglist&layout=print&id='.$shoppingList_id;
	}
	
	/**
	 * @param	int The route of the shoppingList item
	 */
	public static function getShoppingListEditRoute($shoppingList_id)
	{
		return 'index.php?option=com_yoorecipe&view=shoppinglist&layout=edit&id='.$shoppingList_id;
	}
}
