<?php
/**
 * @version     1.0.0
 * @package     com_gridtemplates
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ace | OGOSense <audovicic@ogosense.com> - http://www.ogosense.com
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_gridtemplates/assets/css/gridtemplates.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_gridtemplates');
$saveOrder = $listOrder == 'a.`ordering`';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_gridtemplates&task=templates.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'templateList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function () {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}

	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});
	});
</script>

<?php
//Joomla Component Creator code to allow adding non select list filters
if (!empty($this->extra_sidebar))
{
	$this->sidebar .= $this->extra_sidebar;
}
?>

<form action="<?php echo JRoute::_('index.php?option=com_gridtemplates&view=templates'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
			<?php endif; ?>

			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER'); ?></label>
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i></button>
					<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
						<i class="icon-remove"></i></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
					<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
						<option value="asc" <?php if ($listDirn == 'asc')
						{
							echo 'selected="selected"';
						} ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
						<option value="desc" <?php if ($listDirn == 'desc')
						{
							echo 'selected="selected"';
						} ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
					</select>
				</div>
			</div>
			<div class="clearfix"></div>
			<table class="table table-striped" id="templateList">
				<thead>
				<tr>
					<?php if (isset($this->items[0]->ordering)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.`ordering`', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
						</th>
					<?php endif; ?>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<?php if (isset($this->items[0]->state)): ?>
						<th width="1%" class="nowrap center">
	<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.`state`', $listDirn, $listOrder); ?>
</th>
					<?php endif; ?>

									<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_CATEGORY'); ?>
				</th>

				<?php if (isset($this->items[0]->id)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.`id`', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>


				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE0'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE1'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE2'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE3'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE4'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE5'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE6'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE7'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE8'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE9'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE10'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE11'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE12'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE13'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE14'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE15'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE16'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE17'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE18'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_GRIDTEMPLATES_TEMPLATES_TYPE19'); ?>
				</th>


					<?php if (isset($this->items[0]->id)): ?>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.`id`', $listDirn, $listOrder); ?>
						</th>
					<?php endif; ?>
				</tr>
				</thead>
				<tfoot>
				<?php
				if (isset($this->items[0]))
				{
					$colspan = count(get_object_vars($this->items[0]));
				}
				else
				{
					$colspan = 10;
				}
				?>
				<tr>
					<td colspan="<?php echo $colspan ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php foreach ($this->items as $i => $item) :
					$ordering   = ($listOrder == 'a.ordering');
					$canCreate  = $user->authorise('core.create', 'com_gridtemplates');
					$canEdit    = $user->authorise('core.edit', 'com_gridtemplates');
					$canCheckin = $user->authorise('core.manage', 'com_gridtemplates');
					$canChange  = $user->authorise('core.edit.state', 'com_gridtemplates');
					?>
					<tr class="row<?php echo $i % 2; ?>">

						<?php if (isset($this->items[0]->ordering)): ?>
							<td class="order nowrap center hidden-phone">
								<?php if ($canChange) :
									$disableClassName = '';
									$disabledLabel    = '';
									if (!$saveOrder) :
										$disabledLabel    = JText::_('JORDERINGDISABLED');
										$disableClassName = 'inactive tip-top';
									endif; ?>
									<span class="sortable-handler hasTooltip <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>">
							<i class="icon-menu"></i>
						</span>
									<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
								<?php else : ?>
									<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						<td class="hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<?php if (isset($this->items[0]->state)): ?>
							<td class="center">
	<?php echo JHtml::_('jgrid.published', $item->state, $i, 'templates.', $canChange, 'cb'); ?>
</td>
						<?php endif; ?>

				<td>
					<a href="index.php?option=com_gridtemplates&view=template&layout=edit&id=<?php echo $item->id; ?>">
						<?php echo $item->category; ?>
					</a>
				</td>

				<?php if (isset($this->items[0]->id)): ?>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						<?php endif; ?>


				<td>

					<?php echo $item->type0; ?>
				</td>
				<td>

					<?php echo $item->type1; ?>
				</td>
				<td>

					<?php echo $item->type2; ?>
				</td>
				<td>

					<?php echo $item->type3; ?>
				</td>
				<td>

					<?php echo $item->type4; ?>
				</td>
				<td>

					<?php echo $item->type5; ?>
				</td>
				<td>

					<?php echo $item->type6; ?>
				</td>
				<td>

					<?php echo $item->type7; ?>
				</td>
				<td>

					<?php echo $item->type8; ?>
				</td>
				<td>

					<?php echo $item->type9; ?>
				</td>
				<td>

					<?php echo $item->type10; ?>
				</td>
				<td>

					<?php echo $item->type11; ?>
				</td>
				<td>

					<?php echo $item->type12; ?>
				</td>
				<td>

					<?php echo $item->type13; ?>
				</td>
				<td>

					<?php echo $item->type14; ?>
				</td>
				<td>

					<?php echo $item->type15; ?>
				</td>
				<td>

					<?php echo $item->type16; ?>
				</td>
				<td>

					<?php echo $item->type17; ?>
				</td>
				<td>

					<?php echo $item->type18; ?>
				</td>
				<td>

					<?php echo $item->type19; ?>
				</td>


						<?php if (isset($this->items[0]->id)): ?>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
</form>        

		
