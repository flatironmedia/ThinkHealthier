<?php
/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Newsletter', JPATH_COMPONENT);

// Execute the task.
$controller = JControllerLegacy::getInstance('Newsletter');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
