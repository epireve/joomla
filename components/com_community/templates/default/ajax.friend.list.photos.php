
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
?>
<div>
	<label class="photoTagFriend" id="photoTagFriend-<?php echo $user->id;?>">
		<input type="radio" value="<?php echo $user->id;?>" name="photoTagFriendsId">
		<span><?php echo $user->getDisplayName();?></span>
	</label>
</div>
<?php
}
?>