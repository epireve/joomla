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
<div id="social-bookmarks" class="page-action">
	<a class="icon-bookmark" href="javascript:void(0);" onclick="joms.bookmarks.show('<?php echo $uri;?>');"><span><?php echo JText::_('COM_COMMUNITY_SHARE_THIS');?></span></a>
</div>