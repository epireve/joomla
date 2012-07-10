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
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS' ); ?></legend>
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_EVENTS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_EVENTS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_EVENTS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableevents' , null , $this->config->get('enableevents') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_GUEST_SEARCH' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_GUEST_SEARCH_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ENABLE_GUEST_SEARCH' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'enableguestsearchevents' , null , $this->config->get('enableguestsearchevents') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td width="350" class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_MODERATE_EVENT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_MODERATE_EVENT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_MODERATE_EVENT' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'event_moderation' , null , $this->config->get('event_moderation') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ALLOW_CREATION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_ALLOW_CREATION_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ALLOW_CREATION' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'createevents' , null , $this->config->get('createevents') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_CREATE_LIMIT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_CREATE_LIMIT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_CREATE_LIMIT' ); ?>
					</span>
				</td>
				<td valign="top">
					<input type="text" name="eventcreatelimit" value="<?php echo $this->config->get('eventcreatelimit' );?>" size="10" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_EXPORT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_EXPORT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_EXPORT' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'eventexportical' , null , $this->config->get('eventexportical') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_IMPORT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_IMPORT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_ICAL_IMPORT' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'event_import_ical' , null , $this->config->get('event_import_ical') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>	
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_MAPS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_MAPS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_MAPS' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'eventshowmap' , null , $this->config->get('eventshowmap') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_NEARBY_RADIUS' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_NEARBY_RADIUS_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_NEARBY_RADIUS' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="event_nearby_radius">
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_5; ?>"<?php echo ( $this->config->get('event_nearby_radius') == COMMUNITY_EVENT_WITHIN_5 ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_5MILES_OPTION');?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_10; ?>"<?php echo ( $this->config->get('event_nearby_radius') == COMMUNITY_EVENT_WITHIN_10 ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_10MILES_OPTION');?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_20; ?>"<?php echo ( $this->config->get('event_nearby_radius') == COMMUNITY_EVENT_WITHIN_20 ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_20MILES_OPTION');?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_50; ?>"<?php echo ( $this->config->get('event_nearby_radius') == COMMUNITY_EVENT_WITHIN_50 ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_50MILES_OPTION');?></option>
					</select>
				</td>
			</tr>
			
		</tbody>
	</table>
</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_TIME_FORMAT' ); ?></legend>	
	<table class="admintable" cellspacing="1">
		<tbody>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_LISTINGS_DATE_FORMAT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_LISTINGS_DATE_FORMAT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_LISTINGS_DATE_FORMAT' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="eventdateformat">
							<option value="%b %d"<?php echo ( $this->config->get('eventdateformat') == '%b %d' ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_MONTH_DAY_OPTION');?></option>
							<option value="%d %b"<?php echo ( $this->config->get('eventdateformat') == '%d %b' ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_DAY_MONTH_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_TIME_SELECTION_FORMAT' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_TIME_SELECTION_FORMAT_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_TIME_SELECTION_FORMAT' ); ?>
					</span>
				</td>
				<td valign="top">
					<select name="eventshowampm">
							<option value="1"<?php echo ( $this->config->get('eventshowampm') == '1' ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_12H_OPTION');?></option>
							<option value="0"<?php echo ( $this->config->get('eventshowampm') == '0' ) ? ' selected="true"' : '';?>><?php echo JText::_('COM_COMMUNITY_24H_OPTION');?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_TIMEZONE' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_TIMEZONE_TIPS'); ?>">
						<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_EVENTS_SHOW_TIMEZONE' ); ?>
					</span>
				</td>
				<td valign="top">
					<?php echo JHTML::_('select.booleanlist' , 'eventshowtimezone' , null , $this->config->get('eventshowtimezone') , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
				</td>
			</tr>
			
		</tbody>
	</table>
</fieldset>