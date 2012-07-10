<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<table class="blockUnregister" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td colspan="2" valign="top" align="center">
			<div class="message" style="margin-bottom: 10px;"><?php echo JText::_('COM_COMMUNITY_PLEASE_LOGIN_OR_REGISTER');?></div>
		</td>
	</tr>
	<tr>
		<td valign="top" width="55%">
	        <h3><?php echo JText::_('COM_COMMUNITY_REGISTER_NOW_TO_GET_CONNECTED');?></h3>
	        <ul id="featurelist">
	            <li><?php echo JText::_('COM_COMMUNITY_CONNECT_AND_EXPAND');?></li>
	            <li><?php echo JText::_('COM_COMMUNITY_VIEW_PROFILES_AND_ADD_FRIEND');?></li>
	            <li><?php echo JText::_('COM_COMMUNITY_SHARE_PHOTOS_AND_VIDEOS');?></li>
	            <li><?php echo JText::_('COM_COMMUNITY_GROUPS_INVOLVE');?></li>
	        </ul>
		<?php if ($usersConfig->get('allowUserRegistration')) : ?>
	        <div style="text-align: center;">
		    <a id="joinButton2" href="<?php echo CRoute::_( 'index.php?option=com_community&view=register' , false );?>" title="<?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW');?>"><?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW');?></a>
		</div>
		<?php endif; ?>
		</td>
		<td valign="top">
		    <div class="loginform" style="margin-left: 10px; padding-left: 30px;">
		    	<form action="<?php echo CRoute::getURI();?>" method="post" name="login" id="form-login" >
		        <h3><?php echo JText::_('COM_COMMUNITY_MEMBER_LOGIN');?></h3>
		            <label for="smallusername"><?php echo JText::_('COM_COMMUNITY_USERNAME');?><br />
		                <input type="text" class="inputbox frontlogin" name="username" id="smallusername" />
		            </label>
		            <label form="smallpassword"><?php echo JText::_('COM_COMMUNITY_PASSWORD');?>
		                <input type="password" class="inputbox frontlogin" name="<?php echo COM_USER_PASSWORD_INPUT;?>" id="smallpassword" />
		            </label>
					<label for="remember">
						<input type="checkbox" alt="<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS');?>" value="yes" id="smallremember" name="remember"/>
						<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS');?>
					</label>
					<div style="text-align: center;">
					    <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_LOGIN');?>" name="submit" id="smallsubmit" class="button" />
						<input type="hidden" name="option" value="<?php echo COM_USER_NAME;?>" />
						<input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN;?>" />
						<input type="hidden" name="return" value="<?php echo $uri;?>" />
						<?php echo JHTML::_( 'form.token' );?>
					</div>
		        </form>
		    </div>			
		</td>
	</tr>
</table>