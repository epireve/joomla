<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	groups		An array of group objects.
 */
defined('_JEXEC') or die();

$config	= CFactory::getConfig();

if( $groups )
{
	for( $i = 0; $i < count( $groups ); $i++ )
	{
		$group	=& $groups[$i]; 
?>
	<div class="community-groups-results-item">
		<div class="community-groups-results-left">
			<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>"><img class="cAvatar cAvatar-Large" src="<?php echo $group->getThumbAvatar();?>" alt="<?php echo $this->escape($group->name); ?>"/></a>
		</div>
		<div class="community-groups-results-right">
			<?php
			if($group->approvals == COMMUNITY_PRIVATE_GROUP)
			{
				echo '<span class="icon-online-overlay">'.JText::_('COM_COMMUNITY_GROUPS_PRIVATE').'</span>';
			}
			?>
			<h3 class="groupName">
				<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>">
					<strong><?php echo $this->escape($group->name); ?></strong>
				</a>
			</h3>
			<div class="groupDescription"><?php echo ($config->get('allowhtml')) ? $group->description : $this->escape($group->description); ?></div>
			<div class="groupCreated small"><?php echo JText::sprintf('COM_COMMUNITY_GROUPS_CREATE_TIME_ON' , JHTML::_('date', $group->created, JText::_('DATE_FORMAT_LC')) );?></div>
            
			<div class="groupActions">
				<span class="jsIcon1 icon-group" style="margin-right: 5px;">
					<a href="<?php echo CRoute::_( 'index.php?option=com_community&view=groups&task=viewmembers&groupid=' . $group->id ); ?>"><?php echo JText::sprintf((CStringHelper::isPlural($group->membercount)) ? 'COM_COMMUNITY_GROUPS_MEMBER_COUNT_MANY':'COM_COMMUNITY_GROUPS_MEMBER_COUNT', $group->membercount);?></a>
				</span>
				<span class="jsIcon1 icon-discuss" style="margin-right: 5px;">
					<?php echo JText::sprintf((CStringHelper::isPlural($group->discusscount)) ? 'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT_MANY' :'COM_COMMUNITY_GROUPS_DISCUSSION_COUNT', $group->discusscount);?>
				</span>
				<span class="jsIcon1 icon-wall" style="margin-right: 5px;">
					<?php echo JText::sprintf((CStringHelper::isPlural($group->wallcount)) ? 'COM_COMMUNITY_GROUPS_WALL_COUNT_MANY' : 'COM_COMMUNITY_GROUPS_WALL_COUNT', $group->wallcount);?>
				</span>
				<?php
				if( $isCommunityAdmin && $showFeatured )
				{
					if( !in_array($group->id, $featuredList) )
					{
				?>
				<div style="float:right">
					<span class="jsIcon1 icon-addfeatured" style="margin-right: 5px;">	            
			            <a onclick="joms.featured.add('<?php echo $group->id;?>','groups');" href="javascript:void(0);">	            	            
			            <?php echo JText::_('COM_COMMUNITY_MAKE_FEATURED'); ?>
			            </a>
			        </span>
			    	</div>
				<?php			
					}
				}
				?>
				
			</div>
		</div>
		<div style="clear: both;"></div>
	</div>
<?php
	}
}
else
{
?>
	<div class="group-not-found"><?php echo JText::_('COM_COMMUNITY_GROUPS_NOITEM'); ?></div>
<?php
}
?>

<?php if (!is_null($pagination)) {?>
<div class="pagination-container">
	<?php echo $pagination->getPagesLinks(); ?>
</div>
<?php } ?>
