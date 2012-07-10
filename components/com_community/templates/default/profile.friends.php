<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	friends		array or CUser (all user)
 * @param	total		integer total number of friends
 * @param	user		CFactory User object 
 */
defined('_JEXEC') or die();
?>

<div class="cModule">
	<h3><span><?php echo JText::_('COM_COMMUNITY_PROFILE_FRIENDS'); ?></span></h3>
	<ul class="cResetList cThumbList clrfix">
	<?php
	if( $friends )
	{
	?>
		<?php
		for($i = 0; ($i < 12) && ($i < count($friends)); $i++) {
			$friend =& $friends[$i];
		?>
		<li>
			<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$friend->id); ?>"><img alt="<?php echo $friend->getDisplayName();?>" title="<?php echo $friend->getTooltip(); ?>" src="<?php echo $friend->getThumbAvatar(); ?>" class="cAvatar cAvatar-sidebar jomNameTips" /></a>
		</li>
		<?php } ?>
	<?php
	}
	else
	{
	?>
		<li><?php echo JText::_('COM_COMMUNITY_NO_FRIENDS_YET');?></li>
	<?php
	}
	?>
	</ul>
	<div style="clear: both;"></div>
	<div class="app-box-footer">
		<a href="<?php echo CRoute::_('index.php?option=com_community&view=friends&userid=' . $user->id ); ?>">
			<span><?php echo JText::_('COM_COMMUNITY_FRIENDS_VIEW_ALL'); ?></span>
			<span>(<?php echo $total;?>)</span>
		</a>
	</div>
	
</div>
