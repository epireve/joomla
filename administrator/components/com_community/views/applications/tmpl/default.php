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
<h3 style="text-decoration: underline;"><?php echo JText::_('COM_COMMUNITY_APPLICATIONS');?></h3>
<p>
Applications in Jom Social are installed via <strong>Joomla!'s Plugin Manager</strong>. This is to allow developers
to easily develop their own set of applications easily and allows end user to install them at 1 single location.
</p>
<div>
<a href="index.php?option=com_plugins&<?php if(JOOMLA_LEGACY_VERSION){echo 'filter_type=community';} else {echo 'view=plugins&filter_folder=community';}?>">Click here to view the applications lists</a>
</div>