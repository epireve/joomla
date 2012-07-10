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
<script type="text/javascript">
Joomla.submitbutton = function(action){
	submitbutton( action );
}
</script>
<?php $is16 = JVERSION >= '1.6' ? 1 : 0; ?>

<?php if($is16) { ?>
<div id="submenu-box">
	<div class="t"><div class="t"><div class="t"></div></div></div>
	<div class="m">
<?php } ?>
		<ul id="submenu" class="jsconfiguration">
			<li><a href="#" onclick="return false;" id="main" class="active"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_SITE_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="media"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_MEDIA_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="antispam"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ANTISPAM_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="groups"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_GROUPS_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="events"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="layout"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_LAYOUT_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="privacy"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_PRIVACY_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="network"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_NETWORK_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="facebook-connect"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_FACEBOOK_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="remote-storage"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_REMOTE_TOOLBAR' ); ?></a></li>
			<li><a href="#" onclick="return false;" id="integrations"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_INTEGRATIONS_TOOLBAR' ); ?></a></li>
		</ul>
		<div class="clr"></div>
<?php if($is16) { ?>
	</div>
	<div class="b"><div class="b"><div class="b"></div></div></div>
</div>
<?php } ?>
<div class="clr"></div>
