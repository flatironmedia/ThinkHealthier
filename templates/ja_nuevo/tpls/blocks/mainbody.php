<?php
/**
 * ------------------------------------------------------------------------
 * JA Nuevo template
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;
?>

<?php

/**
 * Mainbody 3 columns, content in center: sidebar1 - content - sidebar2
 */

// positions configuration
$sidebar1 = 'sidebar-1';
$sidebar2 = 'sidebar-2';

$sidebar1 = $this->countModules($sidebar1) ? $sidebar1 : false;
$sidebar2 = $this->countModules($sidebar2) ? $sidebar2 : false;

if(JFactory::getApplication()->input->get('option', '', 'STRING') == 'com_yoorecipe' && (JFactory::getApplication()->input->get('view', '', 'STRING') == 'landingpage' || JFactory::getApplication()->input->get('view', '', 'STRING') == 'categories')){
	$sidebar1 = false;
	$sidebar2 = 'sidebar-2';
}
elseif(JFactory::getApplication()->input->get('option', '', 'STRING') == 'com_yoorecipe' && JFactory::getApplication()->input->get('view', '', 'STRING') == 'recipe'){
	$sidebar1 = 'sidebar-1';
	$sidebar2 = 'sidebar-2';
}
elseif(JFactory::getApplication()->input->get('option', '', 'STRING') == 'com_content' && (JFactory::getApplication()->input->get('view', '', 'STRING') == 'healthaz' || JFactory::getApplication()->input->get('view', '', 'STRING') == 'azalphabet')){
	$sidebar1 = false;
	$sidebar2 = 'sidebar-2';
}

// detect layout
if ($sidebar1 && $sidebar2) {
	$this->loadBlock('mainbody/two-sidebar', array('sidebar1' => $sidebar1, 'sidebar2' => $sidebar2));
} elseif ($sidebar1) {
	$this->loadBlock('mainbody/one-sidebar-left', array('sidebar' => $sidebar1));
} elseif ($sidebar2) {
	$this->loadBlock('mainbody/one-sidebar-right', array('sidebar' => $sidebar2));
} else {
	$this->loadBlock('mainbody/no-sidebar');
}
