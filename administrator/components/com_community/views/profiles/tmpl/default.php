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
Joomla.submitbutton = function( action ){
	submitbutton( action );
}
 
function submitbutton( action )
{
	switch( action )
	{
		case 'newgroup':
			azcommunity.newFieldGroup();
			break;
		case 'newfield':
			azcommunity.newField( false );
			break;
		case 'removefield':
			if( !confirm( '<?php echo JText::_('COM_COMMUNITY_DELETE_FIELD_CONFIRMATION'); ?>' ) )
			{
				break;
			}
		case 'publish':
		case 'unpublish':
		default:
			submitform( action );
	}
}
</script>
<form action="index.php?option=com_community" method="post" name="adminForm">
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%">
				<?php echo JText::_('COM_COMMUNITY_NUMBER'); ?>
			</th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->fields ); ?>);" />
			</th>
			<th>
				<?php echo JText::_('COM_COMMUNITY_NAME'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_COMMUNITY_FIELD_CODE'); ?>
			</th>
			<th align="center" width="10%">
				<?php echo JText::_('COM_COMMUNITY_TYPE'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('COM_COMMUNITY_PUBLISHED'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_( 'COM_COMMUNITY_FIELDS_SEARCHABLE' ); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('COM_COMMUNITY_VISIBLE'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('COM_COMMUNITY_REQUIRED'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('COM_COMMUNITY_REGISTRATION'); ?>
			</th>			
			<th width="5%" align="center">
				<?php echo JText::_('COM_COMMUNITY_PROFILES_ORDERING'); ?>
			</th>
		</tr>		
	</thead>
<?php
	$count	= 0;
	$i		= 0;
			
	foreach($this->fields as $field)
	{
		$input	= JHTML::_('grid.id', $count, $field->id);
		
		if($field->type == 'group')
		{
?>
		<tr>
			<td  style="background-color: #EEEEEE;">&nbsp;</td>
			<td  style="background-color: #EEEEEE;">
				<?php echo $input; ?>
			</td>
			<td colspan="3" style="background-color: #EEEEEE;">
				<strong><?php echo JText::_('COM_COMMUNITY_GROUPS');?>
					<span id="name<?php echo $field->id; ?>">
						<?php echo JHTML::_('link', 'javascript:void(0);', JText::_($field->name), array('onclick'=>'azcommunity.editFieldGroup(\'' . $field->id . '\', \'' . JText::_('COM_COMMUNITY_GROUPS_EDIT') . '\');')); ?>
					</span>
				</strong>
				<div style="clear: both;"></div>
			</td>
			<td align="center" id="published<?php echo $field->id;?>" style="background-color: #EEEEEE;">
				<?php echo $this->getPublish($field, 'published', 'profiles,ajaxGroupTogglePublish'); ?>
			</td>
			<td align="center" id="searchable<?php echo $field->id;?>" style="background-color: #eee;">
				<?php echo $this->getPublish( $field, 'searchable' , 'profiles,ajaxGroupTogglePublish'); ?>
			</td>
			<td align="center" id="visible<?php echo $field->id;?>" style="background-color: #EEEEEE;">
				<?php echo $this->getPublish($field, 'visible', 'profiles,ajaxGroupTogglePublish'); ?>
			</td>
			<td align="center" id="required<?php echo $field->id;?>" style="background-color: #EEEEEE;">
				<?php echo $this->getPublish($field, 'required', 'profiles,ajaxGroupTogglePublish'); ?>
			</td>
			<td align="center" id="registration<?php echo $field->id;?>" style="background-color: #EEEEEE;">
				<?php echo $this->getPublish($field, 'registration', 'profiles,ajaxGroupTogglePublish'); ?>
			</td>			
			<td class="order" align="right" style="background-color: #EEEEEE;">
				<?php echo $this->pagination->orderUpIcon( $count, true, 'orderup', 'Move Up'); ?>
				<?php echo $this->pagination->orderDownIcon( $count, count($this->fields) , true , 'orderdown', 'Move Down', true ); ?>
			</td>
		</tr>
<?php
			$i	= 0;	// Reset count
		}
		else if($field->type != 'group')
		{

			// Process publish / unpublish images
			++$i;
?>
		<tr class="row<?php echo $i%2;?>" id="rowid<?php echo $field->id;?>">
			<td><?php echo $i;?></td>
			<td>
				<?php echo $input; ?>
			</td>
			<td>
				<span class="editlinktip hasTip" title="<?php echo $this->escape( $field->name ); ?>::Tips: <?php echo $field->tips; ?>" id="name<?php echo $field->id;?>">
					<?php echo JHTML::_('link', 'javascript:void(0);', $field->name, array('onclick'=>'azcommunity.editField(\'' . $field->id . '\',\'' . JText::_('COM_COMMUNITY_PROFILES_EDIT') . '\');')); ?>
				</span>
			</td>
			<td align="center">
				<?php echo $field->fieldcode; ?>
			</td>
			<td align="center">
				<span id="type<?php echo $field->id;?>" onclick="$('typeOption').style.display = 'block';$(this).style.display = 'none';">
				<?php echo $this->getFieldText( $field->type ); ?>
				</span>
			</td>
			<td align="center" id="published<?php echo $field->id;?>">
				<?php echo $this->getPublish($field, 'published' , 'profiles,ajaxTogglePublish'); ?>
			</td>
			<td align="center" id="searchable<?php echo $field->id;?>">
				<?php echo $this->getPublish( $field, 'searchable' , 'profiles,ajaxTogglePublish' ); ?>
			</td>
			<td align="center" id="visible<?php echo $field->id;?>">
				<?php echo ($field->type == 'label') ? $this->showPublish($field, 'visible') : $this->getPublish($field, 'visible', 'profiles,ajaxTogglePublish'); ?>
			</td>
			<td align="center" id="required<?php echo $field->id;?>">
				<?php echo ($field->type == 'label') ? $this->showPublish($field, 'required') : $this->getPublish($field, 'required', 'profiles,ajaxTogglePublish'); ?>
			</td>
			<td align="center" id="registration<?php echo $field->id;?>">
				<?php echo $this->getPublish($field, 'registration', 'profiles,ajaxTogglePublish'); ?>
			</td>			
			<td align="right" class="order">
				<span><?php echo $this->pagination->orderUpIcon( $count , true, 'orderup', 'Move Up'); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $count , count($this->fields), true , 'orderdown', 'Move Down', true ); ?></span>
			</td>
		</tr>
<?php
		}
		$count++;
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
<input type="hidden" name="view" value="profiles" />
<input type="hidden" name="task" value="display" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="boxchecked" value="0" />
</form>