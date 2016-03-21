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
$yooRecipeparams 		= JComponentHelper::getParams('com_yoorecipe');
$use_watermark			= $yooRecipeparams->get('use_watermarks' , 1);
$can_show_price			= $yooRecipeparams->get('show_price', 0);
$currency				= $yooRecipeparams->get('currency', '&euro;');
$recipes_layout			= $yooRecipeparams->get('recipes_layout', 'default');

// Landing page parameters
$canBrowseByLetter				= (isset($this->menuParams)) ? $this->menuParams->get('browse_by_letter', 1) : 1;
$canBrowseByCategories			= (isset($this->menuParams)) ? $this->menuParams->get('browse_by_categories', 1) : 1;
$show_sub_categories_picture	= (isset($this->menuParams)) ? $this->menuParams->get('show_sub_categories_picture', 1) : 1;
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
<div class="pull-left">
<?php if ($this->menuParams->get('show_page_heading', 1)) { ?>
	<h1> <?php echo $this->escape($this->menuParams->get('page_heading')); ?> </h1>
<?php } else {?>
	<h1> <?php echo JText::_('COM_YOORECIPE_LANDING_PAGE'); ?> </h1>
<?php }?>
</div>
<form action="<?php echo JFilterOutput::ampReplace(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">

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

	if (array_ereg('[0-9]' , $this->recipeStartLetters)) {
		echo '<a title="#" href="index.php?option=com_yoorecipe&view=landingpage&layout=letters&l=dash">#</a>';
	} else {
		echo '<span title="#">#</span>';
	}
	foreach ($alphabet as $tmpLetter) :
		
		if (in_array($tmpLetter , $this->recipeStartLetters)) {
			echo '<a title="'.$tmpLetter.'" href="index.php?option=com_yoorecipe&view=landingpage&layout=letters&l='.urlencode($tmpLetter).'">'.$tmpLetter.'</a>';
		} else {
			echo '<span title="'.$tmpLetter.'">'.$tmpLetter.'</span>';
		}
	endforeach;
?>
</div>
</div>
<hr/>

<h2><?php echo JText::sprintf('COM_YOORECIPE_YOORECIPE_START_WITH', $this->crtLetter); ?></h2>

<?php 
	if ($show_recipes && $this->items) {
	
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
		
	} // End if ($this->items) {
?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="returnPage" value="<?php echo JUri::current(); ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>