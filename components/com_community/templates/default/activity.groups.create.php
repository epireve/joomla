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
<ul class="cDetailList clrfix">
	<li class="avatarWrap">
		<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>">
			<img class="cAvatar cAvatar-Large" alt="<?php echo $this->escape($group->name );?>" src="<?php echo $group->getThumbAvatar();?>" />
		</a>
	</li>
	<li class="detailWrap">
		<strong><a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>"><?php echo $group->name; ?></a></strong>
		<small>
			<?php echo CStringHelper::truncate(strip_tags($group->description) , $config->getInt('streamcontentlength'));?>
		</small>
	</li>
</ul>
<div class="clr"></div>