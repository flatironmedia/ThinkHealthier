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
JHtmlBehavior::framework();

$document	= $document = JFactory::getDocument();
$params 	= JComponentHelper::getParams('com_yoorecipe');
?>
<?php echo $this->form->renderField('published'); ?>
<?php echo $this->form->renderField('validated'); ?>
<?php echo $this->form->renderField('featured'); ?>
<?php echo $this->form->renderField('price'); ?>
<?php echo $this->form->renderField('seasons'); ?>

<?php echo $this->form->renderField('tags'); ?>

<?php echo $this->form->renderField('difficulty'); ?>
<?php echo $this->form->renderField('cost'); ?>

<div class="control-group">
	<?php echo $this->form->renderField('preparation_time'); ?>
</div>
<div class="control-group">
	<?php echo $this->form->renderField('cook_time'); ?>
</div>
<div class="control-group">
	<?php echo $this->form->renderField('wait_time'); ?>
</div>

<?php echo $this->form->renderField('picture'); ?>
<?php echo $this->form->renderField('video'); ?>