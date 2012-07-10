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
<ul class ="cDetailList clrfix">
<?php
foreach( $photos as $photo )
{
?>
	<li class="avatarWrap">
		<a href="<?php echo $photo->getPhotoLink();?>" title="<?php echo $this->escape($photo->caption);?>">
			<?php 
			$user = CFactory::getUser($photo->creator); 
			?>
			<img alt="<?php echo $this->escape($photo->caption);?>" src="<?php echo $photo->getThumbURI();?>" class="cAvatar cAvatar-Large jomNameTips" title="<?php echo JText::sprintf('COM_COMMUNITY_PHOTOS_UPLOADED_BY' , $user->getDisplayName() );?>" />
		</a>
	</li>
<?php
}
?>
</ul>