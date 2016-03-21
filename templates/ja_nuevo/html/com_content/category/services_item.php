<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.framework');
JLoader::register('NuevoHelper',T3_TEMPLATE_PATH.'/templateHelper.php');

// Create a shortcut for params.
$params  = & $this->item->params;
$images  = json_decode($this->item->images);
$info    = $params->get('info_block_position', 2);
$aInfo1 = ($params->get('show_publish_date') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author'));
$aInfo2 = ($params->get('show_create_date') || $params->get('show_modify_date') || $params->get('show_hits'));
$topInfo = ($aInfo1 && $info != 1) || ($aInfo2 && $info == 0);
$botInfo = ($aInfo1 && $info == 1) || ($aInfo2 && $info != 0);
$icons = $params->get('access-edit') || $params->get('show_print_icon') || $params->get('show_email_icon');

// update catslug if not exists - compatible with 2.5
if (empty ($this->item->catslug)) {
  $this->item->catslug = $this->item->category_alias ? ($this->item->catid.':'.$this->item->category_alias) : $this->item->catid;
}
?>

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
|| ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != '0000-00-00 00:00:00' )) : ?>
<div class="system-unpublished">
<?php endif; ?>

	<!-- Article -->
	<article>
			<?php 
				$iconTitle = NuevoHelper::loadParamsContents($this->item);
				$inner = $iconTitle['icon_titles']?'<i class="fa '.$iconTitle['icon_titles'].'"></i>':'<i class="fa fa-question"></i>';
				echo $inner;
			?>
			
			<section class="article-intro clearfix" itemprop="articleBody">

				<?php if ($params->get('show_title')) : ?>
					<?php echo JLayoutHelper::render('joomla.content.item_title', array('item' => $this->item, 'params' => $params, 'title-tag'=>'h2')); ?>
				<?php endif; ?>
				
				<?php if (!$params->get('show_intro')) : ?>
					<?php echo $this->item->event->afterDisplayTitle; ?>
				<?php endif; ?>

				<?php echo $this->item->event->beforeDisplayContent; ?>

				<div class="service-intro"><?php echo $this->item->introtext; ?></div>
			</section> 
		<?php if ($params->get('show_readmore') && $this->item->readmore) :
						if ($params->get('access-view')) :
							$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
						else :
							$menu = JFactory::getApplication()->getMenu();
							$active = $menu->getActive();
							$itemId = $active->id;
							$link1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
							$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
							$link = new JURI($link1);
							$link->setVar('return', base64_encode(urlencode($returnURL)));
						endif;
					?>
							<p>
									<a class="btn btn-link" href="<?php echo $link; ?>">
										<?php if (!$params->get('access-view')) :
											echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
										elseif ($readmore = $this->item->alternative_readmore) :
											echo $readmore;
											if ($params->get('show_readmore_title', 0) != 0) :
											    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
											endif;
										elseif ($params->get('show_readmore_title', 0) == 0) :
											echo JText::sprintf('TPL_COM_CONTENT_READ_MORE_TITLE');
										else :
											echo JText::_('COM_CONTENT_READ_MORE');
											echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
										endif; ?></a>
							</p>
					<?php endif; ?>

	</article>
	<!-- //Article -->

<?php if ($this->item->state == 0 || strtotime($this->item->publish_up) > strtotime(JFactory::getDate())
|| ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != '0000-00-00 00:00:00' )) : ?>
</div>
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?> 
