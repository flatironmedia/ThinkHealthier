<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;
$item = $displayData['item'];
?>
<?php if($item->featured) : ?>
    <div class="alert alert-success">
        <span class="icon-star"></span> <?php echo JText::_('JFEATURED'); ?>
    </div>
<?php endif; ?>