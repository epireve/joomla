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
<form name="adminForm" id="adminForm" method="post">
<div style="padding-bottom: 10px;">
	<span style="color: red;font-weight: bold;"><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_NOTE');?></span>: <span><?php echo JText::_('COM_COMMUNITY_TEMPLATE_PARAMETER_INFO');?></span>
</div>
<table class="adminlist">
<thead>
	<tr>
		<th width="5" class="title">
			<?php echo JText::_( 'COM_COMMUNITY_NUM' ); ?>
		</th>
		<th class="title" colspan="2">
			<?php echo JText::_( 'COM_COMMUNITY_TEMPLATES_NAME' ); ?>
		</th>
		<th width="5%" class="title">
			<?php echo JText::_('COM_COMMUNITY_TEMPLATES_DEFAULT');?>
		</th>
		<th width="10%" align="center">
			<?php echo JText::_( 'COM_COMMUNITY_TEMPLATES_VERSION' ); ?>
		</th>
		<th width="15%" class="title">
			<?php echo JText::_( 'COM_COMMUNITY_DATE' ); ?>
		</th>
		<th width="25%"  class="title">
			<?php echo JText::_( 'COM_COMMUNITY_TEMPLATES_AUTHOR' ); ?>
		</th>
	</tr>
</thead>
<?php $i = 0; ?>
<?php foreach( $this->templates as $row ): ?>
<tr>
	<td>
		<?php echo ( $i + 1 ); ?>
	</td>
	<td width="5">
		<input type="radio" id="cb<?php echo $i;?>" name="template" value="<?php echo $row->element; ?>" onclick="isChecked(this.checked);" />
	</td>
	<td>
		<a href="index.php?option=com_community&view=templates&layout=edit&override=<?php echo $row->override ? 1 : 0;?>&id=<?php echo $row->element;?>"><?php echo $row->element;?></a>
	</td>
	<td align="center">
	<?php
	if( $this->config->get('template') == $row->element )
	{
	?>
		<img src="templates/<?php echo DEFAULT_TEMPLATE_ADMIN;?>/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'COM_COMMUNITY_PUBLISHED' ); ?>" />
	<?php
	}
	?>
	</td>
	<td align="center">
		<?php echo ($row->info) ? $row->info['version'] : 'N/A';?>
	</td>
	<td align="center">
		<?php echo ($row->info) ? $row->info[TEMPLATE_CREATION_DATE] : 'N/A';?>
	</td>
	<td align="center">
		<?php echo ($row->info) ? $row->info['author'] : 'N/A';?>
	</td>
</tr>
	<?php $i++;?>
<?php endforeach; ?>
</table>
<input type="hidden" name="view" value="templates" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="task" value="publish" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>