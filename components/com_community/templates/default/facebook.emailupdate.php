<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<div style="margin-bottom: 10px;font-weight: 700;"><?php echo JText::_('COM_COMMUNITY_EMAIL_NOT_UPDATED');?></div>
<form name="facebook-email-update" id="facebook-email-update" action="<?php echo CRoute::_('index.php?option=com_community&view=profile&task=save');?>" method="POST">
<div>
	<span><?php echo JText::_('COM_COMMUNITY_EMAIL');?></span><input type="text" name="email" value="" size="50" style="margin-left: 10px;" />
	<input type="hidden" name="emailpass" value="" />
	<input type="hidden" name="id" value="<?php echo $my->id;?>" />
	<input type="hidden" name="id" value="<?php echo $my->get('id');?>" />
	<input type="hidden" name="gid" value="<?php echo $my->get('gid');?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</div>
</form>