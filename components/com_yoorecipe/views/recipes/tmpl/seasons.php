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
$recipes_layout				= $yooRecipeparams->get('recipes_layout', 'default');

// Add styles and JS
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
?>
<h1><?php echo JText::sprintf('COM_YOORECIPE_RECIPES_OF_SEASON', JText::_('COM_YOORECIPE_'.$this->month_id));?></h1>

<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">

<?php
if (count($this->items) == 0) {
	echo JHtml::_('yoorecipeutils.generateCategoriesList', $this->categories);
}
else {
	
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
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="returnPage" value="<?php echo JUri::current(); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
	
<?php 
	if ($pagination_position == 'bottom' || $pagination_position == 'both') {
		echo JHtml::_('yoorecipeutils.generatePagination', $this->pagination);
	}
}
?>
</form>