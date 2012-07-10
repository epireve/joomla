<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript" language="javascript">
/**
 * This function needs to be here because, Joomla toolbar calls it
 **/ 
Joomla.submitbutton = function(action){
	submitbutton( action );
}
 
function submitbutton( action )
{
	if( action == 'newGroup' )
	{
		window.location	= 'index.php?option=com_community&view=groups&layout=edit';
		return;
	}
	submitform( action );
}
</script>
<form action="index.php?option=com_community" method="post" name="adminForm">

<table class="adminform" cellpadding="3">
	<tr>
		<td width="95%">
			<?php echo JText::_('COM_COMMUNITY_SEARCH');?>
			<input type="text" onchange="document.adminForm.submit();" class="text_area" value="<?php echo ($this->search) ? $this->escape( $this->search ) : '';?>" id="search" name="search"/>
			<button onclick="this.form.submit();"><?php echo JText::_('COM_COMMUNITY_SEARCH');?></button>
		</td>
		<td nowrap="nowrap" align="right">
		<span style="font-weight: bold;"><?php echo JText::_('COM_COMMUNITY_GROUPS_VIEW_BY_CATEGORY'); ?>:
		<?php echo $this->categories;?>
		</td>
	</tr>
</table>
	
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%">#</th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->groups ); ?>);" />
			</th>
			<th width="15%" style="text-align: left;">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_NAME') , 'a.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th style="text-align: left;">
				<?php echo JText::_('COM_COMMUNITY_GROUPS_DESCRIPTION'); ?>
			</th>
			<th width="10%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_GROUPS_ADMINISTRATOR'), 'c.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_PUBLISHED'), 'a.published', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_GROUPS_MEMBER_S'), 'membercount', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<?php $i = 0; ?>
	<?php
		if( empty( $this->groups ) )
		{
	?>
	<tr>
		<td colspan="7" align="center"><?php echo JText::_('COM_COMMUNITY_GROUPS_NONE_CREATED');?></td>
	</tr>
	<?php
		} 
	?>
	<?php foreach( $this->groups as $row ): ?>
	<tr>
		<td align="center">
			<?php echo ( $i + 1 ); ?>
		</td>
		<td>
			<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_( 'index.php?option=com_community&view=groups&layout=edit&groupid=' . $row->id );?>">
				<?php echo $row->name; ?>
			</a>
		</td>
		<td>
			<?php echo $row->description; ?>
		</td>
		<td align="center">
			<?php echo $row->username; ?>
			<span>[ <a href="javascript:void(0);" onclick="azcommunity.changeGroupOwner('<?php echo $row->id; ?>');"><?php echo JText::_('COM_COMMUNITY_GROUPS_CHANGE'); ?></a> ]</span>
		</td>
		<td id="published<?php echo $row->id;?>" align="center">
			<?php echo $this->getPublish( $row , 'published' , 'groups,ajaxTogglePublish' );?>
		</td>
		<td align="center">
			<?php echo $row->membercount; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>
<input type="hidden" name="view" value="groups" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="groups" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>