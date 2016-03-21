<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Altarticledata
 * @author     Ace | OGOSense <audovicic@ogosense.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::register('AltarticledataFrontendHelper', JPATH_COMPONENT . '/helpers/altarticledata.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Altarticledata');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
