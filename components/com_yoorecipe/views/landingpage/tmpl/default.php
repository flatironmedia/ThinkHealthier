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

// Get factories
$document 	= JFactory::getDocument();
$user 		= JFactory::getUser();
$lang 		= JFactory::getLanguage();

JHtml::_('bootstrap.framework');

// Component Parameters
$yooRecipeparams 		= JComponentHelper::getParams('com_yoorecipe');
$pagination_position	= $yooRecipeparams->get('pagination_position', 'bottom');
$can_show_price			= $yooRecipeparams->get('show_price', 0);
$currency				= $yooRecipeparams->get('currency', '&euro;');
$use_rss_feeds	 		= $yooRecipeparams->get('use_rss_feeds', 1);
$recipes_layout			= $yooRecipeparams->get('recipes_layout', 'default');

// Landing page parameters
$canBrowseByLetter				= (isset($this->menuParams)) ? $this->menuParams->get('browse_by_letter', 1) : 1;
$canBrowseByCategories			= (isset($this->menuParams)) ? $this->menuParams->get('browse_by_categories', 1) : 1;
$show_sub_categories_picture	= (isset($this->menuParams)) ? $this->menuParams->get('show_sub_categories_picture', 1) : 1;
$show_add_recipe_button			= (isset($this->menuParams)) ? $this->menuParams->get('show_add_recipe_button', 1) : $yooRecipeparams->get('show_add_recipe_button', 1);
$show_recipes					= (isset($this->menuParams)) ? $this->menuParams->get('show_recipes', 1) : 1;

// Add styles and JS
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');
$document->addScript('media/com_yoorecipe/js/generic.js');
?>
<div class="yoorecipe-top-landingpage">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-top-landingpage');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>
<div class="item-page<?php echo $this->pageclass_sfx; ?>" itemscope itemtype="http://schema.org/Article">
	<div class="pull-left">
	<?php if ($this->menuParams->get('show_page_heading', 1)) { ?>
		<h1> <?php echo $this->escape($this->menuParams->get('page_heading')); ?> </h1>
	<?php } else {?>
		<h1> <?php echo JText::_('COM_YOORECIPE_LANDING_PAGE'); ?> </h1>
	<?php }?>
	</div>
	
	<?php if ($use_rss_feeds) { ?>
	<div class="pull-right">
		<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=landingpage&format=feed&type=rss'); ?>">
			<?php echo JHtml::_('image', 'system/livemarks.png', 'feed-image', null, true); ?>
		</a>
	</div>
	<?php } ?>
	<div class="clearfix"></div>
	
	<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
	<?php if ($canBrowseByLetter) : ?>
	<div class="alpha-index-container">
	<div class="alpha-index">
	<?php
		$lang = JFactory::getLanguage();
		$alphabet = JHtml::_('langutils.generateAlphabet', $lang->getTag());
		
		function array_ereg($pattern, $haystack)
		{
		   for($i = 0; $i < count($haystack); $i++)
		   {
			   if (preg_match($pattern, $haystack[$i]))
				   return true;
		   }

		   return false;
		} 

		if (array_ereg('[0-9]', $this->recipeStartLetters)) {
			echo '<a title="#" href="index.php?option=com_yoorecipe&view=landingpage&layout=letters&l=dash">#</a>';
		} else {
			echo '<span title="#">#</span>';
		}
		foreach ($alphabet as $tmpLetter) :
			
			if (in_array($tmpLetter, $this->recipeStartLetters)) {
				echo '<a title="'.$tmpLetter.'" href="index.php?option=com_yoorecipe&view=landingpage&layout=letters&l='. urlencode($tmpLetter). '">'.$tmpLetter.'</a>';
			} else {
				echo '<span title="'.$tmpLetter.'">'.$tmpLetter.'</span>';
			}
		endforeach;
	?>
	</div>
	</div>
	<hr/>
	<?php endif; ?>
	<?php
	if ($canBrowseByCategories && count($this->subcategories) > 0) {

		echo '<div>';
		echo '<h2>'.JText::_('COM_YOORECIPE_BROWSE_BY_CATEGORY').'</h2>';
		echo '<div class="comyoorecipe-cat-browse">';
		echo JHtml::_('yoorecipeutils.generateSubCategoriesMosaic', $this->subcategories, $show_sub_categories_picture);
		echo '</div>';
		echo '</div>';

		echo '<div class="clearfix" > </div>';
		echo '<hr/>';
		
		echo '<div class="yoorecipe-after-subcategories">';
		$modules = JModuleHelper::getModules('yoorecipe-after-subcategories');
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}

	if ($show_recipes && count($this->items) > 0) { 
		
		echo '<h2>'.JText::_($this->sectionLabel).'</h2>';
	?>
	<div class="row-fluid">
	<?php
		if ($show_add_recipe_button) {
			echo JHtml::_('yoorecipeutils.generateAddRecipeButton');
			echo '<br/>';
		}
	?>
	</div>
	<?php 
		
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
	} // End if (count($this->items) > 0) { 
	?>
	
<?php
	$modules = JModuleHelper::getModules('yoorecipe-bottom-landingpage');
	if (count($modules) > 0) {
		echo '<div class="yoorecipe-bottom-landingpage">';
		foreach($modules as $module) {
			echo JModuleHelper::renderModule($module);
		}
		echo '</div>';
	}
?>
	</form>
</div>