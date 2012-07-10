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
<table cellpadding="4" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="100%">
			<img src="<?php echo COMMUNITY_ASSETS_URL . '/logo.png'; ?>" />
		</td>
	</tr>
	<tr>
		<td>		
			<h3>About the Team</h3>
			<p>
			The team behind JomSocial, Slashes &amp; Dots Sdn. Bhd is a world-class establishment in software
			development. Our current focus is developing software to enhance open source technologies like Joomla!,
			and yet agile in emerging technologies as we continuously explore the constantly changing frontier of
			software development.
			</p>
			<p>Please visit <a href="http://www.jomsocial.com">www.jomsocial.com </a>to find out more about us.</p>
		</td>
	</tr>
	<tr>
		<td>
			<div style="font-weight: 700;">
				<?php echo JText::sprintf( 'Version: %1$s', $this->version ); ?>
			</div>
			<div>
				<a href="javascript:void(0);" onclick="azcommunity.checkVersion();">
					<?php echo JText::_('COM_COMMUNITY_ABOUT_CHECK_LATEST_VERSION'); ?>
				</a>
			</div>
		</td>
	</tr>
</table>