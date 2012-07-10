<?php
/**
 * @version		$Id: modules.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_rounded($module, &$params, &$attribs)
{
	if($module->content)
	{
		?>
		<div id="<?php echo $attribs['id'] ?>" class="panel">
			<div class="jpane-slider">
			
				<?php echo $module->content; ?>
				<div class="clr"></div>
			</div>

		</div>
		<?php
	}
}
function modChrome_cpanel($module, &$params, &$attribs)
{ 
	if($module->content)
	{
	?>
		<div class="module <?php echo print_r($module->module);?>" id="module-<?php echo $module->id; ?>">
			<div class="module-2">
				<div class="module-3">
					<div class="module-4">
						<h3 class="module-title">
						<ul class="module-edit">
						<li class="module-edit-parent">
						<a href="index.php?option=com_modules&tmpl=component&client=1&task=edit&cid[]=<?php echo $module->id; ?>" class="modal ap-icon ap-edit" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><span><?php echo JText::_('Edit'); ?></span></a>
						<?php
						// Commenting out until pro version
						/*
						<ul class="module-edit-child">
							<li class="edit-icon"><a href="index.php?option=com_modules&tmpl=component&client=1&task=edit&cid[]=<?php echo $module->id; ?>" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><span></span><?php echo JText::_('Edit'); ?></a></li>
							<li class="edit-separator"><?php echo JText::_('Permissions'); ?></li>
							<li><a href="#"><span></span><?php echo JText::_('Manager'); ?></a></li>
							<li><a href="#"><span class="active"></span><?php echo JText::_('Admin'); ?></a></li>
							<li><a href="#"><span></span><?php echo JText::_('Super Admin'); ?></a></li>
						</ul>
						*/
						?>
						</li>
						<li class="module-disable">
							<a onclick="javascript:adminPraiseUnpublishModule(<?php echo $module->id ?>)" class="ap-icon ap-disable"><span><?php echo JText::_('Disable'); ?></span></a>
						</li>
						</ul>
						<?php echo $module->title; ?></h3>
						<div class="module-content flexcroll"><?php echo $module->content; ?></div>
						<div class="module-footer"></div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}
function modChrome_cpanelhalf($module, &$params, &$attribs)
{ 
	if($module->content)
	{
	?>
		<div class="module half <?php echo print_r($module->module);?>" id="module-<?php echo $module->id; ?>">
			<div class="module-2">
				<div class="module-3">
					<div class="module-4">
						<h3 class="module-title">
						<ul class="module-edit">
						<li class="module-edit-parent">
						<a href="index.php?option=com_modules&tmpl=component&client=1&task=edit&cid[]=<?php echo $module->id; ?>" class="modal ap-icon ap-edit" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><span><?php echo JText::_('Edit'); ?></span></a>
						<?php
						// Commenting out until pro version
						/*
						<ul class="module-edit-child">
							<li class="edit-icon"><a href="index.php?option=com_modules&tmpl=component&client=1&task=edit&cid[]=<?php echo $module->id; ?>" class="modal" rel="{handler: 'iframe', size: {x: 900, y: 550}}"><span></span><?php echo JText::_('Edit'); ?></a></li>
							<li class="edit-separator"><?php echo JText::_('Permissions'); ?></li>
							<li><a href="#"><span></span><?php echo JText::_('Manager'); ?></a></li>
							<li><a href="#"><span class="active"></span><?php echo JText::_('Admin'); ?></a></li>
							<li><a href="#"><span></span><?php echo JText::_('Super Admin'); ?></a></li>
						</ul>
						*/
						?>
						</li>
						<li class="module-disable">
							<a onclick="javascript:adminPraiseUnpublishModule(<?php echo $module->id ?>)" class="ap-icon ap-disable"><span><?php echo JText::_('Disable'); ?></span></a>
						</li>
						</ul>
						<?php echo $module->title; ?></h3>
						<div class="module-content flexcroll"><?php echo $module->content; ?></div>
						<div class="module-footer"></div>
					</div>
				</div>
			</div>
		</div>
	<?php
	}
}
	function modChrome_statustools($module, &$params, &$attribs)
	{ 
		if($module->content)
		{
		?>
			<li class="status-<?php echo print_r($module->module);?>" id="module-<?php echo $module->id; ?>">
				<?php echo $module->content; ?>
			</li>
		<?php
		}
	}

?>