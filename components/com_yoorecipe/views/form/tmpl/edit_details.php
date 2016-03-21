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

// No direct access
defined('_JEXEC') or die('Restricted access');

// Get user
$document 		= JFactory::getDocument();
$user			= JFactory::getUser();
$is_admin		= $user->authorise('core.admin', 'com_yoorecipe');

// Get config parameters
$params 				= JComponentHelper::getParams('com_yoorecipe');
$use_recipe_settings 	= $params->get('use_recipe_settings', 1);
$currency				= $params->get('currency', '&euro;');
$use_tags				= $use_recipe_settings ? $params->get('use_tags', 1) : $params->get('use_tags_fe', 1);
$show_seasons 			= $use_recipe_settings ? $params->get('show_seasons', 1) : $params->get('show_seasons_fe', 1);

$use_nutrition_facts	= $params->get('use_nutrition_facts', 1);
$show_diet	 			= $use_nutrition_facts == 0 ? 0 : ($use_recipe_settings ? $params->get('show_diet', 1) : $params->get('show_diet_fe', 1));
$show_veggie	 		= $use_nutrition_facts == 0 ? 0 : ($use_recipe_settings ? $params->get('show_veggie', 1) : $params->get('show_veggie_fe', 1));
$show_gluten_free	 	= $use_nutrition_facts == 0 ? 0 : ($use_recipe_settings ? $params->get('show_gluten_free', 1) : $params->get('show_gluten_free_fe', 1));
$show_lactose_free	 	= $use_nutrition_facts == 0 ? 0 : ($use_recipe_settings ? $params->get('show_lactose_free', 1) : $params->get('show_lactose_free_fe', 1));

$use_video				= $use_recipe_settings ? $params->get('use_video', 1) : $params->get('show_video_fe', 1);
$show_price				= $use_recipe_settings ? $params->get('show_price', 0) : $params->get('show_price_fe', 0);
$show_video 			= $use_recipe_settings ? $params->get('show_video', 1) : $params->get('show_video_fe', 1);
?>
<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_DETAILS'); ?></h2>
<fieldset>
	<?php echo $this->form->renderField('cuisine'); ?>
	<?php if ($show_seasons) { ?>	
		<?php echo $this->form->renderField('seasons'); ?>
	<?php } ?>
	<?php if ($use_tags) { ?>
		<?php echo $this->form->renderField('tags'); ?>
	<?php }	?>
	
	<div class="row-fluid">
	<?php if ($show_diet) { ?>
		<div class="span6 control-group">
			<?php echo $this->form->renderField('diet'); ?>
		</div>
	<?php } ?>

	<?php if ($show_veggie) { ?>
		<div class="span6 control-group">
			<?php echo $this->form->renderField('veggie'); ?>
		</div>
	<?php } ?>
	</div>

	<div class="row-fluid">
	<?php if ($show_gluten_free) { ?>
		<div class="span6 control-group">
			<?php echo $this->form->renderField('gluten_free'); ?>
		</div>
	<?php } ?>

	<?php if ($show_lactose_free) { ?>
		<div class="span6 control-group">
			<?php echo $this->form->renderField('lactose_free'); ?>
		</div>
	<?php } ?>
	</div>
	
	<?php if ($show_price) { ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('price'); ?>
			</div>
			<div class="controls">
				<div class="input-append">
				<?php echo $this->form->getInput('price');?>
				<div class="add-on"><?php echo $currency; ?></div>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if ($use_video == 1) { ?>	
		<?php echo $this->form->renderField('video'); ?>
	<?php } ?>
</fieldset>