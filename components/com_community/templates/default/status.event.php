<script type="text/javascript">
//<![CDATA[

(function($) {

var Creator;

joms.status.Creator['event'] = 
{
	initialize: function()
	{
		Creator = this;

		Creator.Form = Creator.View.find('.creator-form');

		Creator.Hint = Creator.View.find('.creator-hint');
	},

	focus: function()
	{
		this.Message.defaultValue("<?php echo JText::_('COM_COMMUNITY_STATUS_EVENT_HINT'); ?>", 'hint');

		Creator.Privacy.parent().hide();
	},

	blur: function()
	{
		Creator.Privacy.parent().show();
	},

	getAttachment: function()
	{
		var attachment = Creator.Form.serializeJSON();

		attachment.type = 'event';

		return attachment;
	},

	submit: function()
	{
		return true; // Let server-side do all validation work
	},

	reset: function()
	{
		Creator.Form[0].reset();
	},

	error: function(message)
	{
		if ($.trim(message).length>0)
		{
			Creator.Hint
				.html(message)
				.show();	
		}
	}
}

})(joms.jQuery);

//]]>
</script>

<div class="creator-view type-event">
	<div class="creator-hint"></div>

	<form class="creator-form">
		<table class="formtable" cellspacing="1" cellpadding="0">

			<tr>
				<td>
					<label for="title" class="label"><?php echo JText::_('COM_COMMUNITY_EVENTS_TITLE_LABEL'); ?></label>
				</td>
				<td class="value">
					<input name="title" id="title" type="text" size="35" maxlength="255" class="required inputbox jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_TITLE_TIPS'); ?>" value="" />
				</td>
			</tr>

			<!-- events category -->
			<tr>
				<td>
					<label for="catid" class="label">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY');?>
					</label>
				</td>
				<td class="value">
					<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY_TIPS');?>"><?php echo $lists['categoryid']; ?></span>
				</td>
			</tr>

			<!-- events location -->
			<tr>
				<td>
					<label for="location" class="label"><?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION'); ?></label>
				</td>
				<td class="value">
					<input name="location" id="location" type="text" size="35" maxlength="255" class="required inputbox jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION_TIPS'); ?>" value="" />
					<div class="small">
						<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION_DESCRIPTION');?>
					</div>
				</td>
			</tr>	

			<!-- events start datetime -->
			<tr>
				<td>
					<label class="label"><?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME'); ?></label>
				</td>
				<td class="value">			
					<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME_TIPS'); ?>">
						<?php echo JHTML::_('calendar',  $startDate->toFormat( '%Y-%m-%d' ) , 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true', 'onchange' => 'updateEndDate();') );?>
						<span id="start-time">
						<?php echo $startHourSelect; ?>:<?php  echo $startMinSelect; ?> <?php echo $startAmPmSelect;?>
						</span>
						<script type="text/javascript">
							function updateEndDate(){
								var startdate	=   joms.jQuery('#startdate').val();
								var enddate	=   joms.jQuery('#enddate').val();

								tmpenddate  =	new Date(enddate);
								tmpstartdate	=   new Date(startdate);

								if(tmpenddate < tmpstartdate){
								    joms.jQuery('#enddate').val( startdate );
								}
							}
						</script>
					</span>
				</td>
			</tr>

			<!-- events end datetime -->
			<tr id="event-end-datetime">
				<td>
					<label class="label"><?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME'); ?></label>
				</td>
				<td class="value">			
					<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME_TIPS'); ?>">
						<?php echo JHTML::_('calendar',  $endDate->toFormat( '%Y-%m-%d' ) , 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true', 'onchange' => 'updateStartDate();') );?>
						<?php echo $endHourSelect; ?>:<?php echo $endMinSelect; ?> <?php echo $endAmPmSelect;?>
						<script type="text/javascript">
							function updateStartDate(){
								var enddate	=   joms.jQuery('#enddate').val();
								var startdate	=   joms.jQuery('#startdate').val();

								tmpenddate  =	new Date(enddate);
								tmpstartdate	=   new Date(startdate);

								if(tmpenddate < tmpstartdate){
								    joms.jQuery('#startdate').val( enddate );
								}
							}
						</script>
					</span>
				</td>
			</tr>
			
			<tr>
				<td>
					&nbsp;
				</td>
				<td class="value">
					<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_ALL_DAY_TIPS');?>">
						<input id="allday" name="allday" type="checkbox" onclick="toggleEventDateTime();" value="1"/>&nbsp;<?php echo JText::_('COM_COMMUNITY_EVENTS_ALL_DAY'); ?>
					</span>
					<script type="text/javascript">
						function toggleEventDateTime()
						{
							if( joms.jQuery('#allday').attr('checked') == 'checked' ){
								joms.jQuery('span#start-time, tr#event-end-datetime').hide();
								joms.jQuery('#starttime-hour').val('12');
								joms.jQuery('#starttime-min').val('00');
								joms.jQuery('#starttime-ampm').val('am');
								joms.jQuery('#endtime-hour').val('11');
								joms.jQuery('#endtime-min').val('59');
								joms.jQuery('#endtime-ampm').val('pm');
								joms.jQuery('#enddate').val(joms.jQuery('#startdate').val());
								
							}else{
								joms.jQuery('span#start-time, tr#event-end-datetime').show();
							}
						}
					</script>
				</td>
			</tr>
		</table>
	</form>
</div>