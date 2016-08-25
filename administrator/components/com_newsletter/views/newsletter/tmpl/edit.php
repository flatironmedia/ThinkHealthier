<?php
/**
 * @version    CVS: 3.4.2
 * @package    Com_Newsletter
 * @author     Aleksandar Vrhovac <avrhovac@ogosense.com>
 * @copyright  2016 Aleksandar Vrhovac
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'media/com_newsletter/css/form.css');
?>
<script type="text/javascript">
	js = jQuery.noConflict();
	js(document).ready(function () {
		
	});

	Joomla.submitbutton = function (task) {
		if (task == 'newsletter.cancel') {
			Joomla.submitform(task, document.getElementById('newsletter-form'));
		}
		else {
			
			if (task != 'newsletter.cancel' && document.formvalidator.isValid(document.id('newsletter-form'))) {
				
				Joomla.submitform(task, document.getElementById('newsletter-form'));
			}
			else {
				alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<script type="text/javascript">
	function jSelectArticle(id, title, catid, fieldName, fieldID, lang)
			{
				document.getElementById(fieldName).value = title;
				document.getElementById(fieldID).value = id;
				jModalClose();
			}

</script>

<form
	action="<?php echo JRoute::_('index.php?option=com_newsletter&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="newsletter-form" class="form-validate">

<div class="form-horizontal">
	<div class="row-fluid">
		<div class="span10 form-horizontal">
			<fieldset class="adminform">

				<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />

				<?php if(empty($this->item->created_by)){ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[created_by]" value="<?php echo $this->item->created_by; ?>" />

				<?php } ?>
				<?php if(empty($this->item->modified_by)){ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo JFactory::getUser()->id; ?>" />

				<?php } 
				else{ ?>
					<input type="hidden" name="jform[modified_by]" value="<?php echo $this->item->modified_by; ?>" />

				<?php } ?>
				<br />
				<br />
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('date'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('date'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('subject'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('subject'); ?></div>
				</div>				
				<br />
				<br />
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('featured_article_name'); ?></div>
					<div class="controls">
						<?php echo $this->form->getInput('featured_article_name'); ?>

						<a class="modal" href="index.php?option=com_content&view=articles&layout=modal_newsletter2&tmpl=component&field_name=jform_featured_article_name&field_id=jform_featured_article" rel="{handler: 'iframe', size: {x: 800, y: 550}}">Select article</a>
					
					</div>
					<div class="control-label"><?php echo $this->form->getLabel('featured_article'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('featured_article'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('featured_recipe_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('featured_recipe_name'); ?>
						<a class="modal" href="index.php?option=com_yoorecipe&view=yoorecipes&layout=modal_newsletter&tmpl=component&field_name=jform_featured_recipe_name&field_id=jform_featured_recipe" rel="{handler: 'iframe', size: {x: 1200, y: 550}}">Select recipe</a>
					</div>
					<div class="control-label"><?php echo $this->form->getLabel('featured_recipe'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('featured_recipe'); ?></div>
				</div>
				<br />
				<br />
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('article2_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article2_name'); ?>
						<a class="modal" href="index.php?option=com_content&view=articles&layout=modal_newsletter2&tmpl=component&field_name=jform_article2_name&field_id=jform_article2" rel="{handler: 'iframe', size: {x: 800, y: 550}}">Select article</a>
					</div>
					<div class="control-label"><?php echo $this->form->getLabel('article2'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article2'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('article3_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article3_name'); ?>
						<a class="modal" href="index.php?option=com_content&view=articles&layout=modal_newsletter2&tmpl=component&field_name=jform_article3_name&field_id=jform_article3" rel="{handler: 'iframe', size: {x: 800, y: 550}}">Select article</a>
					</div>
					<div class="control-label"><?php echo $this->form->getLabel('article3'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article3'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('article4_name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article4_name'); ?>
						<a class="modal" href="index.php?option=com_content&view=articles&layout=modal_newsletter2&tmpl=component&field_name=jform_article4_name&field_id=jform_article4" rel="{handler: 'iframe', size: {x: 800, y: 550}}">Select article</a>
					</div>
					<div class="control-label"><?php echo $this->form->getLabel('article4'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('article4'); ?></div>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</div>
</form>
