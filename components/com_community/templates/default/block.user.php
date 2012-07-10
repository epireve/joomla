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
<div id="blockuser" class="page-action">
    
	<?php if( $isBlocked ) : ?>
		<a href="javascript:void(0);" class="icon-blockuser" onclick="joms.users.unBlockUser('<?php echo $userId;  ?>');"><?php echo JText::_('COM_COMMUNITY_UNBLOCK_USER'); ?></a>
	<?php else : ?>
	    <a href="javascript:void(0);" class="icon-blockuser" onclick="joms.users.blockUser('<?php echo $userId;  ?>');"><?php echo JText::_('COM_COMMUNITY_BLOCK_USER'); ?></a>
	<?php endif ; ?>
	
</div>