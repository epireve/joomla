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
<h3><span><?php echo JText::_('COM_COMMUNITY_GROUPS_MEMBERS'); ?></span></h3>

<div class="app-box-content">	
    <div id="latest-members-nav" class="filterlink" style="width: 100%">
        <div>
            <a class="newest-member active-state" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_NEWEST_MEMBERS') ?></a>
            <a class="featured-member" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_FEATURED') ?></a>
            <a class="active-member" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_ACTIVE_MEMBERS') ?></a>
            <a class="popular-member" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_POPULAR_MEMBERS') ?></a>
        </div>
        <div class="clr"></div>
    </div>
		<div class="clr"></div>
    <div id="latest-members-container"><?php echo $memberList ?></div>
		<div class="clr"></div>
</div>

<div class="app-box-footer">
    <a href="<?php echo CRoute::_('index.php?option=com_community&view=search&task=browse' ); ?>" class="app-title-link">
		<?php echo JText::_( 'COM_COMMUNITY_FRONTPAGE_BROWSE_ALL' ); ?>
		<?php if( $this->params->get('showmembercount') ){ ?>
		(<?php echo $totalMembers;?>)
		<?php } ?>
	</a>
</div>