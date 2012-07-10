<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * 
 */
defined('_JEXEC') or die();
?>

<?php echo $featuredHTML; ?>

<div class="cRow">
	<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_VIDEOS_CATEGORY');?></div>
    <ul class="cResetList c3colList">
		<li>
			<?php if( $category->parent == COMMUNITY_NO_PARENT && $category->id == COMMUNITY_NO_PARENT ){ ?>
				<a href="<?php echo CRoute::_($allVideosUrl);?>"><?php echo JText::_( 'COM_COMMUNITY_VIDEOS_ALL_DESC' ); ?></a>
			<?php }else{ ?>
				<?php
					$catid = ''; 
					if( $category->parent != 0) {
						$catid = '&catid=' . $category->parent;
					} 
				?>
				<a href="<?php echo CRoute::_('index.php?option=com_community&view=videos' . $catid ); ?>"><?php echo JText::_('COM_COMMUNITY_BACK_TO_PARENT'); ?></a>
			<?php }  ?>
		</li>
    <?php if( $categories ): ?>
		
		<?php foreach( $categories as $row ): ?>
		<li>
				<a href="<?php echo CRoute::_($catVideoUrl . $row->id ); ?>">
						<?php echo JText::_($this->escape($row->name)); ?>
				</a> <?php echo empty($row->count) ? '' : ' ('.$row->count.')'; ?>
		</li>
		<?php endforeach; ?>
    <?php else: ?>
        <li><?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_NOITEM'); ?></li>
    <?php endif; ?>
    </ul>
    <div class="clr"></div>
</div>


<?php echo $sortings; ?>

<div class="video-index">
	<?php echo $videosHTML; ?>
</div>