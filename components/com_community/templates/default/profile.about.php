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
$noData = true;
?>
<div class="cModule">
	<h3><?php echo JText::_('COM_COMMUNITY_ABOUT_ME');?></h3>
	
	<?php if( $isMine ): ?>
	<a class="edit-this" href="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=edit');?>" title="<?php echo JText::_('COM_COMMUNITY_PROFILE_EDIT'); ?>">[ <?php echo JText::_('COM_COMMUNITY_PROFILE_EDIT'); ?> ]</a>
	<?php endif; ?>


	<?php foreach( $profile['fields'] as $groupName => $items ): 
		
			// Gather display data for the group. If there is no data, we can 
			// later completely hide the whole segment
			$hasData = false;
			ob_start();

			?>
			<div class="cProfile-About">
			<?php if( $groupName != 'ungrouped' ): ?>
			<h4><?php echo JText::_( $groupName ); ?></h4>
			<?php endif; ?>

			<dl class="profile-right-info">
				<?php foreach( $items as $item ): ?>
					<?php
					if( CPrivacy::isAccessAllowed( $my->id , $profile['id'] , 'custom' , $item['access'] ) )
					{
						// There is some displayable data here
						$hasData = true;
					?>
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
					<?php
					}
					?>
				<?php endforeach; ?>
			</dl>
		</div>
		<?php 
		$html = ob_get_contents();
		ob_end_clean();

		// We would only display the profile data in the group if there is actually some
		// data to be displayed
		if( $hasData ):
			echo $html;
			$noData = false;
		endif;
		endforeach; 
		
	if ($noData)
		echo ($isMine) ? JText::_('COM_COMMUNITY_PROFILES_SHARE_ABOUT_YOURSELF') : JText::_('COM_COMMUNITY_PROFILES_NO_INFORMATION_SHARE');
?>
	
</div>

	    