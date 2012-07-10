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
<div class="greybox">
	<div>
		<div>
			<div class="cFrontpageSignup">
				
				<!-- Start the Intro text -->
				<div class="cFrontPageLeft">
					<div class="introduction">
						<h1><?php echo JText::_('COM_COMMUNITY_GET_CONNECTED_TITLE'); ?></h1>
						<ul id="featurelist">
						    <li><?php echo JText::_('COM_COMMUNITY_CONNECT_AND_EXPAND'); ?></li>
						    <li><?php echo JText::_('COM_COMMUNITY_VIEW_PROFILES_AND_ADD_FRIEND'); ?></li>
						    <li><?php echo JText::_('COM_COMMUNITY_SHARE_PHOTOS_AND_VIDEOS'); ?></li>
						    <li><?php echo JText::_('COM_COMMUNITY_GROUPS_INVOLVE'); ?></li>
						</ul>
						<?php if ($usersConfig->get('allowUserRegistration')) : ?>
							<div class="joinbutton">
								<a id="joinButton" href="<?php echo CRoute::_( 'index.php?option=com_community&view=register' , false ); ?>" title="<?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW'); ?>">
									<?php echo JText::_('COM_COMMUNITY_JOIN_US_NOW'); ?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>	
				<!-- End Intro text -->
								
				<!-- Start the Login Form -->
				<div class="cFrontPageRight">
					<div class="loginform">
						<form action="<?php echo CRoute::getURI();?>" method="post" name="login" id="form-login" >
							<h2><?php echo JText::_('COM_COMMUNITY_MEMBER_LOGIN'); ?></h2>
							<label>
								<?php echo JText::_('COM_COMMUNITY_USERNAME'); ?><br />
								<input type="text" class="inputbox frontlogin" name="username" id="username" />
							</label>

							<label>
								<?php echo JText::_('COM_COMMUNITY_PASSWORD'); ?><br />
								<input type="password" class="inputbox frontlogin" name="<?php echo COM_USER_PASSWORD_INPUT;?>" id="password" />
							</label>

							<?php if(JPluginHelper::isEnabled('system', 'remember')) : ?>
								<label for="remember">
									<input type="checkbox" alt="<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?>" value="yes" id="remember" name="remember"/>
									<?php echo JText::_('COM_COMMUNITY_REMEMBER_MY_DETAILS'); ?>
								</label>
							<?php endif; ?>

							<div style="text-align: center; padding: 10px 0 5px;">
								<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_LOGIN_BUTTON');?>" name="submit" id="submit" class="button" />
								<input type="hidden" name="option" value="<?php echo COM_USER_NAME;?>" />
								<input type="hidden" name="task" value="<?php echo COM_USER_TAKS_LOGIN;?>" />
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<?php echo JHTML::_( 'form.token' ); ?>
							</div>

							<span>
								<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=reset' ); ?>" class="login-forgot-password"><span><?php echo JText::_('COM_COMMUNITY_FORGOT_YOUR'). ' '. JText::_('COM_COMMUNITY_PASSWORD').'?'; ?></span></a><br />
								<a href="<?php echo CRoute::_( 'index.php?option='.COM_USER_NAME.'&view=remind' ); ?>" class="login-forgot-username"><span><?php echo JText::_('COM_COMMUNITY_FORGOT_YOUR'). ' '. JText::_('COM_COMMUNITY_USERNAME').'?'; ?></span></a>
							</span>
						
							<?php if ($useractivation) { ?>
								<br />
								<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=register&task=activation' ); ?>" class="login-forgot-username">
									<span><?php echo JText::_('COM_COMMUNITY_RESEND_ACTIVATION_CODE'); ?></span>
								</a>
							<?php } ?>
						</form>
						
						<?php echo $fbHtml;?>
				    
					</div>
				</div>
				<!-- End the Login form -->
				
				<div class="jsClr"></div>
	    </div>
		</div>
	</div>
</div>