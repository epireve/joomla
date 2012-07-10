<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');

$aModel			= XiptFactory::getInstance( 'applications' , 'model');
?>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_xipt/assets/js/jquery1.4.2.js';?>" ></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	// for select all profile type
	$("a#ptypeSelectAll").click(function(){
			$('div#xiptPtype').find(':checkbox').attr('checked', true);	
			return false;
	});

	// for select none
	$("a#ptypeSelectNone").click(function(){	
		$('div#xiptPtype').find(':checkbox').attr('checked', false);	
		return false;
	});

	// for copying the same setting to another app, block
	$("div#xiptOtherApps").css('display','none');
	$('input#xiptApplyTo').click(function(){
		$("div#xiptOtherApps").slideToggle('fast');	
	});
	
});
</script>
<script type="text/javascript">jQuery.noConflict();</script>


<form action="index.php" method="post" name="adminForm">
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo XiptText::_('ASSIGN_APPLICATION_AS_PER_PROFILE_TYPES_FOR_YOUR_SITE');?>
</div>
<div id="error-notice" style="color: red; font-weight:700;"></div>
<div style="clear: both;"></div>


<div class="col width-45" style="float:left;">
	<fieldset class="adminform">
	<legend><?php echo $aModel->getPlugin($this->applicationId)->name;?></legend>
		<div id="xiptPtype">
			<div style="float:left; font-weight:bold; margin-left:10%; padding:5px; width:27%; background: #EFEFEF;">
				<?php echo XiptText::_('FOR_PROFILETYPES');?>
			</div>
			<div style="float:left; margin-left:20px; width:35%;">
				<?php echo XiptHelperApps::buildProfileTypesforApplication($this->applicationId);?>
			</div>
			<div>
				<?php echo XiptText::_("SELECT");?> : <a href="#" id="ptypeSelectAll"><?php echo XiptText::_('ALL');?></a> | <a href="#" id="ptypeSelectNone"><?php echo XiptText::_('NONE');?></a> 
			</div>
		</div>
	</fieldset>
</div>

<div class="col width-45" style="float:left;">
	<fieldset class="adminform">
	<legend>
		<input type="checkBox" id="xiptApplyTo" />
		<?php echo XiptText::_('APPLY_THESE_SETTINGS_FOR')?>		
	</legend>
	
	<div id="xiptOtherApps">
		<?php foreach($this->fields as $id => $field) : ?>
			<input type="checkbox" name="appIds[]" value="<?php echo $id;?>"><?php echo $field->name;?>
			<div class='clr'></div>
		<?php endforeach;?> 
	</div>
	</fieldset>
</div>
<div class="clr"></div>

	<input type="hidden" name="option" value="com_xipt" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' , 'applications' );?>" />
	<input type="hidden" name="id" value="<?php echo $this->applicationId; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php 

