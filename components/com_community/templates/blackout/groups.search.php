<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	posted	boolean	Determines whether the current state is a posted event.
 * @param	search	string	The text that the user used to search 
 */
defined('_JEXEC') or die();
?>


<div class="cToolbarBand">
	<div class="bandContent">
		<form name="jsform-groups-search" method="get" action="">
			<input type="text" class="inputbox" name="search" value="" size="40" />
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON');?>" class="button" /> 
			<?php echo JHTML::_( 'form.token' ); ?>
			<input type="hidden" value="com_community" name="option" />
			<input type="hidden" value="groups" name="view" />
			<input type="hidden" value="search" name="task" />
		</form>
	</div>
	
	<div class="bandFooter"><div class="bandFooter_inner"></div></div>
</div>

<div id="community-groups-wrap">
	<?php
	if( $posted )
	{
	?>
		<div class="dark-bg">
			<span style="float: left; width: 50%;"><?php echo JText::sprintf( 'COM_COMMUNITY_GROUPS_SEARCH_RESULT' , $search ); ?></span>
			<span style="float: right; text-align: right;">
				<?php echo JText::sprintf( (CStringHelper::isPlural($groupsCount)) ? 'COM_COMMUNITY_GROUPS_SEARCH_RESULT_TOTAL_MANY' : 'COM_COMMUNITY_GROUPS_SEARCH_RESULT_TOTAL' , $groupsCount ); ?>
			</span>
			<div class="clr"></div>
		</div>
		
		<?php echo $groupsHTML; ?>
	<?php
	}
	?>
</div>