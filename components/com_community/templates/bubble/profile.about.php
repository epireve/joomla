<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	profile			A Profile object that contains profile fields for this specific user
 * @param	profile->
 * @params	isMine		boolean is this profile belongs to me?
 */
defined('_JEXEC') or die();
?>
<h2 class="app-box-title" style="position: relative;">
	<?php echo JText::_('COM_COMMUNITY_ABOUT_ME');?>
    <?php if( $isMine ): ?>
	<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=edit');?>" class="app-title-link">
		<span><?php echo JText::_('COM_COMMUNITY_PROFILE_EDIT'); ?></span>
	</a>
	<?php endif; ?>
</h2>
	

<?php
	$i=1;
	foreach( $profile['fields'] as $groupName => $items )
	{
?>
<div class="infoGroup">
<div class="infoGroup">
	<?php if( $groupName != 'ungrouped' ): ?>
	<h4 class="infoGroupTitle"><?php echo JText::_( $groupName ); ?></h4>
	<?php endif; ?>
	
	<dl class="profile-right-info">
		<?php foreach( $items as $item ): ?>
			<dt><?php echo JText::_( $item['name'] ); ?></dt>
	    	<?php if( !empty($item['searchLink']) && is_array($item['searchLink']) ): ?>
				<dd>
					<?php foreach($item['searchLink'] as $linkKey=>$linkValue): ?>
					<?php $item['value'] = $linkKey; ?>
						<a href="<?php echo $linkValue; ?>"><?php echo CProfileLibrary::getFieldData( $item ) ?></a><br />
					<?php endforeach; ?>

				</dd>
			<?php else: ?>
				<dd>
					<?php if(!empty($item['searchLink'])) :?>
						<a href="<?php echo $item['searchLink']; ?>">
					<?php endif; ?>

					<?php echo CProfileLibrary::getFieldData( $item ); ?>

					<?php if(!empty($item['searchLink'])) :?>
						</a>
					<?php endif; ?>
				</dd>
			<?php endif; ?>
	    <?php endforeach; ?>
	</dl>
</div>
</div>

<?php
	if ($i==3)
	{
		echo '<div class="clr"></div>';
		$i=0;
	}
	$i++;
	}
?>
<div class="clr"></div>