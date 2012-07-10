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

<?php 
if(empty($groupId))
{
	echo $this->view('photos')->modFeaturedAlbum(); 
}
?>

<div>
	<?php echo $albumsHTML; ?>
</div>