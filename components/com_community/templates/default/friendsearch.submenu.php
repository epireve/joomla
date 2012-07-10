<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	posted	boolean	Determines whether the current state is a posted event.
 */
defined('_JEXEC') or die();
?>
<ul class="jsTogSearch">
	<li>
		<div>
		<form name="jsform-search" method="get" action="<?php echo $url; ?>">
			<input type="text" class="inputbox" name="q" value="" />
			<label class="lblradio"><input type="checkbox" name="avatar" style="margin-right: 5px;" value="1" class="radio"><?php echo JText::_('COM_COMMUNITY_EVENTS_AVATAR_ONLY'); ?></label>
			<?php echo JHTML::_( 'form.token' ) ?>
			<input type="hidden" value="com_community" name="option" />
			<input type="hidden" value="friends" name="view" />
			<input type="hidden" value="friendsearch" name="task" />
			<input type="hidden" value="<?php echo JRequest::getVar( 'userid', '', 'REQUEST' ); ?>" name="userid" />
			<input type="hidden" value="<?php echo CRoute::getItemId();?>" name="Itemid" />
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>">
		</form>
		</div>
	</li>
</ul>