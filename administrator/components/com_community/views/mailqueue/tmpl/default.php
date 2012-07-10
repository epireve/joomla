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
<p>
	<?php echo JText::sprintf('COM_COMMUNITY_MAILQUEUE_DESCRIPTION','http://www.jomsocial.com/support/docs/item/744-configuration.html'); ?>
</p>
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
			<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->mailqueues ); ?>);" /></th>
			<th width="5%" style="text-align: left;">
				<?php echo JText::_('COM_COMMUNITY_MAILQUEUE_RECIPIENT'); ?>
			</th>
			<th style="text-align: left;">
				<?php echo JText::_('COM_COMMUNITY_MAILQUEUE_SUBJECT'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_MAILQUEUE_CONTENT'); ?>
			</th>
			<th align="center" width="10%">
				<?php echo JText::_('COM_COMMUNITY_CREATED'); ?>
			</th>
			<th align="center" width="5%">
				<?php echo JText::_('COM_COMMUNITY_STATUS'); ?>
			</th>
		</tr>		
	</thead>
<?php
	if( !$this->mailqueues )
	{
?>
		<tr>
			<td colspan="7" align="center">
				<div><?php echo JText::_('COM_COMMUNITY_MAILQUEUE_NO_MAIL_QUEUE'); ?></div>
			</td>
		</tr>
<?php
	}
	else
	{
		$i		= 0;
		
		$mainframe	=& JFactory::getApplication();
		CFactory::load('helpers', 'time');

		foreach( $this->mailqueues as $queue )
		{
			$created	=& JFactory::getDate( $queue->created );
			if(method_exists('JDate','getOffsetFromGMT')){
				$systemOffset = new CDate('now',$mainframe->getCfg('offset'));
				$systemOffset = $systemOffset->getOffsetFromGMT(true);
			} else {
				$systemOffset = $mainframe->getCfg('offset');
			}			
			
			$created->setOffSet($systemOffset );
?>
		<tr>
			<td align="center"><?php echo $i + 1; ?></td>
			<td><?php echo JHTML::_('grid.id', $i++, $queue->id); ?></td>
			<td>
				<div>
					<?php echo $queue->recipient; ?>
				</div>
			</td>
			<td>
				<div>
					<?php echo $queue->subject; ?>
				</div>
			</td>
			<td>
				<div>
					<?php echo $queue->body; ?>
				</div>
			</td>
			<td align="center">
				<div>
					<?php echo $created->toFormat(); ?>
				</div>
			</td>
			<td align="center">
				<?php echo $this->getStatusText( $queue->status ); ?>
			</td>
		</tr>
<?php
		}
	}
?>
	<tfoot>
	<tr>
		<td colspan="7">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>
<input type="hidden" name="view" value="mailqueue" />
<input type="hidden" name="task" value="mailqueue" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="boxchecked" value="0" />
</form>