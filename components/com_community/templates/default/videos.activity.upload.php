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
<div>
	<div class="video-thumb" style="padding: 0; padding-right: 8px;">
		<a class="video-thumb-url" href="<?php //echo $url;?>javascript:joms.walls.showVideoWindow('<?php echo $video->getId(); ?>')"><img alt="<?php echo $this->escape( $video->getTitle() );?>" style="width: 112px; height: 84px;" src="<?php echo $video->getThumbnail();?>"/></a>
		<span class="video-durationHMS" style="right: 13px; bottom: 5px;"><?php echo $duration;?></span>
	</div>
	<strong><a href="javascript:joms.walls.showVideoWindow('<?php echo $video->getId(); ?>')"><?php echo $video->getTitle(); ?></a></strong>
	<small>
	<?php echo CStringHelper::truncate( $video->getDescription() , $config->getInt('streamcontentlength'));?>
	</small>
	<div class="clr"></div>
</div>