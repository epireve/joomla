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
foreach( $members as $user )
{
?>
<li class="avatarWrap">
	<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );?>">
		<img alt="<?php echo $this->escape($user->getDisplayName());?>" src="<?php echo $user->getThumbAvatar();?>" class="cAvatar cAvatar-Large jomNameTips" title="<?php echo cAvatarTooltip($user);; ?>" />
	</a>
</li>
<?php
}
?>
</ul>