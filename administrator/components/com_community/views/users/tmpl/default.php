<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
$task	= JRequest::getString( 'task' , '' );

if( $task == 'element' )
{
	echo $this->loadTemplate( 'element' );
}
else
{
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
	switch( action )
	{
	    case 'export':
			var items = new Array();
			joms.jQuery('#adminForm input[name=cid[]]:checked').each( function(){
				items.push( joms.jQuery(this).val() );
			});
            window.open( 'index.php?option=com_community&view=users&tmpl=component&no_html=1&format=csv&task=export&cid[]=' + items.join('&cid[]=') );
			break;
		default:
	 	   submitform( action );
	 	   break;
	}
	
}
</script>
<form action="index.php?option=com_community" method="post" name="adminForm" id="adminForm">
<div style="margin-bottom: 10px;">
<table class="adminform" cellpadding="3">
	<tr>
		<td width="95%">
			<?php echo JText::_('COM_COMMUNITY_SEARCH');?>
			<input type="text" onchange="document.adminForm.submit();" class="text_area" value="<?php echo ($this->search) ? $this->escape( $this->search ) : ''; ?>" id="search" name="search"/>
			<button onclick="this.form.submit();"><?php echo JText::_('COM_COMMUNITY_SEARCH');?></button>
		</td>
		<td nowrap="nowrap" align="right">
			<span style="font-weight: bold;"><?php echo JText::_('COM_COMMUNITY_FILTER_USERS_BY'); ?>
			<select name="usertype" onchange="document.adminForm.submit();">
				<option value="all"<?php echo $this->usertype == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_JUMP_ALL');?></option>
				<option value="joomla"<?php echo $this->usertype == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_JOOMLA_USERS');?></option>
				<option value="facebook"<?php echo $this->usertype == 'facebook' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_FACEBOOK_USERS');?></option>
			</select>
			<select name="profiletype" onchange="document.adminForm.submit();">
				<option value="all"<?php echo $this->profileType == 'all' ? ' selected="selected"' : '';?>><?php echo JText::_('COM_COMMUNITY_USERS_ALL_PROFILE_TYPES');?></option>
				<?php
				if( $this->profileTypes )
				{
					foreach( $this->profileTypes as $profile )
					{
				?>
					<option value="<?php echo $profile->id;?>"<?php echo $this->profileType == $profile->id ? ' selected="selected"' : '';?>><?php echo $profile->name;?></option>
				<?php
					}
				}
				?>
			</select>
		</td>
	</tr>
</table>

</div>
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->users ); ?>);" />
			</th>
			<th width="25%" style="text-align: left;">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_NAME') , 'name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%" style="text-align: center;">
				&nbsp;
			</th>
			<th width="5%" style="text-align: left;">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_USERNAME'), 'username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="2%" width="10%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_ENABLED'), 'block', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_EMAIL'), 'email', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="5%">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_LAST_VISITED'), 'lastvisitDate', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th width="2%">
				<?php echo JText::_('COM_COMMUNITY_USERS_TYPE');?>
			</th>
			<th width="2%" align="center">
				<?php echo JHTML::_('grid.sort',   JText::_('COM_COMMUNITY_ID'), 'id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
		</tr>
	</thead>
	<?php $i = 0; ?>
	<?php
		if( $this->users )
		{
			foreach( $this->users as $row )
			{
	?>
	<tr>
		<td align="center">
			<?php echo ( $i + 1 ); ?>
		</td>
		<td align="center">
			<?php echo JHTML::_('grid.id', $i++, $row->id); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_('index.php?option=com_community&view=users&layout=edit&id=' . $row->id ); ?>">
				<?php echo $row->name; ?>
			</a>
		</td>
		<td align="center">
			<a href="javascript:void(0);" onclick="azcommunity.assignGroup('<?php echo $row->id;?>');"><?php echo JText::_('COM_COMMUNITY_ASSIGN_TO_GROUP');?></a>
		</td>
		<td>
			<?php echo $row->username; ?>
		</td>
		<td align="center" id="block<?php echo $row->id;?>" align="center">
			<?php echo $this->getPublish( $row , 'block' , 'users,ajaxTogglePublish' );?>
		</td>
		<td id="published<?php echo $row->id;?>">
			<a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email;?></a>
		</td>
		<td align="center">
		<?php
			$date		=& JFactory::getDate( $row->lastvisitDate );
			$mainframe	=& JFactory::getApplication();
			$date->setOffset( $mainframe->getCfg( 'offset' ) );
			echo $date->toFormat();
		?>
		</td>
		<td align="center">
			<?php echo $this->getConnectType( $row->id ); ?>
		</td>
		<td align="center"><?php echo $row->id;?></td>
	</tr>
	<?php
			}
		}
		else
		{
	?>
	<tr>
		<td colspan="10" align="center"><?php echo JText::_('COM_COMMUNITY_NO_RESULT');?></td>
	</tr>
	<?php
		}
	 ?>
	<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>
<input type="hidden" name="view" value="users" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="users" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
}
?>