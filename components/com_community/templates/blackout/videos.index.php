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

<div id="cCategories">
	<div class="cCategories-inner">
		<h3><?php echo JText::_('COM_COMMUNITY_CATEGORIES');?></h3>
    
	    <ul class="category-items">
	    <?php if( $categories ): ?>
	        <li class="category-item">
	            <a href="<?php echo CRoute::_($allVideosUrl);?>">
	                <?php echo JText::_( 'COM_COMMUNITY_VIDEOS_ALL_DESC' ); ?>
	            </a>
	        </li>
	        <?php foreach( $categories as $row ): ?>
	            <li style="width: 33%; background: none; display: inline;float:left; padding: 0;">
	                <a href="<?php echo CRoute::_($catVideoUrl . $row->id ); ?>">
	                    <?php echo JText::_( $this->escape($row->name) ); ?>
	                </a> ( <?php echo $row->count; ?> )
	            </li>
	        <?php endforeach; ?>
	    <?php else: ?>
	        <li><?php echo JText::_('COM_COMMUNITY_GROUPS_CATEGORY_NOITEM'); ?></li>
	    <?php endif; ?>
	    </ul>
	    <div class="clr"></div>
		</div>
</div>


<?php echo $sortings; ?>

<div class="video-index">
	<?php echo $videosHTML; ?>
</div>