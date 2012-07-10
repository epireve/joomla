<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 *
 */
defined('_JEXEC') or die();
?>

<div class="community-calendar cModule clrfix">
	<h3><?php echo JText::_('COM_COMMUNITY_EVENTS_CAL');?></h3>
	<script>
		joms.jQuery(document).ready(function(){
			init_calendar();
		});

		//return date in - format
		function getDate(day){
			if(day < 10){
					day = '0'+day;
			}
			var raw = joms.jQuery('input.cal-month-year').val().split(';');	
			return raw[0] + '-' + raw[1]+ '-' + day ;
		}
		
		//initialize all listener on calendar
		function init_calendar(){
			// date listener
			joms.jQuery('div#event>table>tbody>tr>td').click(function(){
				joms.jQuery('div#event>table>tbody>tr>td').each(function(){
					joms.jQuery(this).removeClass('selected');
				});
				if(joms.jQuery(this).html() > 0){ // to indicate this is a date
					joms.jQuery(this).addClass('selected');
					var date = getDate(joms.jQuery(this).html());
					date = date.split('-');
					joms.events.getDayEvent(date[2],date[1],date[0]);
				}
			});
			
			//next or prev month listener
			joms.jQuery('span.calendar-next').click(function(){
				var raw = joms.jQuery('input.cal-month-year').val().split(';');
				var month = parseFloat(raw[1]) + 1;
				var year = parseFloat(raw[0]);
				if(month > 12){ //month > dec, change to 1(january), add 1 to yr
					month = 1;
					year = year + 1;
				}	
				joms.jQuery('.events-list').html('');
				joms.events.getCalendar(month,year);
			});
			
			joms.jQuery('span.calendar-prev').click(function(){
				var raw = joms.jQuery('input.cal-month-year').val().split(';');
				var month = parseFloat(raw[1]) - 1;
				var year = parseFloat(raw[0]);
				if(month == 0){ //month > dec, change to 1(january), add 1 to yr
					month = 12;
					year = year - 1;
				}	
				joms.jQuery('.events-list').html('');
				joms.events.getCalendar(month,year);
			});
		}
		
	</script>

	<div id="event">
		<?php 
				$time = time();
				echo CCalendar::generate_calendar(date('Y', $time), date('n', $time));
		?>
	</div>
	<div class="community-calendar-result">
		<strong class="happening_title" style="display:none"><?php echo JText::_('COM_COMMUNITY_EVENTS_HAPPENING_TITLE'); ?>:</strong>
		<img class="loading-icon" style="display:none" src="<?php echo JURI::root(); ?>components/com_community/assets/ajax-loader.gif"/>
		<div class="events-list small"></div>
	</div>
</div>