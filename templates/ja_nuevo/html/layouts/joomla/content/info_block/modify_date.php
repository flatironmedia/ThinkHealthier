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
			<dd class="modified">
				<time datetime="<?php echo JHtml::_('date', $displayData['item']->modified, 'c'); ?>" itemprop="dateModified">
					<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $displayData['item']->modified, JText::_('DATE_FORMAT_LC3'))); ?>
				</time>
			</dd>
<?php endif; ?>