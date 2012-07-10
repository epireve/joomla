<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();
?>
<?php if( $multiprofile->approvals && !$isCommunityAdmin ){ ?>
	<div><?php echo JText::sprintf('COM_COMMUNITY_PROFILE_CHANGE_REQUIRE_APPROVALS_INFO' , $multiprofile->name );?></div>
	<div style="margin-top: 5px;"><a href="<?php echo CRoute::_('index.php?option=com_community&view=frontpage');?>"><?php echo JText::_('COM_COMMUNITY_RETURN_TO_FRONTPAGE');?></a></div>
<?php } else { ?>
	<div><?php echo JText::sprintf('COM_COMMUNITY_PROFILE_CHANGE_INFO' , $multiprofile->name );?></div>
	<div style="margin-top: 5px;"><a href="<?php echo CRoute::_('index.php?option=com_community&view=profile');?>"><?php echo JText::_('COM_COMMUNITY_RETURN_TO_PROFILE');?></a></div>
<?php } ?>