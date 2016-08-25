<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$jinput = JFactory::getApplication()->input;

$option = $jinput->getCmd('option'); // This gets the component
$view   = $jinput->getCmd('view');   // This gets the view
$layout = $jinput->getCmd('layout'); // This gets the view's layout

?>

<?php if ($option == 'com_content' && $view == 'healthnews' && $layout == 'blog') : ?>
			<dd class="published">
				<time datetime="<?php echo JHtml::_('date', $displayData['item']->publish_up, 'c'); ?>" itemprop="datePublished">
					<?php echo JHtml::_('date', $displayData['item']->publish_up, JText::_('DATE_FORMAT_LC3')); ?>
				</time>
			</dd>
<?php endif; ?>