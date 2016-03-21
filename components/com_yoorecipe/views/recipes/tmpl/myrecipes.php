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

// Get factories
$document 	= JFactory::getDocument();
$user 		= JFactory::getUser();
$lang 		= JFactory::getLanguage();

JHtml::_('bootstrap.framework');

// Component Parameters
$yooRecipeparams 			= JComponentHelper::getParams('com_yoorecipe');
$pagination_position		= $yooRecipeparams->get('pagination_position', 'bottom');
$can_show_price				= $yooRecipeparams->get('show_price', 0);
$currency					= $yooRecipeparams->get('currency', '&euro;');
$show_add_recipe_button		= $yooRecipeparams->get('show_add_recipe_button', 1);
$recipes_layout				= $yooRecipeparams->get('recipes_layout', 'default');

// Add styles and JS
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
?>

<div class="yoorecipe-top-myrecipes">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-top-myrecipes');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>

<h1><?php echo JText::_('COM_YOORECIPE_YOUR_RECIPES'); ?></h1>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">

<div class="row-fluid">
<?php
	if ($show_add_recipe_button) {
		echo JHtml::_('yoorecipeutils.generateAddRecipeButton');
	}
?>
	<div id="filter-bar" class="btn-toolbar pull-right">
	
		<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
			<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_yoorecipe'), 'value', 'text', $this->state->get('filter.category_id'));?>
		</select>
		<div class="filter-search btn-group">
			<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER_LABEL');?></label>
			<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" />
		</div>
		<div class="btn-group hidden-phone">
			<button class="btn tip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button class="btn tip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
		</div>
	</div>
</div>

<?php
if (count($this->items) > 0) {
	
	if ($pagination_position == 'top' || $pagination_position == 'both') {
		echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
	}
	
	$recipes_layout = new JLayoutFile($recipes_layout, $basePath = JPATH_ROOT .'/components/com_yoorecipe/layouts/recipes');
	
	$display_data = array();
	$display_data['items'] = $this->items;

	$display_data['blog_is_picture_clickable']  = $yooRecipeparams->get('blog_is_picture_clickable', 1);
	$display_data['blog_show_title']			= $yooRecipeparams->get('blog_show_title', 1);
	$display_data['blog_show_creation_date'] 	= $yooRecipeparams->get('blog_show_creation_date', 0);
	$display_data['blog_show_ingredients'] 		= $yooRecipeparams->get('blog_show_ingredients', 0);
	$display_data['blog_show_readmore'] 		= $yooRecipeparams->get('blog_show_readmore', 0);
	$display_data['blog_show_description']		= $yooRecipeparams->get('blog_show_description', 1);
	$display_data['blog_show_cuisine']			= $yooRecipeparams->get('blog_show_cuisine', 1);
	$display_data['blog_show_author'] 			= false;
	$display_data['blog_show_nb_views'] 		= $yooRecipeparams->get('blog_show_nb_views', 0);
	$display_data['blog_show_category_title'] 	= $yooRecipeparams->get('blog_show_category_title', 1);
	$display_data['blog_show_difficulty'] 		= $yooRecipeparams->get('blog_show_difficulty', 0);
	$display_data['blog_show_cost']				= $yooRecipeparams->get('blog_show_cost', 0);
	$display_data['blog_show_rating'] 			= $yooRecipeparams->get('blog_show_rating', 1);
	$display_data['blog_rating_style'] 			= $yooRecipeparams->get('blog_rating_style', 'stars');
	$display_data['blog_show_preparation_time'] = $yooRecipeparams->get('blog_show_preparation_time', 1);
	$display_data['blog_show_cook_time'] 		= $yooRecipeparams->get('blog_show_cook_time', 1);
	$display_data['blog_show_wait_time'] 		= $yooRecipeparams->get('blog_show_wait_time', 1);
	$display_data['blog_show_seasons']	 		= $yooRecipeparams->get('blog_show_seasons', 1);
	$display_data['blog_show_pending_recipes']	= true;
	
	echo '<div class="yoorecipe-cont-results">';
	echo $recipes_layout->render($display_data);
	echo'</div>';
	
	if ($pagination_position == 'bottom' || $pagination_position == 'both') {
		echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
	}
}
?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="returnPage" value="<?php echo JUri::current(); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>