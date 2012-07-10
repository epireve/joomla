<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	posted	boolean	Determines whether the current state is a posted event.
 * @param	search	string	The text that the user used to search 
 */
defined('_JEXEC') or die();
?>
<style type="text/css">
div#community-wrap .calendar{
	vertical-align: middle; 
	padding-left: 4px;
	padding-right:4px; 
	border: medium none;
}
</style>
<div id="community-events-wrap">

	<!--SEARCH FORM-->
	<div class="event-search-form">
		<form name="jsform-events-search" method="get" action="">
			<input type="hidden" value="com_community" name="option" />
			<input type="hidden" value="events" name="view" />
			<input type="hidden" value="search" name="task" />
			<input type="hidden" value="<?php echo CRoute::getItemId();?>" name="Itemid" />
			<?php echo JHTML::_( 'form.token' ); ?>
			
			<!-- new forms framework - no tables, less trouble -->
			<ol class="cResetList cForms">
				
				<?php if(!empty($beforeFormDisplay)){ ?>
				<li>
					<div class="cForms-Field">
						<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
							<?php echo $beforeFormDisplay; ?>
						</table>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				<?php } ?>
				
				
				<li>
					<label for="search"><?php echo JText::_('COM_COMMUNITY_SEARCH_FOR'); ?></label>
					<div class="cForms-Field">
						<input type="text" class="inputbox" name="search" value="<?php echo $this->escape($search); ?>" size="50" />
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				
				<li>
					<label for="catid" class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY_TIPS');?>">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY');?>
					</label>
					<div class="cForms-Field">
						<select name="catid" id="catid" class="required inputbox">
							<option value="0" selected></option>
							<?php
								foreach( $categories as $category )
								{
									?>
									<option value="<?php echo $category->id; ?>" <?php if( $category->id == $catId ) { ?>selected<?php } ?>><?php echo JText::_( $this->escape($category->name) ); ?></option>
									<?php
								}
							?>
						</select>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				<li>
					<label class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME_TIPS'); ?>">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_START_DATE'); ?>
					</label>
					<div class="cForms-Field">
						<span>
							<?php echo JHTML::_('calendar',  $advance['startdate'] , 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true') );?>
						</span>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				<li>
					<label class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME_TIPS'); ?>">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_END_DATE'); ?>
					</label>
					<div class="cForms-Field">
						<span>
							<?php echo JHTML::_('calendar',  $advance['enddate'], 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true') );?>
						</span>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				<li>
					<label class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_SEARCH_FROM_TIPS'); ?>">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_FROM'); ?>
					</label>
					<div class="cForms-Field">
						
						<script type="text/javascript">
							joms.jQuery('document').ready(function()
	 						{
							    validateFormValue();

							    // Check if the browsers support W3C Geolocation API
							    // If yes, show the auto-detect link
							    if( navigator.geolocation )
							    {
								    joms.jQuery('#proto__detectButton').show();
							    }
							});

							function get_current_location()
							{
							    joms.jQuery('#proto__currentLocationValue').hide();
							    joms.jQuery('#proto__detectButton').hide();
							    joms.jQuery('#proto__detectingCurrentLocation').show();

							    navigator.geolocation.getCurrentPosition(function(location)
							    {
								var lat	=   location.coords.latitude;
								var lng	=   location.coords.longitude;

								// Reverse Geocoding
								geocoder    =   new google.maps.Geocoder();
								var latlng  =   new google.maps.LatLng( lat, lng );

								geocoder.geocode({'latLng': latlng}, function(results, status){
								    if( status == google.maps.GeocoderStatus.OK ){
									if ( results[4] ){
									    var newLocation = results[4].formatted_address;

									    if( newLocation.length != 0 )
									    {
								    joms.jQuery("#proto_selectRadius").removeAttr("disabled");
								    joms.jQuery("#distance_unit1").removeAttr("disabled");
								    joms.jQuery("#distance_unit2").removeAttr("disabled");
									    }

									    joms.jQuery("#proto__detectingCurrentLocation").hide();
									    joms.jQuery("#proto__currentLocationValue").attr("value", newLocation).show();
									}
								    } else {
									alert("Geocoder failed due to: " + status);
								    }
								});

								joms.jQuery("#proto__detectButton").show();
							    });
							}

							function validateFormValue()
							{
							    var input = joms.jQuery("#proto__currentLocationValue").val();

							    if( input.length != 0 )
							    {
								joms.jQuery("#proto_selectRadius").removeAttr("disabled");
								joms.jQuery("#distance_unit1").removeAttr("disabled");
								joms.jQuery("#distance_unit2").removeAttr("disabled");
							    }
							    else
							    {
								joms.jQuery("#proto_selectRadius").attr("disabled", "disabled");
								joms.jQuery("#distance_unit1").attr("disabled", "disabled");
								joms.jQuery("#distance_unit2").attr("disabled", "disabled");
							    }
							}
						</script>

						<div id="proto__currentLocation">
							<input type="text" name="location" id="proto__currentLocationValue" value="<?php echo $this->escape($advance['fromlocation']); ?>" class="inputbox" onkeyup="validateFormValue();" />
							<span id="proto__detectingCurrentLocation" class="loading" style="float: left;"></span>
							<a id="proto__detectButton" href="javascript: void(0)" style="display: none;" onclick="get_current_location();" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_AUTODETECT_LOCATION'); ?>"><?php echo JText::_('COM_COMMUNITY_EVENTS_AUTODETECT_LOCATION'); ?></a>
						</div>
						
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				<li>
					<label class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_WITHIN_TIPS'); ?>">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_WITHIN'); ?>
					</label>
					<div class="cForms-Field">
						<select id="proto_selectRadius" name="radius" class="required inputbox" disabled="disabled">
							<option value="<?php echo null; ?>" <?php if( empty($advance['radius']) ){ ?>selected<?php } ?>></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_5; ?>" <?php if( $advance['radius'] == COMMUNITY_EVENT_WITHIN_5 ){ ?>selected<?php } ?>><?php echo COMMUNITY_EVENT_WITHIN_5; ?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_10; ?>" <?php if( $advance['radius'] == COMMUNITY_EVENT_WITHIN_10 ){ ?>selected<?php } ?>><?php echo COMMUNITY_EVENT_WITHIN_10; ?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_20; ?>" <?php if( $advance['radius'] == COMMUNITY_EVENT_WITHIN_20 ){ ?>selected<?php } ?>><?php echo COMMUNITY_EVENT_WITHIN_20; ?></option>
							<option value="<?php echo COMMUNITY_EVENT_WITHIN_50; ?>" <?php if( $advance['radius'] == COMMUNITY_EVENT_WITHIN_50 ){ ?>selected<?php } ?>><?php echo COMMUNITY_EVENT_WITHIN_50; ?></option>
						</select>
						<input id="distance_unit1" type="radio" name="unit" class="required inputbox" value="<?php echo COMMUNITY_EVENT_UNIT_KM; ?>" disabled="disabled" <?php if( $unit === COMMUNITY_EVENT_UNIT_KM ){ ?>checked<?php } ?>> <?php echo JText::_('COM_COMMUNITY_EVENTS_KILOMETRE'); ?>
						<input id="distance_unit2" type="radio" name="unit" class="required inputbox" value="<?php echo COMMUNITY_EVENT_UNIT_MILES; ?>" disabled="disabled" <?php if( $unit === COMMUNITY_EVENT_UNIT_MILES || empty($unit) ){ ?>checked <?php } ?>> <?php echo JText::_('COM_COMMUNITY_EVENTS_MILES'); ?>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
				
				<?php if(!empty($afterFormDisplay)){ ?>
					<li>
						<div class="cForms-Field">
							<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
								<?php echo $afterFormDisplay; ?>
							</table>
							<div class="clr"></div>
						</div>
						<div class="clr"></div>
					</li>
				<?php } ?>
				
				<li>
					<div class="cForms-Field">
						<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SEARCH_BUTTON');?> <?php echo JText::_('COM_COMMUNITY_EVENTS');?>" class="button" />
						<?php
							if($searchLinks) {
								echo "<div>";
								echo JText::_('COM_COMMUNITY_OR').' '.JText::_('COM_COMMUNITY_SEARCH_FOR').': ';
								foreach ($searchLinks as $key => $value) {
									?>
									<a href="<?php echo $value; ?>"><?php echo ucwords($key); ?></a>
									<?php
								}
								echo "</div>";
							}
						?>
						<div class="clr"></div>
					</div>
					<div class="clr"></div>
				</li>
			</ol>
			<!-- end: new forms framework - no tables, less trouble -->
                        <input type="hidden" name="posted" value="1">
		</form>
	</div>
	<!--SEARCH FORM-->
	<?php if($posted){?>
	<!--SEARCH DETAIL-->
	<div class="event-search-detail">
		
		<span class="search-detail-left">
			<?php echo JText::sprintf( 'COM_COMMUNITY_SEARCH_RESULT' , $search ); ?>
		</span>
		
		<span class="search-detail-right">
			<?php echo JText::sprintf( (CStringHelper::isPlural($eventsCount)) ? 'COM_COMMUNITY_EVENTS_SEARCH_RESULT_TOTAL_MANY' : 'COM_COMMUNITY_EVENTS_SEARCH_RESULT_TOTAL' , $eventsCount ); ?></span>
		<div style="clear:both;"></div>
		
	</div>
	<!--SEARCH DETAIL-->
	
	<?php echo $eventsHTML; ?>
        <?php }?>
</div>