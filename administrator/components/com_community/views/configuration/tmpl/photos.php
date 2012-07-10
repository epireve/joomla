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
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="300" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_ENABLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_ENABLE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_ENABLE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enablephotos' , null ,  $this->config->get('enablephotos') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_CREATION_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_CREATION_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_CREATION_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="photouploadlimit" value="<?php echo $this->config->get('photouploadlimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_PAGINATION_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_PAGINATION_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_PAGINATION_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="photopaginationlimit" value="<?php echo $this->config->get('photopaginationlimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_MAP_DEFAULT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_MAP_DEFAULT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_MAP_DEFAULT' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'photosmapdefault' , null ,  $this->config->get('photosmapdefault' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_MAXIMUM_UPLOAD_SIZE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_MAXIMUM_UPLOAD_SIZE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_MAXIMUM_UPLOAD_SIZE' ); ?>
					</span>
				</td>
				<td valign="top">
					<div><input type="text" size="3" name="maxuploadsize" value="<?php echo $this->config->get('maxuploadsize');?>" /> (MB)</div>
					<div><?php echo JText::sprintf('COM_COMMUNITY_CONFIGURATION_PHOTOS_MAXIMUM_UPLOAD_SIZE_FROM_PHP', $this->uploadLimit );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_DELETE_ORIGINAL' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_DELETE_ORIGINAL_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_DELETE_ORIGINAL' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'deleteoriginalphotos' , null ,  $this->config->get('deleteoriginalphotos' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_IMAGEMAGICK_PATH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_IMAGEMAGICK_PATH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_IMAGEMAGICK_PATH' ); ?>
					</span>
				</td>
				<td valign="top">
					<input name="magickPath" type="text" size="60" value="<?php echo $this->config->get('magickPath');?>" />
				</td>
			</tr>
			
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_SET_COVER' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_SET_COVER_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_SET_COVER' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'autoalbumcover' , null ,  $this->config->get('autoalbumcover' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_ROTATE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_ROTATE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_AUTO_ROTATE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'photos_auto_rotate' , null ,  $this->config->get('photos_auto_rotate' ) , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_OUTPUT_QUALITY' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_PHOTOS_OUTPUT_QUALITY_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PHOTOS_OUTPUT_QUALITY' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo $this->lists['imgQuality']; ?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>