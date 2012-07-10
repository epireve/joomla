
 <?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

<?php
//s:foreach
$i = 0;
foreach( $friends as $id )
{
$user			= CFactory::getUser( $id );
$invited		= in_array( $user->id , $selected );
?>

<li id="invitation-friend-<?php echo $user->id;?>">
	<div class="invitation-wrap clrfix">
		<img class="invitation-avatar" src="<?php echo addslashes($user->getThumbAvatar());?>">
		<div class="invitation-detail">
			<div class="invitation-name"><?php echo $user->getDisplayName();?></div>
			<?php
			if(!$invited){
			?>
				<div class="invitation-check">
				<input type="checkbox" onclick="joms.invitation.selectMember('#invitation-friend-<?php echo $user->id;?>');" value="<?php echo $user->id;?>" name="friends" id="friend-<?php echo $user->id;?>">
				<label for="friend-<?php echo $user->id;?>"><?php echo JText::_('COM_COMMUNITY_INVITE_SELECTED');?></label>
				</div>
			<?php 
			} else {
			?>
				<div><?php echo JText::_('COM_COMMUNITY_INVITE_INVITED');?></div>
			<?php
			}
			?>
		</div>
	</div>
</li>
<?php
}
?>