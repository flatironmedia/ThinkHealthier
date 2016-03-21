<?php
/*------------------------------------------------------------------------
# com_yoorecipe -  YooRecipe! Joomla 2.5 & 3.x recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.yoorecipe.com
# Technical Support:  Forum - http://www.yoorecipe.com/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$recipe 	= $this->recipe;

JPluginHelper::importPlugin( 'yoorecipe' );
$dispatcher = JDispatcher::getInstance();
$html 		= $dispatcher->trigger( 'onRecipeDisplay', array('com_yoorecipe', &$recipe) );		

echo '<a name="comments"></a>';
if (isset($html[0])) {
	echo '<h3>',JText::_('COM_YOORECIPE_COMMENTS'),'</h3>';
	echo $html[0];
}