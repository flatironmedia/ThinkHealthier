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

defined('_JEXEC') or die;

$is_new	= $this->item->id == 0 ? true : false;
$lang 	= JFactory::getLanguage();
?>
<h2><?php echo JText::_('COM_YOORECIPE_YOORECIPE_MODERATION'); ?></h2>
<div class="alert alert-info"><?php echo JText::_('COM_YOORECIPE_SHOWN_TO_ADMIN_ONLY'); ?></div>
<?php echo $this->form->renderField('published'); ?>
<?php echo $this->form->renderField('validated'); ?>
<?php echo $this->form->renderField('featured'); ?>
<?php echo $this->form->renderField('access'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('creation_date'); ?>
<?php echo $this->form->renderField('publish_up'); ?>
<?php echo $this->form->renderField('publish_down'); ?>
<?php echo $this->form->renderField('nb_views'); ?>
<?php echo $this->form->renderField('alias'); ?>
<?php echo $this->form->renderField('metakey'); ?>
<?php echo $this->form->renderField('metadata'); ?>