<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();
?>
<a href="javascript:void(0);" class="community-invite" onclick="joms.invitation.showForm('<?php echo $userIds;?>', '<?php echo $callbackMethod;?>','<?php echo $cid;?>','<?php echo $displayFriends;?>','<?php echo $displayEmail;?>');"><?php echo JText::_('COM_COMMUNITY_INVITE_FRIENDS');?></a>
