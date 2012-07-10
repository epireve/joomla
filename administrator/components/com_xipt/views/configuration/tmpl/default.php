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
		case 'publish':
		case 'unpublish':
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
				<?php echo XiptText::_( '#' ); ?>
			</th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->fields ); ?>);" />
			</th>
			<!--<th width="1%">
				<?php echo XiptText::_( 'PROFILETYPE-ID' ); ?>
			</th>-->
			<th>
				<?php echo XiptText::_( 'NAME' ); ?>
			</th>
			<th width="30%">
				<?php echo XiptText::_( 'RESET_TO_JOMSOCIAL' ); ?>
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
			<td><?php echo $i;?></td>
			<td>
				<?php echo $input; ?>
			</td>			
			<td>
				<span class="editlinktip" title="<?php echo $field->name; ?>" id="name<?php echo $field->id;?>">
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=configuration&task=edit&name='.$field->name.'&id='.$field->id, false); ?>
						<a href="<?php echo $link; ?>"><?php echo $field->name; ?></a>
				</span>
			</td>
			<td>
				<span class="editlinktip" title="<?php echo "Reset Configuration of ".$field->name." to JomSocial"; ?>" id="name<?php echo $field->id;?>">
					<?php if($this->reset[$field->id]=='true'){
					 $link = XiptRoute::_('index.php?option=com_xipt&view=configuration&task=reset&profileId='.$field->id, false); ?>
						<a href="<?php echo $link; ?>">Reset</a>
					<?php } else
								echo "Reset";?>	
				</span>		
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
