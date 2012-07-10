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
<ul class ="cDetailList clrfix">
	<li class="avatarWrap">
		<a href="<?php echo CUrlHelper::eventLink($event->id); ?>"><img style="width: 64px; height: auto" class="cAvatar" src="<?php echo $event->getThumbAvatar();?>" /></a>
	</li>
	<li class="detailWrap">
		
		<strong><a href="<?php echo CUrlHelper::eventLink($event->id); ?>"><?php echo strip_tags($event->title); ?></a></strong>
		<small>
			<?php if (strlen(strip_tags($event->description))) echo CStringHelper::truncate(strip_tags($event->description) , $config->getInt('streamcontentlength')).'<br />';?>
			<!--
				Ross added the startdate, enddate.
				Need dev's help to convert it into readable resource.
				It supposed to display as shown here:
				http://i.firdouss.com/images/screenpkp.png
			-->
			<?php echo $event->startdate; ?><br />
			<?php echo $event->enddate; ?><br />
			<?php echo strip_tags($event->location); ?><br />
		</small>
	</li>
</ul>
<div class="clr"></div>