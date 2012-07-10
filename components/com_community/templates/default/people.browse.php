<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	author		string
 * @param	categories	An array of category objects.
 * @params	groups		An array of group objects.
 * @params	pagination	A JPagination object.
 * @params	isJoined	boolean	determines if the current browser is a member of the group
 * @params	isMine		boolean is this wall entry belong to me ?
 * @params	config		A CConfig object which holds the configurations for Jom Social
 * @params	sortings	A html data that contains the sorting toolbar
 */
defined('_JEXEC') or die();
CFactory::load( 'libraries' , 'messaging' );

if( $featuredList && $showFeaturedList )
{
?>
	<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_FEATURED_MEMBERS');?></div>
	<div id="cFeatured" class="forPeople">
<?php
  $x =1;
	foreach($featuredList as $id)
	{
		$user	= CFactory::getUser( $id );
	?>
	<div class="album cFeaturedItem">
		<div class="cBoxPad clrfix">
			<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );?>" class="cFeaturedThumb">
				<img class="cAvatar cAvatar-Large" src="<?php echo $user->getThumbAvatar();?>" alt="<?php echo $user->getDisplayName(); ?>"/>
			</a>
	
			<div class="cFeatured-Name">
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $user->id );?>"><?php echo $user->getDisplayName(); ?></a>
				<div class="cFeatured-Status jomNameTips" title="<?php echo $user->getStatus(); ?>">
					<?php echo $user->getStatus(); ?>
				</div>
				<a class="cFeatured-icons" onclick="<?php echo CMessaging::getPopup($user->id); ?>" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?>"><span class="jsIcon1 icon-write"></span></a>
			</div>

			<?php if( $isCommunityAdmin ) { ?>
			<div class="album-actions small" style="display: none">
				<a onclick="joms.featured.remove('<?php echo $user->id;?>','search');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>" class="album-action remove-featured">
				<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>
			</a>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php
		}
	?>
		<div class="clr"></div>
	</div>
<?php
}
?>
<?php echo $sortings; ?>
<?php if( !empty( $data ) ) { ?>
	<?php foreach( $data as $row ) : ?>
		<?php $displayname = $row->user->getDisplayName(); ?>
		<?php if(!empty($row->user->id) && !empty($displayname)) : ?>
		<div class="mini-profile">
			<div class="mini-profile-avatar">
				<a href="<?php echo $row->profileLink; ?>"><img class="cAvatar cAvatar-Large" src="<?php echo $row->user->getThumbAvatar(); ?>" alt="<?php echo $row->user->getDisplayName(); ?>" /></a>
			</div>
			<div class="mini-profile-details">
				<h3 class="name">
					<a href="<?php echo $row->profileLink; ?>"><strong><?php echo $row->user->getDisplayName(); ?></strong></a>
				</h3>
				<div class="mini-profile-details-status"><?php echo $row->user->getStatus() ;?></div>
				<div class="mini-profile-details-action">
					<div class="jsLft">
					    <span class="jsIcon1 icon-group">
					    	<?php echo JText::sprintf( (CStringHelper::isPlural($row->friendsCount)) ? 'COM_COMMUNITY_FRIENDS_COUNT_MANY' : 'COM_COMMUNITY_FRIENDS_COUNT', $row->friendsCount);?>
					    </span>

				    <?php if( $config->get('enablepm') && $my->id != $row->user->id ): ?>
				        <span class="jsIcon1 icon-write">
				            <a onclick="<?php echo CMessaging::getPopup($row->user->id); ?>" href="javascript:void(0);">
				            <?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?>
				            </a>
				        </span>
			        <?php endif; ?>

					<?php if($row->addFriend) { ?>
					    <span class="jsIcon1 icon-add-friend">
							<?php if(isset($row->isMyFriend) && $row->isMyFriend==1){ ?>
							    <a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $row->user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADDED_AS_FRIEND'); ?></span></a>
							<?php } else { ?>
							    <a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $row->user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span></a>
							<?php } ?>
						</span>
					<?php } else { ?>
					    <?php if(($my->id != $row->user->id) && ($my->id !== 0)){ ?>
					     <span class="jsIcon1 icon-add-friend">
					     <a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $row->user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADDED_AS_FRIEND'); ?></span></a>
					     </span>
					    <?php }elseif($my->id == 0){ ?>
                                            <span class="jsIcon1 icon-add-friend">
					     <a href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $row->user->id;?>')"><span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span></a>
					     </span>
					<?php }} ?>
					</div>
					<?php
					if( $isCommunityAdmin )
					{
						if( !in_array($row->user->id, $featuredList) )
						{
					?>
					<div class="jsRgt">
						<span class="jsIcon1 icon-addfeatured" id="featured-<?php echo $row->user->id;?>">
				            <a onclick="joms.featured.add('<?php echo $row->user->id;?>','search');" href="javascript:void(0);">
				            <?php echo JText::_('COM_COMMUNITY_MAKE_FEATURED'); ?>
				            </a>
				        </span>
					</div>
					<?php
						}
					}
					?>
				</div>

				<?php if($row->user->isOnline()): ?>
				<span class="icon-online-overlay">
			    	<?php echo JText::_('COM_COMMUNITY_ONLINE'); ?>
			    </span>
			    <?php endif; ?>


			</div>
			<div class="clr"></div>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>

	<?php echo (isset($pagination)) ? '<div class="pagination-container">'.$pagination->getPagesLinks().'</div>' : ''; ?>
<?php
	}
	else
	{
?>
		<div class="advance-not-found"><?php echo JText::_('COM_COMMUNITY_SEARCH_NO_RESULT');?></div>
<?php
	}
?>
