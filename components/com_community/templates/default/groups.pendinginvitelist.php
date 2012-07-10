<?php

/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
?>

<div class="cModule cPending">
    <h3><?php echo JText::_('COM_COMMUNITY_GROUPS_PENDING_INVITATIONS');?></h3>
    <ul class="cResetList clrfix">
	<?php
	if($groups)
	{
		for( $i = 0; $i < count( $groups ); $i++ )
		{
			$group	=&  $groups[$i];
	?>
	<li class="jomNameTips" original-title="<?php echo $group->name; ?>">
	    <div class="list-right">
		<div class="small"><a href="" class="response"><?php echo JText::_('COM_COMMUNITY_GROUPS_INVITATION_RESPONSE');?></a></div>
	    </div>
	    <div class="list-left">
		<div class="small"><a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id ); ?>"><?php echo $group->name; ?></a></div>
		<div class="small"><?php echo JText::sprintf((CStringHelper::isPlural($group->membercount)) ? 'COM_COMMUNITY_GROUPS_MEMBER_COUNT_MANY':'COM_COMMUNITY_GROUPS_MEMBER_COUNT', $group->membercount);?></div>
	    </div>
	</li>
	<?php
		}
	}else{
	?>
	<li class="small"><?php echo JText::_('COM_COMMUNITY_GROUPS_NO_INVITATIONS'); ?></li>
	<?php
	}
	?>
    </ul>
    <div class="app-box-footer">
	<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups'); ?>">View all groups</a>
    </div>
</div>
