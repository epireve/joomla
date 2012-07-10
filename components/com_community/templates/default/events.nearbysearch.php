<?php
/**
 * @package		JomSocial
 * @subpackage 	Template
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 * @params	isMine		boolean is this group belong to me
 * @params	categories	Array	An array of categories object
 * @params	members		Array	An array of members object
 * @params	group		Group	A group object that has the property of a group
 * @params	wallForm	string A html data that will output the walls form.
 * @params	wallContent string A html data that will output the walls data.
 */
defined('_JEXEC') or die();
?>
 <div id="community-event-nearby-form" class="cModule clrfix">
		<h3><?php echo JText::_('COM_COMMUNITY_EVENTS_NEARBY'); ?></h3>
		<div id="showNearByEventsForm">
				<input type="text" id="userInputLocation" name="userInputLocation" value="">
				<div class="small cFormTips">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION_DESCRIPTION');?>
				</div>
				<button class="button" onclick="joms.geolocation.validateNearByEventsForm();"><?php echo JText::_('COM_COMMUNITY_SEARCH'); ?></button>
				<span id="autodetectLocation" style="display: none;">&nbsp;<?php echo JText::_('COM_COMMUNITY_OR') ?>&nbsp;<a href="javascript:void(0);" onclick="joms.geolocation.showNearByEvents();"><?php echo JText::_('COM_COMMUNITY_EVENTS_AUTODETECT') ?></a></span>
		</div>
		<div id="community-event-nearby-listing" style="display: none">
				<span id="showNearByEventsLoading" class="loading" style="display: none; float: left; margin-top: 10px; margin-left: 80px;"></span>
		</div>
</div>