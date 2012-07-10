<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>

<div class="denied-box">
<h3 style="margin: 0;"><?php echo JText::_('COM_COMMUNITY_MEMBER_LOGIN');?></h3>
<?php echo JText::_('COM_COMMUNITY_PERMISSION_DENIED_WARNING');?>
<div class="loginform" style="padding-top: 15px;">
	<form action="<?php echo CRoute::getURI();?>" method="post" name="login" id="form-login" >
	
		<label for="username" style="display:inline; float: left; width: 100px;"><?php echo JText::_('COM_COMMUNITY_USERNAME'); ?></label>
		<input type="text" class="inputbox frontlogin" name="username" id="username" style="width: 200px; margin-bottom: 5px;" />
		
		<div style="clear: left;"></div>		

		<label for="passwd" style="display:inline; float: left; width: 100px;"><?php echo JText::_('COM_COMMUNITY_PASSWORD'); ?></label>
		<input type="password" class="inputbox frontlogin" name="<?php echo COM_USER_PASSWORD_INPUT;?>" id="password" style="width: 200px; margin-bottom: 5px;" />

		<div style="clear: left;"></div>		

		<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
		<label for="remember" style="padding: 4px 0 4px 100px;">
			<input type="checkbox" alt="<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?>" value="yes" id="remember" name="remember"/>
			<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?>
		</label>
		<?php endif; ?>

		<div style="padding: 4px 0 0 100px">
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_LOGIN_BUTTON');?>" name="submit" id="submit" class="button" />
			<input type="hidden" name="option" value="<?php echo COM_USER_NAME;?>" />
			<input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN;?>" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
		
		<div style="padding: 12px 0 0 100px">
			<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=reset' ); ?>" class="login-forgot-password"><span><?php echo JText::_('COM_COMMUNITY_FORGOT_PASSWORD'); ?></span></a>
			<br/>
			<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=remind' ); ?>" class="login-forgot-username"><span><?php echo JText::_('COM_COMMUNITY_FORGOT_USERNAME'); ?></span></a>
			<br/>
			<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=register' ); ?>"><span><?php echo JText::_('COM_COMMUNITY_CREATE_ACCOUNT'); ?></span></a>
		</div>
	</form>
	<?php echo $fbHtml;?>
</div>
</div>