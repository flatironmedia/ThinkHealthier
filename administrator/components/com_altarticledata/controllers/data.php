<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Altarticledata
 * @author     Ace | OGOSense <audovicic@ogosense.com>
 * @copyright  Copyright (C) 2015. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Data controller class.
 *
 * @since  1.6
 */
class AltarticledataControllerData extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'datas';
		parent::__construct();
	}
}
