<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search_healthnews
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_search_healthnews
 *
 * @package     Joomla.Site
 * @subpackage  mod_search_healthnews
 * @since       1.5
 */
class ModSearchHealthNewsHelper
{
	/**
	 * Display the search button as an image.
	 *
	 * @param   string  $button_text  The alt text for the button.
	 *
	 * @return  string  The HTML for the image.
	 *
	 * @since   1.5
	 */
	public static function getSearchImage($button_text)
	{
		$img = JHtml::_('image', 'searchButton.gif', $button_text, null, true, true);

		return $img;
	}
}
