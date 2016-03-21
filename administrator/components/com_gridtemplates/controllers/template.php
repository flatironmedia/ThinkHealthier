<?php
/**
 * @version     1.0.0
 * @package     com_gridtemplates
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Template controller class.
 */
class GridtemplatesControllerTemplate extends JControllerForm
{

    function __construct() {
        $this->view_list = 'templates';
        parent::__construct();
    }

}