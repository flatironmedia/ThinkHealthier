<?php
/**
 * ------------------------------------------------------------------------
 * JA Masshead Module for J25 & J34
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die('Restricted access');
?>

<?php 

$jinput = JFactory::getApplication()->input;

$option = $jinput->getCmd('option'); // This gets the component
$view   = $jinput->getCmd('view');   // This gets the view
$layout = $jinput->getCmd('layout'); // This gets the view's layout

if ($option == 'com_yoorecipe' && $view == 'landingpage') {
    echo "
    <style>
	.com_yoorecipe .jamasshead .sharing-icons .addthis_default_style .at300b {
    	padding: 0 2px;
	}
	</style>";
	echo "\n";
	echo "\n";
	echo "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/300/addthis_widget.js\"></script>";
	echo "\n";
}

if ($option == 'com_yoorecipe' && $view == 'categories') {
    echo "
    <style>
	.com_yoorecipe .jamasshead .sharing-icons .addthis_default_style .at300b {
    	padding: 0 2px;
	}
	</style>";
	echo "\n";
	echo "\n";
	echo "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/300/addthis_widget.js\"></script>";
	echo "\n";
}

if ($option == 'com_content' && $view == 'healthaz') {
    echo "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/300/addthis_widget.js\"></script>";
	echo "\n";
}

if ($option == 'com_content' && $view == 'azalphabet') {
    echo "<script type=\"text/javascript\" src=\"http://s7.addthis.com/js/300/addthis_widget.js\"></script>";
	echo "\n";
}

?>

<div class="jamasshead<?php echo $params->get('moduleclass_sfx','')?>">
	<h3 class="jamasshead-title"><?php echo $masshead['title']; ?></h3>
	<div class="jamasshead-date"><?php echo $masshead['date']; ?></div>

<?php if ($view != 'recipe' && $view != 'article') { ?>
	<div class="sharing-icons">
	<div class="addthis_toolbox addthis_default_style ">
		<a class="addthis_button_facebook"></a>
		<a class="addthis_button_twitter"></a>
		<a class="addthis_button_pinterest_pinit"></a>
		<a class="addthis_counter addthis_pill_style"></a>
	</div>
	</div>
<?php }else { ?>
	<div></div>
<?php } ?>

</div>
<div class="after-masthead">
</div>
