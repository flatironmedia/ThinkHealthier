<?php

/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class NewsletterFrontendHelper
 *
 * @since  1.6
 */
class NewsletterFrontendHelper
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_newsletter/models/' . strtolower($name) . '.php'))
		{
			$model = JModelLegacy::getInstance($name, 'NewsletterModel');
		}

		return $model;
	}
}
