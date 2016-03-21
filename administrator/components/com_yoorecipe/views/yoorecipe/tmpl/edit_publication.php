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
?>
<?php echo $this->form->renderField('access'); ?>
<?php echo $this->form->renderField('created_by'); ?>
<?php echo $this->form->renderField('creation_date'); ?>
<?php echo $this->form->renderField('publish_up'); ?>
<?php echo $this->form->renderField('publish_down'); ?>
<?php echo $this->form->renderField('nb_views'); ?>