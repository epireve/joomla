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
		case 'unhook':
			if( !confirm( '<?php echo XiptText::_('ARE_YOU_SURE_YOU_WANT_TO_UNHOOK'); ?>' ) )
			{
				break;
			}
		default:
			submitform( action );
	}
}
</script>

<form action="<?php echo JURI::base();?>index.php?option=com_xipt&view=setup" method="post" name="adminForm">
<div>
	<div style="float:left;"> 
		<?php $counter = 1;	?>
		<table>
			<?php 	$complete 		= '<img src="../components/com_xipt/assets/images/tick.png" alt="done" />'; ?>
			<?php 	$notcomplete 	= '<img src="../components/com_xipt/assets/images/publish_x.png" alt="not complete" />'; ?>
			<?php 	$warningImage	= '<img src="../components/com_xipt/assets/images/warning.png" alt="warning" />'; ?>
			
			<?php  foreach($this->requiredSetup as $util) :	?>
			<tr id="setup<?php echo $counter; ?>" >
			 <td>
			 		<?php  echo $counter.". ";?>
			 </td>
			 
			 <td id="setupMessage<?php echo $counter; ?>">
			 	<?php  echo $util['message'];?>
			 </td>
			 
			 <td id="setupImage<?php echo $counter; ?>">
			 	<?php  if($util['type'] == XIPT_SETUP_WARNING)
			 				echo $warningImage;
			 			else if($util['done'] == true)
			 				echo $complete;
			 			else 
			 				echo $notcomplete;?>
			 </td>				 
			 </tr>
			 <?php  $counter++;  ?>
			 <?php endforeach;	?>
		</table>
	</div>
	<div style="float:inherit; margin-left:50%;">
			<?php
				$num = 1;
				echo $this->pane->startPane( 'stat-pane' );
					foreach($this->setupRules as $rule):
						if(isset($this->helpMsg[$rule['name']])==false)
							continue;
						echo $this->pane->startPanel($num.". ".$rule['title'],$rule['name']);
						echo $this->helpMsg[$rule['name']];
						echo $this->pane->endPanel();
						$num++;
					endforeach;	
				echo $this->pane->endPane();
			?>
	</div>
	</div>
<input type="hidden" name="view" value="setup" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="option" value="com_xipt" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>	
<?php 
