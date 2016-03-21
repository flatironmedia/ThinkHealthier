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

// no direct access
defined('_JEXEC') or die;

JHtml::_('bootstrap.framework');

// Get Factories
$user 		= JFactory::getUser();
$document	= JFactory::getDocument();

// Component Parameters
$yooRecipeparams 		= JComponentHelper::getParams('com_yoorecipe');
$pagination_position	= $yooRecipeparams->get('pagination_position', 'bottom');
$can_show_price			= $yooRecipeparams->get('show_price', 0);
$currency				= $yooRecipeparams->get('currency', '&euro;');
$recipes_layout			= $yooRecipeparams->get('recipes_layout', 'default');

$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
?>
<h1><?php echo JText::_('COM_YOORECIPE_SEARCH_RECIPE'); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=search&layout=results', false); ?>" method="post" name="adminForm" id="adminForm">
<?php

echo '<div class="yoorecipe-before-search">';
$modules = JModuleHelper::getModules('yoorecipe-before-search');
foreach($modules as $module) {
	echo JModuleHelper::renderModule($module);
}
echo '</div>';
	
if ($pagination_position == 'top' || $pagination_position == 'both') {
	echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
}

echo '<p>',$this->pagination->getResultsCounter(),'</p>';
echo '<p><a href="',JRoute::_('index.php?option=com_yoorecipe&option=com_yoorecipe&view=search&layout=search', false),'">',JText::_('COM_YOORECIPE_NEW_SEARCH'),'</a></p>';

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
$display_data['blog_show_author'] 			= $yooRecipeparams->get('blog_show_author', 1);
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

?>
	<div>
		<input type="hidden" name="task" value="search" />
		<input type="hidden" name="searchword" value="<?php echo $this->state->get('filter.searchword') ; ?>"/>
	<?php foreach ($this->withIngredients as $with_ingredient) { ?>
		<input type="hidden" name="withIngredients[]" value="<?php echo $with_ingredient; ?>"/>
	<?php } ?>
	<?php foreach ($this->withoutIngredients as $withoutIngredient) { ?>
		<input type="hidden" name="withoutIngredients[]" value="<?php echo $withoutIngredient; ?>"/>
	<?php } ?>
	<?php foreach ($this->searchCategories as $search_category) { ?>
		<input type="hidden" name="searchCategories[]" value="<?php echo $search_category; ?>"/>
	<?php } ?>
		<input type="hidden" name="searchSeasons" value="<?php echo $this->state->get('filter.searchSeasons'); ?>"/>
		<input type="hidden" name="searchPerformed" value="<?php echo $this->state->get('filter.searchPerformed'); ?>"/>
		
		<input type="hidden" name="search_author" value="<?php echo $this->state->get('filter.search_author'); ?>"/>
		<input type="hidden" name="search_max_prep_hours" value="<?php echo $this->state->get('filter.search_max_prep_hours'); ?>"/>
		<input type="hidden" name="search_max_prep_minutes" value="<?php echo $this->state->get('filter.search_max_prep_minutes'); ?>"/>
		<input type="hidden" name="search_max_cook_hours" value="<?php echo $this->state->get('filter.search_max_cook_hours'); ?>"/>
		<input type="hidden" name="search_max_cook_minutes" value="<?php echo $this->state->get('filter.search_max_cook_minutes'); ?>"/>
		<input type="hidden" name="search_min_rate" value="<?php echo $this->state->get('filter.search_min_rate'); ?>"/>
		<input type="hidden" name="search_max_cost" value="<?php echo $this->state->get('filter.search_max_cost'); ?>"/>
		
		<input type="hidden" name="search_operator_price" value="<?php echo $this->state->get('filter.search_operator_price'); ?>"/>
		<input type="hidden" name="search_price" value="<?php echo $this->state->get('filter.search_price'); ?>"/>
		<input type="hidden" name="search_type_diet" value="<?php echo $this->state->get('filter.search_type_diet'); ?>"/>
		<input type="hidden" name="search_type_veggie" value="<?php echo $this->state->get('filter.search_type_veggie'); ?>"/>
		<input type="hidden" name="search_type_glutenfree" value="<?php echo $this->state->get('filter.search_type_glutenfree'); ?>"/>
		<input type="hidden" name="search_type_lactosefree" value="<?php echo $this->state->get('filter.search_type_lactosefree'); ?>"/>
		
		<input type="hidden" name="returnPage" value="<?php echo JUri::current(); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
<?php 
echo '<div class="yoorecipe-after-search">';
$modules = JModuleHelper::getModules('yoorecipe-after-search');
foreach($modules as $module) {
	echo JModuleHelper::renderModule($module);
}
echo '</div>';
	
if ($pagination_position == 'bottom' || $pagination_position == 'both') { 
	echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
}
?>
</form>