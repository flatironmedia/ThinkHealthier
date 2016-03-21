<?php
/**
 * @author     mediahof, Kiel-Germany
 * @link       http://www.mediahof.de
 * @copyright  Copyright (C) 2013 - 2014 mediahof. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class plgSystemFail2Ban extends JPlugin
{

    public function onUserLoginFailure(array $response)
    {
        error_log(sprintf($this->params->get('message'), $response['username']));
    }
}