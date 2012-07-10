<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<ul class="cResetList cThumbList clrfix">
	<?php
	if(!empty($data)){
	foreach( $data as $video )
	{
	?>
	<li class="jomNameTips" id="<?php echo "video-" . $video->getId() ?>" title="<?php echo $this->escape($video->title); ?>">
		<a class="video-thumb-url" href="<?php echo $video->getURL(); ?>">
			<img src="<?php echo $video->getThumbNail(); ?>" style="width:103px; height:75px;" alt="<?php echo $video->getTitle(); ?>" class="cAvatar" />
			<span class="video-durationHMS"><?php echo $video->getDurationInHMS(); ?></span>
		</a>
	</li>
	<?php }
	} else {

	    echo JText::_('COM_COMMUNITY_VIDEOS_NO_VIDEO');
	}
	?>
</ul>