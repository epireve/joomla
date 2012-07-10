<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_HIDE_MENU' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_HIDE_MENU_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_HIDE_MENU' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'show_toolbar' , null , $this->config->get('show_toolbar') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_NAME' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_NAME_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_NAME' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="displayname">
						<?php
							$selectedRealName	= ( $this->config->get('displayname') == 'name' ) ? 'selected="true"' : '';
							$selectedUserName	= ( $this->config->get('displayname') == 'username' ) ? 'selected="true"' : '';
						?>
						<option <?php echo $selectedRealName; ?> value="name"><?php echo JText::_('COM_COMMUNITY_REALNAME_OPTION');?></option>
						<option <?php echo $selectedUserName; ?> value="username"><?php echo JText::_('COM_COMMUNITY_USERNAME_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_DATE_STYLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_DATE_STYLE_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_DATE_STYLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="activitydateformat">
						<?php
							$selectedFixedDate	= ( $this->config->get('activitydateformat') == 'fixed' ) ? 'selected="true"' : '';
							$selectedLapseDate	= ( $this->config->get('activitydateformat') == 'lapse' ) ? 'selected="true"' : '';
						?>
						<option <?php echo $selectedFixedDate; ?> value="fixed"><?php echo JText::_('COM_COMMUNITY_FIXED_OPTION');?></option>
						<option <?php echo $selectedLapseDate; ?> value="lapse"><?php echo JText::_('COM_COMMUNITY_LAPSED_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ALLOW_HTML' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_ALLOW_HTML_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ALLOW_HTML' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'allowhtml' , null , $this->config->get('allowhtml') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_USE_EDITOR' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_USE_EDITOR_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_USE_EDITOR' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php
						$editor	= $this->config->get('htmleditor', 'none');
					 	if( $editor == '1' || $editor == '0' )
					 	{
					 		$editor	= 'none';
						}
					?>
					<?php echo JHTML::_('select.genericlist' , $this->getEditors() , 'htmleditor' , null , 'value' , 'text' , $editor );?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_AVATAR_IN_ACTIVITY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_AVATAR_IN_ACTIVITY_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_AVATAR_IN_ACTIVITY' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="showactivityavatar">
						<?php
							$showActivityAvatar	= ( $this->config->get('showactivityavatar') == '1' ) ? 'selected="true"' : '';
							$hideActivityAvatar	= ( $this->config->get('showactivityavatar') == '0' ) ? 'selected="true"' : '';
						?>
						<option <?php echo $showActivityAvatar; ?> value="1"><?php echo JText::_('COM_COMMUNITY_YES_OPTION');?></option>
						<option <?php echo $hideActivityAvatar; ?> value="0"><?php echo JText::_('COM_COMMUNITY_NO_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENTS_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="showactivitycontent">
						<?php
							$showActivityContent	= ( $this->config->get('showactivitycontent') == '1' ) ? 'selected="true"' : '';
							$hideActivityContent	= ( $this->config->get('showactivitycontent') == '0' ) ? 'selected="true"' : '';
						?>
						<option <?php echo $showActivityContent; ?> value="1"><?php echo JText::_('COM_COMMUNITY_YES_OPTION');?></option>
						<option <?php echo $hideActivityContent; ?> value="0"><?php echo JText::_('COM_COMMUNITY_NO_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENT_LENGTH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENT_LENGTH_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_ACTIVITY_CONTENT_LENGTH' ); ?>
					</span>
				</td>
				<td valign="top">       
					<input type="text" name="streamcontentlength" value="<?php echo $this->config->get('streamcontentlength');?>" size="20" /> 
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_SINGULAR_NUMBER' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_SINGULAR_NUMBER_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_SINGULAR_NUMBER' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="singularnumber" value="<?php echo $this->config->get('singularnumber');?>" size="20" /> 
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_PROFILE_DATE_FORMAT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_PROFILE_DATE_FORMAT_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_DISPLAY_PROFILE_DATE_FORMAT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="profileDateFormat" value="<?php echo $this->config->get('profileDateFormat');?>" size="20" />
					<a href="http://dev.mysql.com/doc/refman/5.1/en/date-and-time-functions.html#function_date-format" target="_blank"><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_DISPLAY_AVAILABLE_DATE_FORMATS');?></a> 
				</td>
			</tr>	
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_SHOW_FEATURED' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_SHOW_FEATURED_TIPS'); ?>">
					<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_SHOW_FEATURED' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'show_featured' , null , $this->config->get('show_featured') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>