<?php
/**
 * @version     1.0.2
 * @package     com_subchannel
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JControllerLegacy::getInstance('Subchannel');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
