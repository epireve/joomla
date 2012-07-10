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
		<form name="jsform-videos-search" method="get" action="<?php echo $url; ?>">
			<input type="text" class="inputbox" name="search-text" value="" />
			<?php echo JHTML::_( 'form.token' ) ?>
			<input type="hidden" name="option" value="com_community" />
			<input type="hidden" name="view" value="videos" />
			<input type="hidden" name="task" value="search" />
			<input type="hidden" name="Itemid" value="<?php echo CRoute::getItemId();?>" />
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH'); ?>">
		</form>
		</div>
	</li>
</ul>