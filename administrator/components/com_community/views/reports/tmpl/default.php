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
 * This function needs to be here because, Joomla calls it
 **/ 
 Joomla.submitbutton = function(action){
 	submitbutton( action );
 }
 
function submitbutton(action)
{	
	submitform(action);
}
</script>
<form action="index.php?option=com_community" method="post" name="adminForm">
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
			<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->reports ); ?>);" /></th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_ITEM_LINK');?>
			</th>
			<th style="text-align: left;">
				<?php echo JText::_('COM_COMMUNITY_VIEW_REPORTS'); ?>
			</th>
			<th style="text-align: center;" width="5%">
				<?php echo JText::_('COM_COMMUNITY_STATUS'); ?>
			</th>
			<th width="15%" style="text-align: center;">
				<?php echo JText::_('COM_COMMUNITY_ACTIONS'); ?>
			</th>
			<th style="text-align: center;" width="5%">
				<?php echo JText::_('COM_COMMUNITY_VIEW_ITEM'); ?>
			</th>
			<th align="center" width="5%">
				<?php echo JText::_('COM_COMMUNITY_COUNT'); ?>
			</th>
			<th align="center" width="10%">
				<?php echo JText::_('COM_COMMUNITY_SUBMITTED_ON'); ?>
			</th>
		</tr>		
	</thead>
<?php
	if( !$this->reports )
	{
?>
		<tr>
			<td colspan="9" align="center">
				<div><?php echo JText::_('COM_COMMUNITY_REPORTS_NOT_SUBMITTED'); ?></div>
			</td>
		</tr>
<?php
	}
	else
	{
		$count		= 0;
		
		foreach( $this->reports as $row )
		{
?>
		<tr id="row<?php echo $row->id;?>">
			<td align="center"><?php echo $count + 1; ?></td>
			<td><?php echo JHTML::_('grid.id', $count++, $row->id); ?></td>
			<td>
				<a href="<?php echo $row->link;?>" target="_blank"><?php echo $row->link;?></a>
			</td>
			<td>
				<div>
					<a href="<?php echo JRoute::_('index.php?option=com_community&view=reports&layout=childs&reportid=' . $row->id );?>">
						<?php echo JText::_('COM_COMMUNITY_REPORTS');?>
					</a>
				</div>
			</td>
			<td>
				<div style="text-align: center;">
					<?php
						if( $row->status == 1 )
						{
							echo JText::_('COM_COMMUNITY_REPORTS_PROCESSED');
						}
						else if( $row->status == 2 )
						{
							echo JText::_('COM_COMMUNITY_REPORTS_IGNORED');
						}
						else
						{
							echo JText::_('COM_COMMUNITY_PENDING');
						}
					?>
				</div>
			</td>
			<td style="text-align: center;">
				<?php
					for( $i = 0; $i < count( $row->actions ); $i++ )
					{
						$action	=& $row->actions[ $i ];
				?>
					<span>
						<a href="javascript:void(0);" onclick="azcommunity.reportAction('<?php echo $action->id;?>');">
							<?php echo $action->label;?>
						</a>
					</span>
				<?php
						if( ( $i + 1 )!= count( $row->actions ) )
						{
							echo ' | ';
						}
					}
				?>
					<span>
						| <a href="javascript:void(0);" onclick="azcommunity.reportAction( '<?php echo $action->id;?>', 1 );">
							<?php echo JText::_('COM_COMMUNITY_REPORTS_IGNORE'); ?>
						</a>
					</span>
			</td>
			<td>
				<div>
					<a href="<?php echo $row->link;?>" target="_blank"><?php echo JText::_('COM_COMMUNITY_VIEW_ITEM');?></a>
				</div>
			</td>
			<td align="center">
				<div>
					<?php echo count( $row->reporters ); ?>
				</div>
			</td>
			<td align="center">
				<div>
					<?php echo $row->created; ?>
				</div>
			</td>
		</tr>
<?php
		}
	}
?>
	<tfoot>
	<tr>
		<td colspan="9">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>
<input type="hidden" name="view" value="reports" />
<input type="hidden" name="task" value="reports	" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="boxchecked" value="0" />
</form>