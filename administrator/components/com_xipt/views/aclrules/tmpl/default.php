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
			/** FOR JOOMLA1.6 ++**/
			Joomla.submitbutton=function(action) {
				submitbutton(action);
			}
	  <?php }?>
	  
function submitbutton( action )
{
	switch( action )
	{
		case 'remove':
			if( !confirm( '<?php echo XiptText::_('ARE_YOU_SURE_YOU_WANT_TO_REMOVE_THIS_RULE'); ?>' ) )
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

<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo XiptText::_('FOLLOWING_PUBLISHED_RULES_WILL_BE_APPLIED_FOR_RESTRICTION');?>
</div>

<form action="<?php echo JURI::base();?>index.php?option=com_xipt" method="post" name="adminForm">
<table class="adminlist" cellspacing="1">
	<thead>
		<tr class="title">
			<th width="1%">
				<?php echo XiptText::_( 'NUM' ); ?>
			</th>
			<th width="1%">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rules ); ?>);" />
			</th>
			<th>
				<?php echo XiptText::_( 'RULE_NAME' ); ?>
			</th>
			<th>
				<?php echo XiptText::_( 'ACL_NAME' ); ?>
			</th>
			<th>
				<?php echo XiptText::_( 'APPLICABLE_PROFILETYPE' ); ?>
			</th>
			<th width="5%">
				<?php echo XiptText::_( 'PUBLISHED' ); ?>
			</th>
		</tr>		
	</thead>
<?php
	$count	= 0;
	$i		= 0;

	if(!empty($this->rules))
	foreach($this->rules as $rule)
	{
		$input	= JHTML::_('grid.id', $count, $rule->id);
		
		// Process publish / unpublish images
		++$i;
?>
		<tr class="row<?php echo $i%2;?>" id="rowid<?php echo $rule->id;?>">
			<td><?php echo $i;?></td>
			<td>
				<?php echo $input; ?>
			</td>
			<td>
				<span class="editlinktip" title="<?php echo $rule->rulename; ?>" id="rulename<?php echo $rule->id;?>">
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=aclrules&task=edit&id='.$rule->id, false); ?>
						<A HREF="<?php echo $link; ?>"><?php echo $rule->rulename; ?></A>
				</span>
			</td>
			<td>
				<?php echo $rule->aclname; ?>
			</td>
			<td>
				<?php echo $this->ruleProfiletype[$rule->id]; ?>
			</td>
			<td align="center" id="published<?php echo $rule->id;?>">
				<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i-1;?>','<?php echo $rule->published ? 'switchOffpublished' : 'switchOnpublished' ?>')">
							<?php if($rule->published)
							{ ?>
								<img src="../components/com_xipt/assets/images/tick.png" width="16" height="16" border="0" alt="Published" />
							<?php 
							}
							else 
							{ ?>
								<img src="../components/com_xipt/assets/images/publish_x.png" width="16" height="16" border="0" alt="Unpublished" />
						<?php 
							} //echo $published;
						?>
				</a>
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



<input type="hidden" name="view" value="<?php echo JRequest::getVar('view','aclrules');?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_xipt" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>	
<?php 
