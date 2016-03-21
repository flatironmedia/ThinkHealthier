<?php

/**
* @version 		1.0.0
* @package 		com_delagate
* @copyright 	Copyright (C) 2014. All rights reserved.
* @license 		GNU General Public License version 2 or later; see LICENSE.txt
* @author 		Xander <avrhovac@ogosense.com> - http://www.ogosense.com
*/
// no direct access
defined('_JEXEC') or die;
if($nut_slider) :
?>
	<script type="text/javascript">
	//--- Xander@OGOSense Nutritional info module JS ---//
	jQuery(document).ready(function(){
	    jQuery('#slider').on('change', function(){

	    	nut_type = ['calories', 'carbohydrates', 'fat', 'protein', 'saturated_fat', 'sodium', 'fiber', 'sugar', 'cholesterol'];
	    	nut_div = jQuery('#nut_info');
	    	dish_size = parseFloat(jQuery('#dish_size').val());
	    	dish_change = parseFloat(jQuery('#slider').val());
	    	nut_type.each(function(value){
	    		nut_val = parseFloat(nut_div.find('#nut_info_' + value).text());
	    		if(value == 'sodium' || value == 'cholesterol'){
	    			nut_val = ((nut_val*dish_change)/dish_size).toFixed(3);
	    		}
	    		else{
		    		nut_val = ((nut_val*dish_change)/dish_size).toFixed(2);
		    	}
		    	if(value == 'calories')
					nut_div.find('#nut_info_' + value).text(nut_val);
				else
					nut_div.find('#nut_info_' + value).text(nut_val + ' g');
	    	});
	    	jQuery('#dish_size').val(dish_change);
	    });
	});
	</script>

	<div class="nutritional-info" id="nut_info">
		<?php foreach ($nutritions as $key => $value) : ?>
			<div class="item">
			<div class="title nutritional-info-<?php echo strtolower($key);?>-text">
				<?php echo str_replace('_', ' ', $key); ?>
			</div>
			<div class="amount nutritional-info-<?php echo strtolower($key); ?>-value" id="nut_info_<?php echo strtolower($key); ?>">
				<?php
				if($key == 'Sodium' || $key == 'Cholesterol')
					echo number_format($value['value']/1000, 3, '.', '');
				else
					echo number_format($value['value'], 2, '.', ''); ?>
				<span class="nutritional-info-measure">
					<?php echo $value['measure']; ?>
				</span>
			</div>
			</div>
		<?php endforeach; ?>
		<input type="hidden" name="dish_size" value="<?php echo $dish_size; ?>" id="dish_size">
	</div>

<?php else : ?>
	<div class="nutritional-info" id="nut_info">
		<?php foreach ($nutritions as $key => $value) : ?>
			<div class="item">
			<div class="title nutritional-info-<?php echo strtolower($key);?>-text">
				<?php echo str_replace('_', ' ', $key); ?>
			</div>
			<div class="amount nutritional-info-<?php echo strtolower($key); ?>-value" id="nut_info_<?php echo strtolower($key); ?>">
				<?php
				if($key == 'Sodium' || $key == 'Cholesterol')
					echo number_format($value['value']/(1000*$dish_size), 3, '.', '');
				else
					echo number_format($value['value']/$dish_size, 2, '.', ''); ?>
				<span class="nutritional-info-measure">
					<?php echo $value['measure']; ?>
				</span>
			</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>