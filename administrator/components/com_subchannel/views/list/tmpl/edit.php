<?php
/**
 * @version     1.0.2
 * @package     com_subchannel
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_subchannel/assets/css/subchannel.css');


$db = JFactory::getDBO(); 

if (empty($this->item->category_id)) $this->item->category_id=1;

$query = 'SELECT `path` FROM `#__categories` 
	  		WHERE `#__categories`.`id`='.$this->item->category_id;
$db->setQuery(true);
$db->setQuery($query);
$db->execute();
unset($query);
$path = $db->loadObjectList();

if (! empty($this->item->category_id)) {
	$query = 'SELECT `#__content`.`id`,`#__content`.`title` FROM `#__content` 
		LEFT JOIN `#__categories` ON `#__categories`.`id`=`#__content`.`catid`
	  	WHERE `path` like "'.$path[0]->path.'%" AND `state`=1 AND `featured`=1 ORDER BY title';
	$db->setQuery(true);
	$db->setQuery($query);
	$db->execute();
	unset($query);

	$articles = $db->loadObjectList();
}


?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function() {
        
	js('input:hidden.category_id').each(function(){
		var name = js(this).attr('name');
		if(name.indexOf('category_idhidden')){
			js('#jform_category_id option[value="'+js(this).val()+'"]').attr('selected',true);
		}
	});
	js("#jform_category_id").trigger("liszt:updated");
    });

    Joomla.submitbutton = function(task)
    {
        if (task == 'list.cancel') {
            Joomla.submitform(task, document.getElementById('list-form'));
        }
        else {
            
            if (task != 'list.cancel' && document.formvalidator.isValid(document.id('list-form'))) {
                
                Joomla.submitform(task, document.getElementById('list-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_subchannel&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="list-form" class="form-validate">

    <div class="form-horizontal">
        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_SUBCHANNEL_TITLE_LIST', true)); ?>
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">

                    				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
				<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
				<input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>" />
				<input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>" />
				<input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>" />

				<?php if(empty($this->item->created_by)){ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

				<?php } ?>			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('category_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('category_id'); ?></div>
			</div>

			<?php
				foreach((array)$this->item->category_id as $value): 
					if(!is_array($value)):
						echo '<input type="hidden" class="category_id" name="jform[category_idhidden]['.$value.']" value="'.$value.'" />';
					endif;
				endforeach;
			?>

			<?php

			for ($i=0; $i < 20; $i++) { ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'.$i); ?></div>
					<!-- <div class="controls"><?php echo $this->form->getInput('id'.$i); ?></div> -->
					<div class="controls">
						<select name="jform[id<?php echo $i; ?>]">
							<?php foreach ($articles as $key => $article) {
								echo '<option value=""> </option>';
								echo '<option value="'.$article->id.'" ';
								$spec_id = 'id'.$i;
								if ($article->id == $this->item->$spec_id) echo 'selected="selected"';
								echo '>'.$article->title.'</option>';
							} ?>
		                </select>
            		</div>
				</div>
			<?php


			}


			?>
			

			<!-- <div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id0'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id0'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id1'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id1'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id2'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id2'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id3'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id3'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id4'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id4'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id5'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id5'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id6'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id6'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id7'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id7'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id8'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id8'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id9'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id9'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id10'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id10'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id11'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id11'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id12'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id12'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id13'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id13'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id14'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id14'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id15'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id15'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id16'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id16'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id17'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id17'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id18'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id18'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id19'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id19'); ?></div>
			</div> -->


                </fieldset>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>
        
        

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>