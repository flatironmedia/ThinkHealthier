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

jimport('joomla.application.module.helper');

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');

// Get Factories
$lang 		= JFactory::getLanguage();
$document	= JFactory::getDocument();

// Init variables
$input	 	= JFactory::getApplication()->input;
$user 		= JFactory::getUser();
$recipe 	= $this->recipe;
$is_printing = $input->get('print', 0, 'INT');

// Component Parameters
$yooRecipeparams 			= JComponentHelper::getParams('com_yoorecipe');
$use_automatic_numbering	= $yooRecipeparams->get('use_automatic_numbering', 1);

$use_video					= $yooRecipeparams->get('use_video', 1);
$can_show_price				= $yooRecipeparams->get('show_price', 0);

// Menu Parameters also defined in Component Settings
$enable_reviews				= $yooRecipeparams->get('enable_reviews', 1);


$currency					= $yooRecipeparams->get('currency', '&euro;');

$can_show_category			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_category', 1);

$can_show_description		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_description', 1);
$can_show_difficulty		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_difficulty', 1);
$can_show_cost				= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cost', 1);
$rating_style				= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'rating_style', 'stars');

$can_show_preparation_time	= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_preparation_time', 1);
$can_show_cook_time			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cook_time', 1);
$can_show_wait_time			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_wait_time', 1);

?>

<div class="recipe-information">
<?php 
	if ($can_show_preparation_time && $recipe->preparation_time != 0) { ?>
		<div class="row-fluid">
			<span class="recipe-info-title span4"><?php echo JText::_('COM_YOORECIPE_RECIPES_PREPARATION_TIME'); ?></span>
			<span class="recipe-info-value cooktime span8">
				<time datetime="PT<?php echo JHtml::_('datetimeutils.formattime', $recipe->preparation_time, "D", "H", "M");?>" itemprop="prepTime"><?php echo JHtml::_('datetimeutils.formattime', $recipe->preparation_time); ?></time>
			</span>
		</div>
		
<?php	}?>

<?php	if ($can_show_cook_time && $recipe->cook_time != 0) { ?>
		<div class="row-fluid">
			<span class="recipe-info-title span4"><?php echo JText::_('COM_YOORECIPE_RECIPES_COOK_TIME'); ?></span>
			<span class="recipe-info-value cooktime span8">
				<time datetime="PT<?php echo JHtml::_('datetimeutils.formattime', $recipe->cook_time, "D", "H", "M");?>" itemprop="prepTime"><?php echo JHtml::_('datetimeutils.formattime', $recipe->cook_time); ?></time>
			</span>
		</div>	
<?php 	} ?>

<?php 	if ($can_show_wait_time && $recipe->wait_time != 0) { ?>
		<div class="row-fluid">
			<span class="recipe-info-title span4"><?php echo JText::_('COM_YOORECIPE_RECIPES_WAIT_TIME');?></span>
			<span class="recipe-info-value waittime span8"><?php echo JHtml::_('datetimeutils.formattime', $recipe->wait_time); ?></span>
		</div>
<?php 	} ?>
</div>

<div class="row-fluid">
	<div class="span12 div-ingredients">
		<?php echo $this->loadTemplate('ingredients'); ?>
		<div class="yoorecipe-after-ingredients">
		<?php
			$modules = JModuleHelper::getModules('yoorecipe-after-ingredients');
			foreach($modules as $module) {
				echo JModuleHelper::renderModule($module);
			}
		?>
		</div>
	</div>
</div>

<div id="div-recipe-preparation-single">
	<h3>Directions</h3>
<?php
	$modules = JModuleHelper::getModules('yoorecipe-before-directions');
	if (count($modules) > 0) {
		
		echo '<div class="yoorecipe-before-directions">';
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>

	<div class="row-fluid">
		<div class="span12" itemprop="recipeInstructions">
			<?php echo ($use_automatic_numbering) ? JHtml::_('yoorecipeutils.formatParagraphs', $recipe->preparation) : $recipe->preparation; ?>
		</div>
	</div>
	
<?php
	$modules = JModuleHelper::getModules('yoorecipe-after-directions');
	if (count($modules) > 0) {
		echo '<div class="yoorecipe-after-directions">';
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>

<?php	
	if ($use_video && !empty($recipe->video)) {
		echo '<div class="row-fluid">';
		echo '<div class="span12">'.JHtml::_('yoorecipeutils.generateVideoPlayer', $recipe->video).'</div>';
		echo '</div>';
	}
?>

<?php if (!empty($recipe->notes)) { ?>
	<div id="div-recipe-notes">
		<h3><?php echo JText::_('COM_YOORECIPE_YOORECIPE_NOTES_LABEL'); ?></h3>
		<div class="row-fluid">
			<div class="span12">
				<?php echo $recipe->notes; ?>
			</div>
		</div>
	</div>
<?php } ?>

</div>