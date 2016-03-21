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

$document->addStyleSheet('media/com_yoorecipe/styles/jquery.fancybox.css');
$document->addScript('media/com_yoorecipe/js/jquery.fancybox.js');

// Init variables
$input	 	= JFactory::getApplication()->input;
$user 		= JFactory::getUser();
$recipe 	= $this->recipe;
$is_printing = $input->get('print', 0, 'INT');

// Component Parameters
$yooRecipeparams 			= JComponentHelper::getParams('com_yoorecipe');
$use_mealplanner			= $yooRecipeparams->get('use_mealplanner', 1);
$use_shoppinglist			= $yooRecipeparams->get('use_shoppinglist', 1);
$use_lightbox				= $yooRecipeparams->get('use_lightbox', 1);

$use_social_sharing					= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'use_social_sharing', 1);
$show_social_bookmarks_on_top		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_on_top', 1);

$can_show_print_icon		= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_print_icon', 1);
$show_email_icon			= JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_email_icon', 1);

$picture_path 	= JHtml::_('imageutils.getPicturePath', $recipe->picture);

$editUrl 		= JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit&id='.$recipe->slug); 

$picture_html = '<img class="thumbnail photo" itemprop="image" src="'.$picture_path.'" alt="'.htmlspecialchars($recipe->title).'"/>';
if ($use_lightbox) {
	$picture_html = '<a class="fancybox" rel="group" href="'.$picture_path.'">'.$picture_html.'</a>';
}
?>
<br/>
<?php echo $picture_html; ?>

<?php
if ($use_social_sharing && $show_social_bookmarks_on_top) {
	echo '<div>',JHtml::_('yoorecipeutils.socialSharing', $yooRecipeparams, $recipe),'</div>';
}
?>

<ul class="yoorecipe-actions unstyled">
<?php
if ($use_mealplanner) {
			
	$html = array();
	if (!$user->guest) {
		$html[] = '<li id="mealplanner_'.$recipe->id.'">';
		$html[] = ($recipe->is_queued) ? '<a href="#" onclick="removeRecipeFromQueue('.$recipe->id.');" ><i class="icon-minus"></i>' : '<a href="#" onclick="addRecipeToQueue('.$recipe->id.');"><i class="icon-plus"></i>';
		$html[] = JText::_('COM_YOORECIPE_RECIPE_BOX');
		$html[] ='</a>';
		$html[] = '</li>';
	}
	echo implode("\n", $html);
}

if($use_shoppinglist) {
	if (!$user->guest){
		echo '<li><a href="#" onclick="loadAddToShoppingListModal();"><i class="icon-plus"></i> '.JText::_('COM_YOORECIPE_SHOPPINGLIST').'</a></li>';
	}
}

if (!$is_printing && $show_email_icon) {
	echo '<li>'.JHtml::_('yoorecipeicon.email', $recipe, $yooRecipeparams).'</li>';
}

if (!$is_printing && $can_show_print_icon) {
	echo '<li>'.JHtml::_('yoorecipeicon.print_popup', $recipe, $yooRecipeparams).'</li>';
}

if (!$user->guest && $yooRecipeparams->get('use_favourites', 1) == 1 ) {
	echo '<li id="fav_'.$recipe->id.'"> '.JHtml::_('yoorecipeicon.favourites', $recipe, $yooRecipeparams).'</li>';
}

if ($this->canEdit && !$is_printing) {
	echo '<li><a href="#" onclick="window.location=\''.$editUrl.'\';return false;"><i class="icon-edit"></i> '.JText::_('COM_YOORECIPE_EDIT').'</a></li>';
}

echo '</ul>';
