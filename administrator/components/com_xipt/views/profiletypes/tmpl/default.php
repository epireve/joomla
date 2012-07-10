<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
if(!defined('_JEXEC')) die('Restricted access');
?>
<script type="text/javascript" language="javascript">
/**
 * This function needs to be here because, Joomla toolbar calls it
 **/ 
 <?php 
		 If(!XIPT_JOOMLA_15)
		 {
		 	?>
		 /** FOR JOOMLA1.6++ **/
		 Joomla.submitbutton=function(action) {
		 	submitbutton(action);
		 }
   <?php }?>
		 
function submitbutton( action )
{
	switch( action )
	{
		case 'remove':
			if( !confirm( "<?php echo XiptText::_('ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_PROFILE_TYPE'); ?>" ) )
			{
				break;
			}
		case 'switchOnpublished':
		case 'switchOffpublished':
		default:
			submitform( action );
	}
}
</script>

<form action="<?php echo JURI::base();?>index.php?option=com_xipt" method="post" name="adminForm">
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->fields ); ?>);" />
			</th>
			<th width="1%">
				<?php echo XiptText::_( 'PROFILETYPE-ID' ); ?>
			</th>
			<th>
				<?php echo XiptText::_( 'NAME' ); ?>
			</th>
			<th width="10%">
				<?php echo XiptText::_( 'AVATAR' ); ?>
			</th>
			<th width="10%">
				<?php echo XiptText::_( 'WATERMARK' ); ?>
			</th>
			<th width="10%">
				<?php echo XiptText::_( 'JOOMLA_USER_TYPE' ); ?>
			</th>
			<th width="5%">
				<?php echo XiptText::_( 'REQUIRE_APPROVAL' ); ?>
			</th>
			<th width="5%">
				<?php echo XiptText::_( 'PUBLISHED' ); ?>
			</th>
			<th width="5%">
				<?php echo XiptText::_( 'VISIBLE' ); ?>
			</th>
			<th width="5%">
				<?php echo XiptText::_( 'TOTAL_USERS' ); ?>
			</th>
			<th width="5%" align="center">
				<?php echo XiptText::_( 'ORDERING' ); ?>
			</th>
		</tr>		
	</thead>
	
	<?php 
	$count	= 0;
	$i		= 0;
	if(!empty($this->fields))
	foreach($this->fields as $field)
	{
		$input	= JHTML::_('grid.id', $count, $field->id);
		
		// Process publish / unpublish images
		++$i;
	?>
		<tr class="row<?php echo $i%2;?>" id="rowid<?php echo $field->id;?>">			
			<td>
				<?php echo $input; ?>
			</td>
			
			<td><?php echo $field->id;?></td>
			
			<td>
				<span class="editlinktip" title="<?php echo $field->name; ?>" id="name<?php echo $field->id;?>">
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes&task=edit&id='.$field->id, false); ?>
						<a href="<?php echo $link; ?>"><?php echo $field->name; ?></a>
						<?php $tmp = $field->tip; ?>
                        <?php JFilterOutput::cleanText($tmp); ?><br/>
                        <?php echo JString::substr($tmp, 0,100); ?>
				</span>
			</td>
			
			<td align="center" id="avatar<?php echo $field->id;?>">							
				<img src="<?php echo JURI::root().XiptHelperUtils::getUrlpathFromFilePath($field->avatar);?>" width="64" height="64" border="0" alt="<?php echo $field->avatar; ?>" />	
			</td>
			
			<td align="center" id="watermark<?php echo $field->id;?>">
					<?php	$wm = $field->watermarkparams;
					$wmparams = new XiptParameter($wm, '');
					if($wmparams->get('enableWaterMark',0)):  ?>				
				<img src="<?php echo JURI::root().XiptHelperUtils::getUrlpathFromFilePath($field->watermark);?>"  border="0" alt="<?php echo $field->watermark; ?>" />	
					<?php endif; ?>
				</td>
			
			<td align="center" id="jusertype<?php echo $field->id;?>">
				<?php echo $field->jusertype; ?>
			</td>
			
			<td align="center" id="approve<?php echo $field->id;?>">
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i-1;?>','<?php echo $field->approve ? 'switchOffapprove' : 'switchOnapprove' ?>')">
					<?php if($field->approve) : ?>
						<img src="../components/com_xipt/assets/images/tick.png" width="16" height="16" border="0" alt="Admin Approve" />
					<?php else : ?> 
						<img src="../components/com_xipt/assets/images/publish_x.png" width="16" height="16" border="0" alt="Auto Approve" />
					<?php endif; ?>
				</a>			
			</td>			
			
			<td align="center" id="published<?php echo $field->id;?>">
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i-1;?>','<?php echo $field->published ? 'switchOffpublished' : 'switchOnpublished' ?>')">
					<?php if($field->published) : ?>
						<img src="../components/com_xipt/assets/images/tick.png" width="16" height="16" border="0" alt="Published" />
					<?php else : ?> 
						<img src="../components/com_xipt/assets/images/publish_x.png" width="16" height="16" border="0" alt="Unpublished" />
					<?php endif; ?>
				</a>
			</td>
			
			<td align="center" id="visible<?php echo $field->id;?>">
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i-1;?>','<?php echo $field->visible ? 'switchOffvisible' : 'switchOnvisible' ?>')">
					<?php if($field->visible) : ?>
						<img src="../components/com_xipt/assets/images/tick.png" width="16" height="16" border="0" alt="Visible" />
					<?php else : ?>
						<img src="../components/com_xipt/assets/images/publish_x.png" width="16" height="16" border="0" alt="Invisible" />
					<?php endif; ?>
				</a>					
			</td>
			
			<td align="center">
				<?php echo $this->getTotalUsers( $field->id );?>
			</td>
			
			<td align="right">
				<span><?php echo $this->pagination->orderUpIcon( $count , true, 'orderup', 'Move Up'); ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $count , count($this->fields), true , 'orderdown', 'Move Down', true ); ?></span>
			</td>			
		</tr>
<?php
		
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
<div class="clr"></div>
<input type="hidden" name="view" value="profiletypes" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_xipt" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>	
<?php 