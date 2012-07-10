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
<script language="javascript" type="text/javascript">
joms.jQuery.noConflict();

function submitbutton() {	
	var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");
	
	//hide all the error messsage span 1st
	joms.jQuery('#jsemail').removeClass('invalid');
	joms.jQuery('#errjsemailmsg').hide();
	joms.jQuery('#errjsemailmsg').html('&nbsp');
	
	// do field validation
	var isValid	= true;	
	
	if(joms.jQuery('#jsemail').val() !=  joms.jQuery('#email').val())
	{
		regex=/^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
	   	isValid = regex.test(joms.jQuery('#jsemail').val());
	   	
		var fieldname = joms.jQuery('#jsemail').attr('name');;			       
		if(isValid == false){
			joms.jQuery('#jsemail').addClass('invalid');
			cvalidate.setMessage(fieldname, '', 'COM_COMMUNITY_INVALID_EMAIL');
		}	   	
   	}

	if(! isValid) {
	    joms.jQuery('#btnSubmit').show();
		joms.jQuery('#cwin-wait').hide();
 	}
	   	   		
	return isValid;	
}
</script>

<div>
	<span><?php echo JText::_('COM_COMMUNITY_ACTIVATION_ENTER_EMAIL'); ?></span>		
</div>
<div>
	<span>&nbsp;</span>		
</div>	

<form action="" method="post" id="jomsForm" name="jomsForm" class="community-form-validate">
<table class="ccontentTable" cellspacing="3" cellpadding="0">
<tbody>
	<tr>						
		<td class="paramlist_key">
			<label id="jsemailmsg" for="jsemail">*<?php echo JText::_( 'COM_COMMUNITY_EMAIL' ); ?></label>
		</td>
		<td class="paramlist_value">				
			<input class="inputbox required" type="text" id="jsemail" name="jsemail" size="50">
			<span id="errjsemailmsg" style="display:none;">&nbsp;</span>
		</td>					
	</tr>		
	<tr>
		<td class="listkey" >&nbsp;</td>
		<td class="listvalue">
			<div id="cwin-wait" style="display:none;"></div>
			<input class="button validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('COM_COMMUNITY_SEND'); ?>" name="submit">
		</td>					
	</tr>
</tbody>
</table>
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="view" value="register" />
<input type="hidden" name="task" value="activationResend" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    cvalidate.init();
    cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
    
	joms.jQuery( '#jomsForm' ).submit( function() {
	    joms.jQuery('#btnSubmit').hide();
		joms.jQuery('#cwin-wait').show();
		
		return submitbutton();
	});
</script>