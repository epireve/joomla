<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();

$uri	= CRoute::_('index.php?option=com_community&view=profile' , false );
$return	= base64_encode($uri);
?>

<!--sepatutnya kat atas ni false, but cuba jadi true dan tambah &screen=mobile -->

<h2>Members Login</h2>


<script type="text/javascript">
function clearInputs()
{
	joms.jQuery('#username').val('');
	joms.jQuery('#password').val('');
	joms.jQuery('#username').focus();
}
window.scrollTo(0, 1);
</script>




<form action="<?php echo CRoute::getURI() . 'screen=mobile' ?>" method="post" name="login" id="form-login">


<div class="loginform">
	<div class="loginform-label"><?php echo JText::_('COM_COMMUNITY_USERNAME'); ?></div>
	
	<div class="loginform-input">
		<input type="text" class="inputbox frontlogin" name="username" id="username" size="18" value="your username" onFocus="if(this.value==this.defaultValue) this.value='';" onBlur="if(this.value=='') this.value=this.defaultValue;"/>
	</div>
			<div class="clear"></div>
	<div class="loginform-label"><?php echo JText::_('COM_COMMUNITY_PASSWORD'); ?></div>
	<div class="loginform-input"><input type="password" class="inputbox frontlogin" name="<?php echo COM_USER_PASSWORD_INPUT;?>" id="password" value="password" onFocus="if(this.value==this.defaultValue) this.value='';" onBlur="if(this.value=='') this.value=this.defaultValue;"/>

</div>
	
<div class="clear"></div>

</div><!--end of loginform-->



<div class="buttons-area">
	<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_IPHONE_BUTTON_LOGIN');?>" name="submit" id="submit" class="button" />
	<input type="button" value="<?php echo JText::_('COM_COMMUNITY_IPHONE_BUTTON_CLEAR');?>" name="clear" id="clear" class="button" onclick="clearInputs();return false;" />
	<input type="hidden" name="option" value="<?php echo COM_USER_NAME;?>" />
	<input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN;?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</div>
		
		<!--
		<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=reset' ); ?>" class="login-forgot-password">
			<span><?php echo JText::_('COM_COMMUNITY_FORGOT_PASSWORD'); ?></span>
		</a><br />
		<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=remind' ); ?>" class="login-forgot-username">
			<span><?php echo JText::_('COM_COMMUNITY_FORGOT_USERNAME'); ?></span>
		</a>
		-->
</form>
    
    
    <h2><?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW'); ?></h2>
    
    <ul class="benefit-lists">
    	<li><?php echo JText::_('COM_COMMUNITY_CONNECT_AND_EXPAND'); ?></li>
    	<li><?php echo JText::_('COM_COMMUNITY_VIEW_PROFILES_AND_ADD_FRIEND'); ?></li>
    	<li><?php echo JText::_('COM_COMMUNITY_SHARE_PHOTOS_AND_VIDEOS'); ?></li>
    	<li><?php echo JText::_('COM_COMMUNITY_GROUPS_INVOLVE'); ?></li>
    </ul>

