<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @param	$app	Application object
 */
defined('_JEXEC') or die();
?>

<table class="cWindowForm" cellspacing="1" cellpadding="0">
	<tr>
		<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_APPS_NAME');?></td>
		<td class="cWindowFormVal"><?php echo $this->escape($app->name); ?></td>
	</tr>
	<?php if($this->params->get('appsShowAuthor')) { ?>
	<tr>
		<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_APPS_AUTHOR');?></td>
		<td class="cWindowFormVal"><?php echo $this->escape($app->author); ?></td>
	</tr>
	<?php } ?>
	<tr>
		<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_APPS_VERSION');?></td>
		<td class="cWindowFormVal"><?php echo $this->escape($app->version); ?></td>
	</tr>
	<tr>
		<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_APPS_DESCRIPTION');?></td>
		<td class="cWindowFormVal"><?php echo $this->escape( JText::_( $app->description ) ); ?></td>
	</tr>
</table>