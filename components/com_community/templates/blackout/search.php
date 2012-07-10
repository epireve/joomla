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
<div class="cToolbarBand">
	<div class="bandContent">
	<form name="jsform-search" method="get" action="">
		<input type="hidden" name="option" value="com_community" />
		<input type="hidden" name="view" value="search" />
		<input type="hidden" name="Itemid" value="<?php echo CRoute::_getDefaultItemid();?>">
		<input type="text" class="inputbox" size="40" name="q" value="<?php echo $query; ?>" />
		<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON_TEMP');?>" class="button" name="Search" />
	</form>
	</div>
	<div class="bandFooter"><div class="bandFooter_inner"></div></div>
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
	<br />
	<div style="border:1px solid #00CCFF; padding:20px; background-color:#CCFFFF">
		<?php echo JText::_('COM_COMMUNITY_NO_RESULT_FROM_SEARCH');?>
	</div>
<?php
}
?>