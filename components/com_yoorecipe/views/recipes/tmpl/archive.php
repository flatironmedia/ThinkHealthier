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

$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe.css');

// Component Parameters
$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
$use_rss_feeds	 	= $yooRecipeparams->get('use_rss_feeds', 1);
?>
<div class="yoorecipe-top-archives">
<?php
	$modules = JModuleHelper::getModules('yoorecipe-top-archives');
	foreach($modules as $module) {
		echo JModuleHelper::renderModule($module);
	}
?>
</div>
<div class="pull-left">
	<h1><?php echo JText::_('COM_YOORECIPE_ARCHIVES_LIST'); ?></h1>
</div>

<?php if ($use_rss_feeds) { ?>
<div class="pull-right">
	<a href="<?php echo JRoute::_('index.php?option=com_yoorecipe&view=recipes&layout=archive&format=feed&type=rss'); ?>">
		<?php echo JHtml::_('image', 'system/livemarks.png', 'feed-image', null, true); ?>
	</a>
</div>
<?php } ?>
<div class="clear">&nbsp;</div>
	
<?php
if (count($this->items) > 0) { 

	$cnt = 1;
	$crtLetter = mb_substr($this->items[0]->title, 0, 1, 'utf-8');

	$html = array();
	$html[] = '<div class="yoorecipe-cont-results">';
	$html[] = '<div class="dropcap">'.$crtLetter.'</div>';
	$html[] = '<ul class="unstyled" >';
	
	foreach($this->items as $recipe) {
	
		if ($crtLetter != mb_substr($recipe->title, 0, 1, 'utf-8')) {
			$crtLetter = mb_substr($recipe->title, 0, 1, 'utf-8');
			$html[] = '</ul>';
			$html[] = '<div class="dropcap">'.$crtLetter.'</div>';
			$html[] = '<ul class="unstyled" >';
		}
		
		$url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $recipe->slug, $recipe->catslug) , false);
		$html[] = '<li>';
		$html[] = '<a href="'.$url.'" title="'.htmlspecialchars($recipe->title).'" target="_self">';
		$html[] = htmlspecialchars($recipe->title);
		$html[] = '</a>';
		$html[] = '</li>';
	}
	
	$html[] = '</ul>';
	$html[] = '</div>';
	
	echo implode("\n", $html);
	
} // End if (count($this->items) > 0) {