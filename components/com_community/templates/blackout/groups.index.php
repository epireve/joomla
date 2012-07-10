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
if( $featuredList )
{
?>
<div class="cToolbarBand">

	<div class="bandContent">
	<h3 class="bandContentTitle"><?php echo JText::_('COM_COMMUNITY_FEATURED_GROUPS');?></h3>
<?php
	foreach($featuredList as $group)
	{
?>
	<div class="featured-items">
		<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $group->id );?>"><img class="cAvatar" src="<?php echo $group->getAvatar();?>" alt="<?php echo $this->escape($group->name); ?>" /><span style="display: block;font-weight:700;"><?php echo $this->escape($group->name); ?></span></a>
<?php
		if( $isCommunityAdmin )
		{
?>
	<div class="icon-removefeatured"><a onclick="joms.featured.remove('<?php echo $group->id;?>','groups');" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?></a></div>
<?php
		}
?>
	</div>
<?php
	}
?>
		<div class="clr"></div>
	</div>
	<div class="bandFooter"><div class="bandFooter_inner"></div></div>
</div>
<?php
}
?>
<?php if ( $index ) : ?>
<div id="cCategories">
	<h3><?php echo JText::_('COM_COMMUNITY_CATEGORIES');?></h3>
	<ul class="category-items">
	<?php if( $categories ): ?>
		<li class="category-item">
			<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups');?>">
				<?php echo JText::_( 'COM_COMMUNITY_GROUPS_ALL_GROUPS' ); ?>
			</a>
		</li>
		<?php foreach( $categories as $row ): ?>
			<li class="category-item">
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=display&categoryid=' . $row->id ); ?>">
					<?php echo $this->escape($row->name); ?>
				</a> ( <?php echo $row->count; ?> )
			</li>
		<?php endforeach; ?>
	<?php else: ?>
		<li><?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_NOITEM'); ?></li>
	<?php endif; ?>
	</ul>
	<div class="clr"></div>
</div>
<?php endif; ?>


<?php echo $sortings; ?>

<?php if( $index ): ?>
<h3 style="text-decoration: underline;">
	<?php echo ( isset($category) && ($category->id != '0') ) ? JText::sprintf('COM_COMMUNITY_GROUPS_CATEGORY_NAME' , $this->escape($category->name)) : JText::_( 'COM_COMMUNITY_GROUPS_ALL_GROUPS' ); ?>
</h3>
<?php endif; ?>

<div id="community-groups-results-wrapper">
	<?php echo $groupsHTML;?>
</div>