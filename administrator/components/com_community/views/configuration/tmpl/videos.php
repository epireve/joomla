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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablevideos' , null ,  $this->config->get('enablevideos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_GUEST_SEARCH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_GUEST_SEARCH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_GUEST_SEARCH' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableguestsearchvideos' , null ,  $this->config->get('enableguestsearchvideos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
                        <tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_PROFILE_VIDEO_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_PROFILE_VIDEO_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_PROFILE_VIDEO_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableprofilevideo' , null ,  $this->config->get('enableprofilevideo') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_UPLOAD_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_UPLOAD_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_UPLOAD_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablevideosupload' , null ,  $this->config->get('enablevideosupload') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_CREATION_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_CREATION_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_CREATION_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="videouploadlimit" value="<?php echo $this->config->get('videouploadlimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_MAP_DEFAULT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_MAP_DEFAULT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_MAP_DEFAULT' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'videosmapdefault' , null ,  $this->config->get('videosmapdefault' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_DELETE_ORIGINAL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_DELETE_ORIGINAL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_DELETE_ORIGINAL' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'deleteoriginalvideos' , null ,  $this->config->get('deleteoriginalvideos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ROOT_FOLDER' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ROOT_FOLDER_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ROOT_FOLDER' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" size="40" name="videofolder" value="<?php echo $this->config->get('videofolder');?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_MAXIMUM_UPLOAD_SIZE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_MAXIMUM_UPLOAD_SIZE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_MAXIMUM_UPLOAD_SIZE' ); ?>
					</span>
				</td>
				<td valign="top">
					<div><input type="text" size="3" name="maxvideouploadsize" value="<?php echo $this->config->get('maxvideouploadsize');?>" /> (MB)</div>
					<div><?php echo JText::sprintf('COM_COMMUNITY_CONFIGURATION_VIDEOS_MAXIMUM_UPLOAD_SIZE_FROM_PHP', $this->uploadLimit );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_FFMPEG_PATH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_FFMPEG_PATH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_FFMPEG_PATH' ); ?>
					</span>
				</td>
				<td valign="top">
					<input name="ffmpegPath" type="text" size="60" value="<?php echo $this->config->get('ffmpegPath');?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_FLVTOOL2_PATH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_FLVTOOL2_PATH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_FLVTOOL2_PATH' ); ?>
					</span>
				</td>
				<td valign="top">
					<input name="flvtool2" type="text" size="60" value="<?php echo $this->config->get('flvtool2');?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_QUANTIZER_SCALE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_QUANTIZER_SCALE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_QUANTIZER_SCALE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->lists['qscale']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_SIZE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_SIZE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_SIZE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->lists['videosSize']; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_CUSTOM_COMMAND' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_CUSTOM_COMMAND_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_CUSTOM_COMMAND' ); ?>
					</span>
				</td>
				<td valign="top">
					<input name="customCommandForVideo" type="text" size="60" value="<?php echo $this->config->get('customCommandForVideo');?>" />
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_PSEUDO_STREAMING' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_PSEUDO_STREAMING_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_PSEUDO_STREAMING' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablevideopseudostream' , null ,  $this->config->get('enablevideopseudostream') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_DEBUGGING' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_DEBUGGING_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_DEBUGGING' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'videodebug' , null ,  $this->config->get('videodebug') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_INTEGRATIONS' ); ?></legend>
	<p><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_INTEGRATIONS_INFO' );?></p>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ACCOUNT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ACCOUNT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ACCOUNT' ); ?>
					</span>
				</td>
				<td valign="top">
					<a onclick="azcommunity.registerZencoderAccount()" class="" href="javascript: void(0);"><?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_CREATE_ACCOUNT'); ?></a>
				</td>
			</tr>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enable_zencoder' , null ,  $this->config->get('enable_zencoder') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_API_KEY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_API_KEY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_VIDEOS_ZENCODER_API_KEY' ); ?>
					</span>
				</td>
				<td valign="top">
					<input name="zencoder_api_key" type="text" size="60" value="<?php echo $this->config->get('zencoder_api_key');?>" />
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>