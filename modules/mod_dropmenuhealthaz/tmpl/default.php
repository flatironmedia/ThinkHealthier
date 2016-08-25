<?php

/**
* @version 		1.0.0
* @package 		mod_dropmenuhealthaz
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/

// no direct access
defined('_JEXEC') or die;

?>

<div class="menu-module-articles">
	<?php foreach ($result as $key => $value) : ?>
		<div class="item">
		    <div class="padding clearfix">
		    	<a href="<?php echo $value->link; ?>" target="_self">
		    		<img class="top" src="<?php echo '/slir/w183-h122/'.$value->picture;?>" alt="image">
		    		<h4>
		    			<?php echo $value->title;?>
		    	</a>
		    		</h4>
		    </div>
		    <div class="xs_intro">
		    	<?php
		    	if($value->description && strpos($value->description, ' ', 50))
		    		echo substr($value->description, 0, strpos($value->description, ' ', 50)).'...';
		    	elseif($value->description)
		    		echo $value->description;
		    	?>
		    </div>
		</div>
	<?php endforeach; ?>
</div>
