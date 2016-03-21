<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Ads_frequency
 * @author     Ace | OGOSense <audovicic@ogosense.com>
 * @copyright  Copyright (C) 2016. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('Ads_frequencyFrontendHelper', JPATH_COMPONENT . '/helpers/ads_frequency.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Ads_frequency');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
