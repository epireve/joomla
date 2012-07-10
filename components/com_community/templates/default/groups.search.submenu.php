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
		<form name="jsform-groups-search" method="get" action="<?php echo $url; ?>">
			<input type="text" class="inputbox" name="search" value="" />
			<?php echo JHTML::_( 'form.token' ) ?>
			<input type="hidden" value="com_community" name="option" />
			<input type="hidden" value="groups" name="view" />
			<input type="hidden" value="search" name="task" />
			<input type="hidden" value="<?php echo CRoute::getItemId();?>" name="Itemid" />
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>">
		</form>
		</div>
		<a href="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=search');?>"><?php echo JText::_('COM_COMMUNITY_GROUPS_ADVANCE_SEARCH'); ?></a>
	</li>
</ul>