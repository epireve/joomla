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

<ul class="viewtypeSwitcher">
	<li class="<?php if ($viewtype=='mobile') echo 'active'; ?>">
		<a href="<?php echo $link['mobile'] ?>" target="page"><?php echo JText::_('COM_COMMUNITY_MOBILE_VIEW'); ?></a>
	</li>
	<li class="<?php if ($viewtype=='desktop') echo 'active'; ?>">
		<a href="<?php echo $link['desktop'] ?>" target="page"><?php echo JText::_('COM_COMMUNITY_DESKTOP_VIEW'); ?></a>
	</li>
</ul>