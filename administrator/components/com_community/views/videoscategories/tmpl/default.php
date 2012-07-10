<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript" language="javascript">
/**
 * This function needs to be here because, Joomla calls it
 **/ 
 Joomla.submitbutton = function(action){
 	submitbutton( action );
 }
 
function submitbutton(action)
{
	if(action == 'newcategory')
	{
		azcommunity.editVideosCategory( 0 , '<?php echo JText::_('COM_COMMUNITY_CATEGORY_NEW'); ?>');
	}
	
	if(action == 'removecategory')
	{
		submitform(action);
	}
}
</script>
	<form action="index.php?option=com_community" method="post" name="adminForm">
	<table class="adminlist" cellspacing="1">
		<thead>
			<tr class="title">
				<th width="1%"><?php echo JText::_('COM_COMMUNITY_NUMBER'); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->categories ); ?>);" /></th>
				<th width="15%" style="text-align: left;">
					<?php echo JText::_('COM_COMMUNITY_NAME'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_COMMUNITY_PARENT'); ?>
				</th>
				<th style="text-align: left;">
					<?php echo JText::_('COM_COMMUNITY_CATEGORY_DESCRIPTION'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('ID'); ?>
				</th>
			</tr>
		</thead>
<?php
		$i		= 0;
		
		foreach($this->categories as $category)
		{
?>
			<tr>
				<td align="center"><?php echo $i + 1; ?></td>
				<td><?php echo JHTML::_('grid.id', $i++, $category->id); ?></td>
				<td>
					<?php echo JHTML::_('link', 'javascript:void(0);', $category->name, array('id' => 'videos-title-' . $category->id , 'onclick'=>'azcommunity.editVideosCategory(\'' . $category->id . '\',\'' . JText::_('COM_COMMUNITY_CATEGORY_EDIT') . '\');')); ?>
				</td>
				<td><?php echo $category->pname; ?></td>
				<td id="videos-description-<?php echo $category->id; ?>">
					<?php echo $category->description;?>
				</td>
				<td align="center">
					<?php echo $category->id; ?>
				</td>
			</tr>
<?php
		}
?>
		<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
	</table>
	<input type="hidden" name="view" value="videoscategories" />
	<input type="hidden" name="option" value="com_community" />
	<input type="hidden" name="task" value="videoscategories" />
	<input type="hidden" name="boxchecked" value="0" />
	</form>