<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>
<form name="editvideo" action="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=saveVideo'); ?>" method="post">
<table cellspacing="0" class="admintable" border="0" width="100%">
	<tbody>
		
		<tr>
			<td class="key"><label class="label" for="title"><?php echo JText::_('COM_COMMUNITY_VIDEOS_TITLE');?></label></td>
			<td>:</td>
			<td>
				<span>
					<input type="text" id="title" name="title" class="inputbox" value="<?php echo $this->escape($video->title);?>" style="width: 300px;" />
				</span>
			</td>
		</tr>
		<tr>
			<td class="key"><label class="label" for="description"><?php echo JText::_('COM_COMMUNITY_VIDEOS_DESCRIPTION');?></label></td>
			<td>:</td>
			<td>
				<textarea name="description" style="width: 300px;" rows="8" id="description"><?php echo $this->escape($video->description); ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key"><label class="label" for="category"><?php echo JText::_('COM_COMMUNITY_VIDEOS_CATEGORY');?></label></td>
			<td>:</td>
			<td>
				<?php  echo $categoryHTML; ?>
			</td>
		</tr>
		
		<tr>
			<td class="key"><label class="label" for="location"><?php echo JText::_('COM_COMMUNITY_VIDEOS_LOCATION');?></label></td>
			<td>:</td>
			<td>
				<span>
					<input type="text" id="title" name="location" class="inputbox" value="<?php echo $this->escape($video->location);?>" style="width: 300px;" />
				</span>
			</td>
		</tr>
		
		<?php
			if($showPrivacy)
			{
		?>
		<tr>
			<td class="key"><label class="label" for="description"><?php echo JText::_('COM_COMMUNITY_VIDEOS_WHO_CAN_SEE');?></label></td>
			<td>:</td>
			<td>
				<?php echo CPrivacy::getHTML( 'permissions', $video->permissions, COMMUNITY_PRIVACY_BUTTON_LARGE ); ?>
			</td>
		</tr>
		<?php
			}
		?>
	</tbody>
</table>
<input type="hidden" name="id" value="<?php echo $video->id;?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="view" value="videos" />
<input type="hidden" name="task" value="saveVideo" /> 
<input type="hidden" name="redirectUrl" value="<?php echo $redirectUrl;?>" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<br />
<br />
<br />
<br />
<br />
<br />