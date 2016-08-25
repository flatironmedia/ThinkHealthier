<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenurhealthazcategories
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

// no direct access
defined('_JEXEC') or die;
?>
<div class="mod-drop-menu-recipe-categories-first-row">
	<?php for($i = 0; $i < $first_row; $i++) : ?>
		<a href="<?php echo $result[$i]->link; ?>"><?php echo $result[$i]->title; ?></a>
	<?php endfor; ?>
</div>
<div class="mod-drop-menu-recipe-categories-second-row">
	<?php for($i = $first_row; $i < count($result); $i++) : ?>
		<a href="<?php echo $result[$i]->link; ?>"><?php echo $result[$i]->title; ?></a>
	<?php endfor; ?>
</div>