<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	albums	An array of album objects.
 * @param	user	Current browser's CUser object. 
 * @params	isOwner		boolean Determines if the current photos view belongs to the browser
 */
defined('_JEXEC') or die();
?>
<div>
    <!--div align="right"><a href="" onclick="joms.notifications.showUploadPhoto();return false;" class="btn-photo-uploader yellow-grad" title=""><span class="">Upload New Photos</span></a></div-->
	<?php echo $albumsHTML; ?>
</div>