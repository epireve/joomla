<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	author		string
 * @param	$results	An array of user objects for the search result
 */
defined('_JEXEC') or die();
?>
<div class="people-search-form">
	<form name="jsform-search" method="get" action="">
		<input type="hidden" name="option" value="com_community" />
		<input type="hidden" name="view" value="friends" />
		<input type="hidden" name="task" value="friendsearch" />
		<input type="hidden" name="userid" value="<?php echo JRequest::getVar( 'userid', '', 'REQUEST' ); ?>" />
		<input type="hidden" name="Itemid" value="<?php echo CRoute::_getDefaultItemid();?>">
		<div>
			<input type="text" class="inputbox" size="40" name="q" value="<?php echo $this->escape( $query ); ?>" />
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON_TEMP');?>" class="button" name="Search" />
			
		</div>
		<div class="labelradio">
			<label class="lblradio"><input type="checkbox" name="avatar" id="avatar" style="margin-right: 5px;" value="1" class="radio"<?php echo ($avatarOnly) ? ' checked="checked"' : ''; ?>><?php echo JText::_('COM_COMMUNITY_EVENTS_AVATAR_ONLY'); ?></label>
		</div>
	</form>
</div>
<?php
if( $results )
{
?>
	<h2>
		<?php echo JText::_('COM_COMMUNITY_SEARCH_RESULTS');?>
	</h2>
	<?php echo $resultHTML;?>
<?php		
}
else if( empty( $results ) && !empty( $query ) )
{
?>
	<div class="people-not-found">
		<?php echo JText::_('COM_COMMUNITY_NO_RESULT_FROM_SEARCH');?>
	</div>
<?php
}
?>