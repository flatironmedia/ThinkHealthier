<?php
/**
 * @version     1.0.2
 * @package     com_subchannel
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * List controller class.
 */
class SubchannelControllerList extends JControllerForm
{

    function __construct() {
        $this->view_list = 'lists';
        parent::__construct();
    }

}