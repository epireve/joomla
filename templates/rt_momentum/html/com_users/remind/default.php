<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

require_once(JPATH_LIBRARIES.'/gantry/gantry.php');
$gantry->init();
gantry_import('core.utilities.gantryjformfieldaccessor');

?>
<div class="user remind <?php echo $this->pageclass_sfx?>">
		<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="rt-pagetitle">
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h1>
		<?php endif; ?>
	
	<form id="user-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=remind.remind'); ?>" method="post" class="josForm form-validate">
	
			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
				<dl>
				<?php
					foreach ($this->form->getFieldset($fieldset->name) as $name => $field):
				    $fa = new GantryJFormFieldAccessor($field);
				    if ($fa->getType() == "email") $fa->addClass('inputbox');
				?>
					<dt><?php echo $field->label; ?></dt>
					<dd><?php echo $field->input; ?></dd>
				<?php endforeach; ?>
				</dl>
			</fieldset>
			<?php endforeach; ?>
			<div>
				<div class="readon"><button type="submit" class="button"><?php echo JText::_('JSUBMIT'); ?></button></div>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</form>
	</div>
