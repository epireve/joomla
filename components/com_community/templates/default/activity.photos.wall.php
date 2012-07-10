<?php
/**
 * @package			JomSocial
 * @subpackage 	Template 
 * @copyright		(c) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license			GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>
<ul class ="cDetailList clrfix">
	<li>
		<!-- Show the Avatar, and surround it with link, if any URL is present. -->
		<div class="avatarWrap">
			<?php if ($url) {
			echo '<a href="' . CRoute::_($param->get('url')) . '">';
			} ?>

			<img src="<?php echo $photo->getThumbURI();?>" class="cAvatar cAvatar-Large" />

			<?php if ($url)	{ ?>
				</a>
			<?php } ?>
		</div>
		<!-- End Avatar -->
		
		<!-- Show the Details -->
		<div class="detailWrap alpha"><?php echo JString::substr($act->content, 0, $config->getInt('streamcontentlength'));?></div>
		<!-- End Details -->
	</li>
</ul>