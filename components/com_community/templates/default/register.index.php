<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();
?>
<form action="<?php echo CRoute::getURI(); ?>" method="post" id="jomsForm" name="jomsForm" class="community-form-validate">
<div class="ctitle">
	<h2><?php echo JText::_( 'COM_COMMUNITY_REGISTER_TITLE_USER_INFO' ); ?></h2>
</div>
<table class="ccontentTable paramlist" cellspacing="1" cellpadding="0">
    <tbody>
<?php if ($isUseFirstLastName) { ; ?>
		<tr>
			<td class="paramlist_key">
				<label id="jsfirstnamemsg" for="jsfirstname" class="label">*<?php echo JText::_( 'COM_COMMUNITY_FIRST_NAME' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input type="text" id="jsfirstnamemsg" name="jsfirstname" size="40" 
						value="<?php echo $data['html_field']['jsfirstname']; ?>" 
						class="inputbox required validate-firstname" 
						maxlength="150" />
				<span id="errjsfirstnamemsg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label id="jslastnamemsg" for="jslastname" class="label">*<?php echo JText::_( 'COM_COMMUNITY_LAST_NAME' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input type="text" id="jslastnamemsg" name="jslastname" size="40" 
						value="<?php echo $data['html_field']['jslastname']; ?>" 
						class="inputbox required validate-jslastname" 
						maxlength="150" />
				<span id="errjslastnamemsg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
<?php } else { ?>
		<tr>
			<td class="paramlist_key">
				<label id="jsnamemsg" for="jsname" class="label">*<?php echo JText::_( 'COM_COMMUNITY_NAME' ); ?></label>												
			</td>
			<td class="paramlist_value">
			    <input type="text" name="jsname" id="jsname" size="40" value="<?php echo $data['html_field']['jsname']; ?>" class="inputbox required validate-name" maxlength="50" />
				<span id="errjsnamemsg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
<?php } ?>
		<tr>
			<td class="paramlist_key">
				<label id="jsusernamemsg" for="jsusername" class="label">*<?php echo JText::_( 'COM_COMMUNITY_USERNAME' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input type="text" id="jsusername" name="jsusername" size="40" value="<?php echo $data['html_field']['jsusername']; ?>" 
				       class="inputbox required validate-username" 
					   maxlength="150" />
			    <input type="hidden" name="usernamepass" id="usernamepass" value="N"/>							   
				<span id="errjsusernamemsg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label id="jsemailmsg" for="jsemail" class="label">*<?php echo JText::_( 'COM_COMMUNITY_EMAIL' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input type="text" id="jsemail" name="jsemail" size="40" value="<?php echo $data['html_field']['jsemail']; ?>" class="inputbox required validate-email" maxlength="100" />
			    <input type="hidden" name="emailpass" id="emailpass" value="N"/>
			    <span id="errjsemailmsg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label id="pwmsg" for="jspassword" class="label">*<?php echo JText::_( 'COM_COMMUNITY_PASSWORD' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input class="inputbox required validate-password" type="password" id="jspassword" name="jspassword" size="40" value="" />
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label id="pw2msg" for="jspassword2" class="label">*<?php echo JText::_( 'COM_COMMUNITY_VERIFY_PASSWORD' ); ?></label>
			</td>
			<td class="paramlist_value">
			    <input class="inputbox required validate-passverify" type="password" id="jspassword2" name="jspassword2" size="40" value="" />
			    <span id="errjspassword2msg" style="display:none;">&nbsp;</span>
			</td>
		</tr>
		<tr>
		    <td class="paramlist_key">&nbsp;</td>
			<td class="paramlist_value">						
				<?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?>
			</td>
		</tr>				
    </tbody>
</table>
<?php
if( $config->get('enableterms') )
{
?>
<div class="ctitle">
<h2><?php echo JText::_( 'COM_COMMUNITY_REGISTER_TITLE_TNC' ); ?></h2>
</div>

<table class="ccontentTable paramlist" cellspacing="1" cellpadding="0">
  <tbody>
	<tr>
		<td class="paramlist_key" id="tncmsg" for="tnc">
		    <input type="checkbox" name="tnc" id="tnc" value="Y"
			       class="inputbox required"/>
		
		</td>
		<td class="paramlist_value">
			<?php echo JText::_('COM_COMMUNITY_I_HAVE_READ').' <a href="javascript:void(0);" onclick="joms.registrations.showTermsWindow(0);">'.JText::_('COM_COMMUNITY_TERMS_AND_CONDITION').'</a>.';?>
		</td>
	</tr>
</tbody>
</table>	
<?php
}
?>
<?php 
if(!empty($recaptchaHTML))
{
?>
<table cellspacing="0" cellpadding="0">
  <tbody>
	<tr>
		<td class="paramlist_key">&nbsp;</td>
		<td><?php echo $recaptchaHTML;?></td>
	</tr>
</tbody>
</table>
<?php
}
?>

<table class="ccontentTable paramlist" cellspacing="1" cellpadding="0">
  <tbody>
	<tr>
		<td class="paramlist_key">&nbsp;</td>
		<td class="paramlist_value">
			<div id="cwin-wait" style="display:none;"></div>
			<input class="button validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('COM_COMMUNITY_NEXT'); ?>" name="submit" />
		</td>
	</tr>
	<tr>
	    <td class="paramlist_key">&nbsp;</td>
	    <td class="paramlist_value"><?php echo $fbHtml;?></td>
	</tr>
</tbody>
</table>
<input type="hidden" name="isUseFirstLastName" value="<?php echo $isUseFirstLastName; ?>" />
<input type="hidden" name="task" value="register_save" />
<input type="hidden" name="id" value="0" />
<input type="hidden" name="gid" value="0" />
<input type="hidden" id="authenticate" name="authenticate" value="0" />
<input type="hidden" id="authkey" name="authkey" value="" />
</form>	

<script type="text/javascript">  
	cvalidate.init();
	cvalidate.noticeTitle	= '<?php echo addslashes(JText::_('COM_COMMUNITY_NOTICE') );?>';
	cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
	cvalidate.setSystemText('JOINTEXT','<?php echo addslashes(JText::_("COM_COMMUNITY_AND")); ?>');

joms.jQuery( '#jomsForm' ).submit( function(){
    joms.jQuery('#btnSubmit').hide();
	joms.jQuery('#cwin-wait').show();
	joms.jQuery('#jomsForm input').attr('readonly', true);
	
	if(joms.jQuery('#authenticate').val() != '1')
	{
		joms.registrations.authenticate();
		return false;
	}
});





// Password strenght indicator
var password_strength_settings = {
	'texts' : {
		1 : '<?php echo addslashes(JText::_('COM_COMMUNITY_PASSWORD_STRENGHT_L1')); ?>',
		2 : '<?php echo addslashes(JText::_('COM_COMMUNITY_PASSWORD_STRENGHT_L2')); ?>',
		3 : '<?php echo addslashes(JText::_('COM_COMMUNITY_PASSWORD_STRENGHT_L3')); ?>',
		4 : '<?php echo addslashes(JText::_('COM_COMMUNITY_PASSWORD_STRENGHT_L4')); ?>',
		5 : '<?php echo addslashes(JText::_('COM_COMMUNITY_PASSWORD_STRENGHT_L5')); ?>'
	}
}
			
joms.jQuery('#jspassword').password_strength(password_strength_settings);


</script>
