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
$use_rss_feeds	 			= $yooRecipeparams->get('use_rss_feeds', 1);
$nb_cols	 				= $yooRecipeparams->get('nb_cols', 4);
$blog_is_picture_clickable 	= $yooRecipeparams->get('blog_is_picture_clickable', 1);
$blog_show_rating 			= $yooRecipeparams->get('blog_show_rating', 1);
$blog_rating_style 			= $yooRecipeparams->get('blog_rating_style', 'stars');
$blog_show_category_title 	= $yooRecipeparams->get('blog_show_category_title', 1);
$recipes_layout				= $yooRecipeparams->get('recipes_layout', 'default');

// Add styles and JS
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
$document->addScript('media/com_yoorecipe/js/jquery.masonry.min.js');
?>

<div class="yoorecipe-top-wall">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-top-wall');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<ul id="search_filter" class="nav nav-tabs">
		<li>
			<div class="input-append">
				<input type="text" class="input-medium" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" />
				<span class="add-on"><a href="#" onclick="$('adminForm').submit();return false;"><i class="icon-search"></i></a></span>
				<span class="add-on"><a href="#" onclick="$('filter_search').value='';$('adminForm').submit();return false;"><i class="icon-delete"></i></a></span>
			</div>
		</li>

		<li>
			<select name="category_id" class="input-medium" onchange="$('adminForm').submit();">
			<option value="*" selected="selected"><?php echo '- '.JText::_('COM_YOORECIPE_CATEGORY').' -';?></option>
			<?php echo JHtml::_('select.options', JHtml::_('optionsutils.categoriesOptions'), 'value', 'text', $this->state->get('filter.category_id')); ?>
			</select>
			
		</li>
		<li>
			<select name="cuisine" class="input-medium" onchange="$('adminForm').submit();">
				<option value=""><?php echo '- '.JText::_('COM_YOORECIPE_CUISINE').' -';?></option>
				<?php echo JHtml::_('select.options', JHtml::_('optionsutils.cuisineOptions'), 'value', 'text', $this->state->get('filter.cuisine')); ?>
			</select>
		</li>
		<li>
			<select name="recipe_time" class="input-medium" onchange="$('adminForm').submit();">
				<option value=""><?php echo '- '.JText::_('COM_YOORECIPE_RECIPE_TIME').' -';?></option>
				<?php echo JHtml::_('select.options', JHtml::_('optionsutils.recipeTimeOptions'), 'value', 'text', $this->state->get('filter.recipe_time')); ?>
			</select>
		</li>
		<li>
			<select name="order_col" class="input-medium" onchange="$('adminForm').submit();">
				<option value="title"><?php echo '- '.JText::_('COM_YOORECIPE_SORT_ORDER').' -';?></option>
				<?php echo JHtml::_('select.options', JHtml::_('optionsutils.orderOptions'), 'value', 'text', $this->state->get('filter.order_col')); ?>
			</select>
		</li>
	</ul>
	
	<input type="hidden" value="<?php echo $nb_cols; ?>" id="nb_cols"/>
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
	$display_data['blog_show_pending_recipes']	= false;
	
	echo '<div class="yoorecipe-cont-results">';
	echo $recipes_layout->render($display_data);
	echo'</div>';

	echo '<div class="clearfix"></div>';
	if ($pagination_position == 'bottom' || $pagination_position == 'both') {
		echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
	}
	
} // End if (count($this->items) > 0) {
?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="returnPage" value="<?php echo JUri::current(); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>