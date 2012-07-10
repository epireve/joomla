<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form name="jsform-memberlist-addlist" id="jsform-memberlist-addlist" action="<?php echo CRoute::_('index.php?option=com_community&view=memberlist&task=save');?>" method="post">
<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
	<tr>
		<td class="key"><label>*<?php echo JText::_('COM_COMMUNITY_FILTER_TITLE');?></label></td>
		<td class="value">
			<input type="text" name="title" id="title" />
			<div id="filter-title-error" style="display: none;color:red;"><?php echo JText::_('COM_COMMUNITY_MEMBERLIST_TITLE_REQUIRED');?></div>
		</td>
	</tr>
	<tr>
		<td class="key"><label>*<?php echo JText::_('COM_COMMUNITY_FILTER_DESCRIPTION');?></label></td>
		<td class="value">
			<textarea name="description" id="description"></textarea>
			<div id="filter-description-error" style="display: none;color:red;"><?php echo JText::_('COM_COMMUNITY_MEMBERLIST_DESCRIPTION_REQUIRED');?></div>
		</td>
	</tr>
	<tr>
		<td class="key"><label><?php echo JText::_('COM_COMMUNITY_SELECT_MENU');?></label></td>
		<td>
			<?php echo JHTML::_('select.genericlist',   $menuTypes , 'menutype', 'class="inputbox" size="1"', 'menutype', 'title', 1 );?>
		</td>
	</tr>
	<tr>
		<td class="key"><label><?php echo JText::_('COM_COMMUNITY_ACCESS_LEVEL');?></label></td>
		<td>
			<?php echo JHTML::_('list.accesslevel',  $menuAccess ); ?>
		</td>
	</tr>
</table>
<input type="hidden" name="totalfilters" value="<?php echo count( $filters );?>" />
<input type="hidden" name="condition" value="<?php echo $condition;?>" />
<input type="hidden" name="avataronly" value="<?php echo $avatarOnly;?>" />
<?php
for( $i = 0; $i < count( $filters );$i++){
?>
<input type="hidden" name="filter<?php echo $i;?>" value="<?php echo $filters[$i];?>" />
<?php } ?>
</form>