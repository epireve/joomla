<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');
?>

<script language="javascript" type="text/javascript">

<?php 
		If(!XIPT_JOOMLA_15)
		{
			?>
		/** FOR JOOMLA1.6++ **/
		Joomla.submitbutton=function(action) {
			submitbutton(action);
		}
  <?php }?>

	function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		submitform( pressbutton );
	}
</script>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_xipt/assets/js/jquery1.4.2.js';?>" ></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	// for select all profile type
	$("a.ptypeSelectAll").click(function(){		
		var $this = $(this);
		var divID = $this.attr("id").replace("ptypeSelectAll", "profileTypes");
		var $div = $('#'+divID);
		$div.find(':checkbox').attr('checked', true);	
			return false;
	});

	// for select none
	$("a.ptypeSelectNone").click(function(){	
		var $this = $(this);
		var divID = $this.attr("id").replace("ptypeSelectNone", "profileTypes");
		var $div = $('#'+divID);
		$div.find(':checkbox').attr('checked', false);	
		return false;
	});

	// for copying the same setting to another profilefields block
	$("div#xiptOtherApps").css('display','none');
	$('input#xiptApplyTo').click(function(){
		$("div#xiptOtherApps").slideToggle('fast');	
	});
});
</script>
<script type="text/javascript">jQuery.noConflict();</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist" cellspacing="1" style="width:40%; float:left;" >
		<thead>
		<tr>
			<td style="width:40%;" >
				<?php echo XiptText::_( 'FIELD_NAME' ); ?> :
			</td>
			<td style="width:60%;">
					<?php echo $this->field->name; ?>
			</td>
		</tr>
		</thead>
		<?php foreach($this->categories as $catIndex => $catInfo) :?> 
				<?php $catName = $catInfo['name']; ?>
				<tr  class="row<?php echo $catIndex%2;?>" >
					<td>
						<?php echo XiptText::_($catName) ;?> :
					</td>
					<td > 
						<div id="profileTypes<?php echo $catName;?>">
							<div style="float:left; margin-left:5%; width:50%;">
								<?php echo XiptHelperProfilefields::buildProfileTypes($this->fieldid,$catIndex);?>
							</div>
							<div style="float:right; width:40%;">
								<?php echo XiptText::_("SELECT");?> : 
								<a href="#" class="ptypeSelectAll" id="ptypeSelectAll<?php echo $catName;?>"><?php echo XiptText::_('ALL');?></a> | 
								<a href="#" class="ptypeSelectNone" id="ptypeSelectNone<?php echo $catName;?>"><?php echo XiptText::_('NONE');?></a>	 
							</div>
						</div>							
					</td>			
				</tr>
		<?php endforeach; ?>
		</table>

<div style="width:30%; float:right;">
<?php 
echo $this->pane->startPane( 'stat-pane' );
require("helppanel.php");
echo $this->pane->endPane();
?>
</div>

<div class="col width-10" style="float:right;">
	<fieldset class="adminform">
	<legend>
		<input type="checkBox" id="xiptApplyTo" />
		<?php echo XiptText::_('APPLY_THESE_SETTINGS_FOR')?>		
	</legend>
	
	<div id="xiptOtherApps">
		<?php foreach($this->fields as $field) : ?>
			<?php if($field->type == 'group') : 
					echo $field->name;
				  else :?>
					<input type="checkbox" name="fieldIds[]" value="<?php echo $field->id;?>"><?php echo $field->name;?>
			<?php endif; ?>	
			<div class='clr'></div>
		<?php endforeach;?> 
	</div>
	</fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="option" value="com_xipt" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' , 'profilefields' );?>" />
	<input type="hidden" name="id" value="<?php echo $this->fieldid; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php 
