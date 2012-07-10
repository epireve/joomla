<?php
/**
 * @package	JomSocial
 * @subpackage Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<?php if( $showProfileType ){ ?>
<div class="com-notice">
		<?php if( $multiprofile->id != COMMUNITY_DEFAULT_PROFILE ){ ?>
			<?php echo JText::sprintf('COM_COMMUNITY_CURRENT_PROFILE_TYPE' , $multiprofile->name );?>
		<?php } else { ?>
			<?php echo JText::_('COM_COMMUNITY_CURRENT_DEFAULT_PROFILE_TYPE');?>
		<?php } ?>
		[ <a href="<?php echo CRoute::_('index.php?option=com_community&view=multiprofile&task=changeprofile');?>"><?php echo JText::_('COM_COMMUNITY_CHANGE');?></a> ]
</div>
<?php } ?>
<div id="profile-edit">
<form name="jsform-profile-edit" id="frmSaveProfile" action="<?php echo CRoute::getURI(); ?>" method="POST" class="community-form-validate">
<?php if(!empty($beforeFormDisplay)){ ?>
	<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
		<?php echo $beforeFormDisplay; ?>
	</table>
<?php } ?>
<?php
foreach ( $fields as $name => $fieldGroup )
{
		if ($name != 'ungrouped')
		{
?>
		<div class="ctitle">
			<h2><?php echo JText::_( $name );?></h2>
		</div>
<?php
		}
?>
		<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
		<tbody>
			<?php
				foreach ( $fieldGroup as $f )
				{
					$f = JArrayHelper::toObject ( $f );
					
					// DO not escape 'SELECT' values. Otherwise, comparison for
					// selected values won't work
					if($f->type != 'select'){
						$f->value	= $this->escape( $f->value );
					}
			?>
					<tr>
	 					<td class="key"><label id="lblfield<?php echo $f->id;?>" for="field<?php echo $f->id;?>" class="label"><?php if($f->required == 1) echo '*'; ?><?php echo JText::_( $f->name );?></label></td>	 					
	 					<td class="value"><?php echo CProfileLibrary::getFieldHTML( $f , '' ); ?></td>
	 					<td class="privacy">
	 						<?php echo CPrivacy::getHTML( 'privacy' . $f->id , $f->access ); ?>
	 					</td>
	 				</tr>
	 		<?php
				}
			?>
		</tbody>
		</table>
<?php
}
?>
		<table class="formtable" cellspacing="1" cellpadding="0">
			<tr>
				<td class="key"></td>
				<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
			</tr>
		</table>

<?php if(!empty($afterFormDisplay)){ ?>
	<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
		<?php echo $afterFormDisplay; ?>
	</table>
<?php } ?>
		<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
		<tbody>
			<tr>
			    <td class="key"></td>
			    <td class="value">
					<input type="hidden" name="action" value="save" />
                    <input class="validateSubmit button" type="submit" value="<?php echo JText::_('COM_COMMUNITY_SAVE_BUTTON'); ?>" />
			    </td>
			</tr>
		</tbody>
		</table>
</form>
<script type="text/javascript">
    cvalidate.init();
    cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
    cvalidate.setSystemText('JOINTEXT','<?php echo addslashes(JText::_("COM_COMMUNITY_AND")); ?>');
    
    joms.jQuery( document ).ready( function(){
    	joms.privacy.init();
	});
</script>
</div>