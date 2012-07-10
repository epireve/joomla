<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	author		string
 * @param	categories	An array of category objects.
 * @param	category	An integer value of the selected category id if 0, not selected. 
 * @params	groups		An array of group objects.
 * @params	pagination	A JPagination object.  
 * @params	isJoined	boolean	determines if the current browser is a member of the group 
 * @params	isMine		boolean is this wall entry belong to me ?
 * @params	config		A CConfig object which holds the configurations for Jom Social
 * @params	sorttype	A string of the sort type. 
 */
defined('_JEXEC') or die();
?>

<?php
// sample of module loading via template
// echo  $this->view('groups')->modPublicDiscussion($categoryId);
?>
<div id="community-groups-wrap">
	<?php
	if( $featuredList )
	{
	?>
	<div class="cRow">
	<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_FEATURED_GROUPS');?></div>
		<div id="cFeatured" class="forGroup">
			<?php
				foreach($featuredList as $group)
				{
			?>
			<div class="cFeaturedItem">
				<div class="cBoxPad clrfix">
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>" class="cFeaturedThumb jomNameTips" 
					title="<?php echo $this->escape($group->name);?>">
		            	<img src="<?php echo $group->getThumbAvatar();?>" alt="<?php echo $this->escape($group->name);?>" />
		            	<span class="cFeaturedOverlay">star</span>
		            </a>
					<?php
					if( $isCommunityAdmin )
					{
					?>
					<div class="album-actions small" style="display: none;">	        
						<a onclick="joms.featured.remove('<?php echo $group->id;?>','groups');" href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>" class="album-action remove-featured"><?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?></a>
					</div>
					<?php
					}
					?>
				</div>
			</div>
			<?php
				}
			?>
		</div>
		<div class="clr"></div>
	</div>
	<?php
	}
	?>

    
        <?php echo $sortings; ?>

        <div class="cLayout clrfix">

            <div class="cSidebar clrfix">
								<!-- CATEGORIES -->
								
								<div class="cModule clrfix">
								<?php if ( $index ) : ?>
									<h3><?php echo JText::_('COM_COMMUNITY_CATEGORIES');?></h3>
									<ul class="cResetList cCategories">
										<li>
										<?php if( $category->parent == COMMUNITY_NO_PARENT && $category->id == COMMUNITY_NO_PARENT ){ ?>
											<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups');?>"><?php echo JText::_( 'COM_COMMUNITY_GROUPS_ALL_GROUPS' ); ?></a>
										<?php }else{ ?>
											<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=display&categoryid=' . $category->parent ); ?>"><?php echo JText::_('COM_COMMUNITY_BACK_TO_PARENT'); ?></a>
										<?php }  ?>
										</li>
										<?php if( $categories ): ?>
											<?php foreach( $categories as $row ): ?>
												<li>
													<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=display&categoryid=' . $row->id ); ?>"><?php echo JText::_( $this->escape($row->name) ); ?><?php if( $row->count > 0 ){ ?><span class="cCount"><?php echo $row->count; ?></span><?php } ?></a>
												</li>
											<?php endforeach; ?>
										<?php else: ?>
											<?php if( $category->parent == COMMUNITY_NO_PARENT && $category->id == COMMUNITY_NO_PARENT ){ ?>
												<li>
													<?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_NOITEM'); ?>
												</li>
											<?php } ?>
										<?php endif; ?>
									</ul>
								<?php endif; ?>
								</div><!-- /CATEGORIES -->
								
                <?php echo $discussionsHTML;?>
            </div>

            <!-- ALL GROUP LIST -->
            <div class="cMain clrfix">
                    <?php echo $groupsHTML;?>
            </div>
        </div>
    
	<div class="clr"></div>
</div>    
