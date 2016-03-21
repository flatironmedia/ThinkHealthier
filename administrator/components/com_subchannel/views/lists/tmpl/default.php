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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_subchannel/assets/css/subchannel.css');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_subchannel');
$saveOrder = $listOrder == 'a.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_subchannel&task=lists.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'listList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
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

<form action="<?php echo JRoute::_('index.php?option=com_subchannel&view=lists'); ?>" method="post" name="adminForm" id="adminForm">
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
			<table class="table table-striped" id="listList">
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
						
					<?php endif; ?>

									<th class='left'>
				<?php echo JText::_('Category'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID0', 'a.`id0`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID1', 'a.`id1`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID2', 'a.`id2`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID3', 'a.`id3`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID4', 'a.`id4`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID5', 'a.`id5`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID6', 'a.`id6`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID7', 'a.`id7`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID8', 'a.`id8`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID9', 'a.`id9`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID10', 'a.`id10`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID11', 'a.`id11`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID12', 'a.`id12`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID13', 'a.`id13`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID14', 'a.`id14`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID15', 'a.`id15`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID16', 'a.`id16`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID17', 'a.`id17`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID18', 'a.`id18`'); ?>
				</th>
				<th class='left'>
				<?php echo JText::_('COM_SUBCHANNEL_LISTS_ID19', 'a.`id19`'); ?>
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
					$canCreate  = $user->authorise('core.create', 'com_subchannel');
					$canEdit    = $user->authorise('core.edit', 'com_subchannel');
					$canCheckin = $user->authorise('core.manage', 'com_subchannel');
					$canChange  = $user->authorise('core.edit.state', 'com_subchannel');
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
							
						<?php endif; ?>

										<td>
					<a href="index.php?option=com_subchannel&view=list&layout=edit&id=<?php echo $item->id; ?>">
					<?php echo $item->category_id; ?>
					</a>
				</td>
				<td>

					<?php echo $item->id0; ?>
				</td>
				<td>

					<?php echo $item->id1; ?>
				</td>
				<td>

					<?php echo $item->id2; ?>
				</td>
				<td>

					<?php echo $item->id3; ?>
				</td>
				<td>

					<?php echo $item->id4; ?>
				</td>
				<td>

					<?php echo $item->id5; ?>
				</td>
				<td>

					<?php echo $item->id6; ?>
				</td>
				<td>

					<?php echo $item->id7; ?>
				</td>
				<td>

					<?php echo $item->id8; ?>
				</td>
				<td>

					<?php echo $item->id9; ?>
				</td>
				<td>

					<?php echo $item->id10; ?>
				</td>
				<td>

					<?php echo $item->id11; ?>
				</td>
				<td>

					<?php echo $item->id12; ?>
				</td>
				<td>

					<?php echo $item->id13; ?>
				</td>
				<td>

					<?php echo $item->id14; ?>
				</td>
				<td>

					<?php echo $item->id15; ?>
				</td>
				<td>

					<?php echo $item->id16; ?>
				</td>
				<td>

					<?php echo $item->id17; ?>
				</td>
				<td>

					<?php echo $item->id18; ?>
				</td>
				<td>

					<?php echo $item->id19; ?>
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

		
