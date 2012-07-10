<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form name="jsform-profile-ajaxupdateurl" id="jsform-profile-ajaxupdateurl" action="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=updateAlias');?>" method="post">
	<div>Current profile url is <?php echo $prefixURL;?></div>
	<input type="hidden" name="userid" value="<?php echo $user->id;?>" />
</form>