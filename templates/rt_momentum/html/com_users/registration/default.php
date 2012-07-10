<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.6
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

require_once(JPATH_LIBRARIES.'/gantry/gantry.php');
$gantry->init();
gantry_import('core.utilities.gantryjformfieldaccessor');

?>
<div class="user registration<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="rt-pagetitle"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>
	
		<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" class="form-validate">
	<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
		<?php $fields = $this->form->getFieldset($fieldset->name);?>
		<?php if (count($fields)):?>
			<fieldset>
			<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
				<legend><?php echo JText::_($fieldset->label);?></legend>
			<?php endif;?>
				<dl>
				<?php
					foreach($fields as $field):
				    $fa = new GantryJFormFieldAccessor($field);
				    if ($fa->getType() == "text" || $fa->getType() == "password" || $fa->getType() == "email") $fa->addClass('inputbox');
				?>
				<?php if ($field->hidden):// If the field is hidden, just display the input.?>
					<?php echo $field->input;?>
				<?php else:?>
					<dt>
					<?php echo $field->label; ?>
					<?php if (!$field->required && (!$field->type == "spacer")): ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
					<?php endif; ?>
					</dt>
					<dd><?php echo $field->input;?></dd>
				<?php endif;?>
			<?php endforeach;?>
				</dl>
			</fieldset>
		<?php endif;?>
	<?php endforeach;?>
			<div class="register-buttons">
				<div class="readon"><button type="submit" class="validate button"><?php echo JText::_('JREGISTER');?></button></div>
				<a href="<?php echo JRoute::_('');?>" class="readon" title="<?php echo JText::_('JCANCEL');?>"><span><?php echo JText::_('JCANCEL');?></span></a>
				<input type="hidden" name="option" value="com_users" />
				<input type="hidden" name="task" value="registration.register" />
				<?php echo JHtml::_('form.token');?>
				<div class="clear"></div>
			</div>
		</form>
	</div>
