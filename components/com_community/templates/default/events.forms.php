<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @params	categories Array	An array of categories
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

<form method="post" action="<?php echo CRoute::getURI(); ?>" id="createEvent" name="createEvent" class="community-form-validate">

<script type="text/javascript">
	<?php if(!empty($event->description)){ ?>
	joms.jQuery(document).ready(function(){
	    joms.events.showDesc();
	});
	<?php } ?>
	
	function saveContent()
	{
		<?php echo $editor->saveText( 'description' ); ?>
		return true;
	}
</script>
    
<div id="community-events-wrap">
<?php if(!$event->id && $eventcreatelimit != 0 ) { ?>
    <?php if($eventCreated/$eventcreatelimit>=COMMUNITY_SHOW_LIMIT) { ?>
	<div class="hints">
		<?php echo JText::sprintf('COM_COMMUNITY_EVENTS_CREATION_LIMIT_STATUS', $eventCreated, $eventcreatelimit ); ?>
	</div>
    <?php } ?>
<?php } ?>
	<table class="formtable" cellspacing="1" cellpadding="0">
	<!-- events name -->
	<tr>
		<td class="key">
			<label for="title" class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_TITLE_LABEL'); ?>
			</label>
		</td>
		<td class="value">
			<input name="title" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_TITLE_TIPS'); ?>" id="title" type="text" size="45" maxlength="255" class="required inputbox jomNameTips" value="<?php echo $this->escape($event->title); ?>" />
		</td>
	</tr>
        <!--events summary-->
        <tr>
                <td class="key">
                        <label for="summary" class="label title">
                            <?php echo JText::_('COM_COMMUNITY_EVENTS_SUMMARY')?>
                        </label>
                </td>
                <td class="value">
                    <textarea name="summary" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_SUMMARY_TIPS')?>" id="summary" maxlength="140" class="jomNameTips" style="width:293px;height:50px;resize:vertical;"><?php echo $this->escape($event->summary);?></textarea>
                </td>
                
        </tr>
        <tr id="event-description-link">
            <td class="key"></td>
            <td class="value">
                <a title="<?php echo JText::_('COM_COMMUNITY_EVENTS_DESCRIPTION_TIPS');?>" class="jomNameTips" href="javascript:joms.events.showDesc()" > <?php echo JText::_('COM_COMMUNITY_EVENTS_SHOW_EDITOR')?></a>
           </td>
        </tr>
	<!-- events description -->
	<tr id="event-discription" style="display:none">
		<td class="key">
			<label for="description" class="label title">
				<?php echo JText::_('COM_COMMUNITY_EVENTS_DESCRIPTION');?>
			</label>
		</td>
		<td class="value">
			<?php if( $config->get( 'htmleditor' ) == 'none' && $config->getBool('allowhtml') ) { ?>
   				<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED');?></div>
			<?php } ?>
			
			<?php
			if( !CStringHelper::isHTML($event->description) 
				&& $config->get('htmleditor') != 'none' 
				&& $config->getBool('allowhtml') )
			{
				$event->description = CStringHelper::nl2br($event->description);
			}
			?>
				
			<?php echo $editor->displayEditor( 'description',  $event->description , '95%', '350', '10', '20' , false ); ?>
	
		</td>
	</tr>
	<!-- events category -->
	<tr>
		<td class="key">
			<label for="catid" class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY');?>
			</label>
		</td>
		<td class="value">
			<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY_TIPS');?>"><?php echo $lists['categoryid']; ?></span>
		</td>
	</tr>
	<!-- events location -->
	<tr>
		<td class="key">
			<label for="location" class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION'); ?>
			</label>
		</td>
		<td class="value">
			<input title="<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION_TIPS'); ?>" name="location" id="location" type="text" size="45" maxlength="255" class="required inputbox jomNameTips" value="<?php echo $this->escape($event->location); ?>" />
			<div class="small">
				<?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION_DESCRIPTION');?>
			</div>
		</td>
	</tr>
	<!-- events location -->
	<!-- events start datetime -->
	<tr  id="event-start-datetime">
		<td class="key">
			<label class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME'); ?>
			</label>
		</td>
		<td class="value">			
			<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME_TIPS'); ?>">
				<script type="text/javascript">
					<!-- add calendar listener to the field -->
					window.addEvent('domready', function() {Calendar.setup({
					inputField: "startdate",
					ifFormat: "%Y-%m-%d",
					button: "startdate",
					singleClick: true,
					firstDay: 0
					});}); 
				</script>
				<?php echo JHTML::_('calendar',  $startDate->toFormat( '%Y-%m-%d' ) , 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true', 'onchange' => 'updateEndDate();', 'id'=>'startdate') );?>
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
		<td class="key">
			<label class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME'); ?>
			</label>
		</td>
		<td class="value">			
			<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME_TIPS'); ?>">
				<script type="text/javascript">
					window.addEvent('domready', function() {Calendar.setup({
					inputField: "enddate",
					ifFormat: "%Y-%m-%d",
					button: "enddate",
					singleClick: true,
					firstDay: 0
					});}); 
				</script>
				<?php echo JHTML::_('calendar',  $endDate->toFormat( '%Y-%m-%d' ) , 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'required inputbox', 'size'=>'10',  'maxlength'=>'10' , 'readonly' => 'true', 'id'=>'enddate', 'onchange' => 'updateStartDate();') );?>
				<span id="end-time">
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
			</span>
		</td>
	</tr>
	<script type="text/javascript">
		function toggleEventDateTime()
		{
			if( joms.jQuery('#allday').attr('checked') == 'checked' ){
				joms.jQuery('#start-time, tr#event-end-datetime').hide();
			}else{
				joms.jQuery('#start-time, tr#event-end-datetime').show();
			}
		}
	</script>
	<tr>
		<td class="key">&nbsp;</td>
		<td class="value">
			<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_ALL_DAY_TIPS');?>">
				<input id="allday" name="allday" type="checkbox" onclick="toggleEventDateTime();" value="1" <?php if($allday){ echo 'checked'; } ?> />&nbsp;<?php echo JText::_('COM_COMMUNITY_EVENTS_ALL_DAY'); ?>
			</span>
		</td>
	</tr>
	<?php
	if( $config->get('eventshowtimezone') )
	{
	?>
	<tr>
		<td class="key">
			<label class="label title">
				*<?php echo JText::_('COM_COMMUNITY_TIMEZONE'); ?>
			</label>
		</td>
		<td class="value">			
			<span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_SET_TIMEZONE'); ?>">
				<select name="offset">
				<?php
				$defaultTimeZone = isset($event->offset)?$event->offset:$systemOffset;				
				foreach( $timezones as $offset => $value ){
				?>
					<option value="<?php echo $offset;?>"<?php echo $defaultTimeZone == $offset ? ' selected="selected"' : '';?>><?php echo $value;?></option>
				<?php
				}
				?>
				</select>
			</span>
		</td>
	</tr>
	<?php
	}
	?>
	<!-- events tickets -->
	<tr>
		<td class="key">
			<label for="ticket" class="label title">
				*<?php echo JText::_('COM_COMMUNITY_EVENTS_NO_SEAT'); ?>
			</label>
		</td>
		<td class="value">
			<input title="<?php echo JText::_('COM_COMMUNITY_EVENTS_NO_SEAT_DESCRIPTION'); ?>" name="ticket" id="ticket" type="text" size="10" maxlength="5" class="required inputbox jomNameTips" value="<?php echo (empty($event->ticket)) ? '0' : $this->escape($event->ticket); ?>" />
			<div class="small">
				<?php echo JText::_('COM_COMMUNITY_EVENTS_NO_SEAT_DESCRIPTION');?>
			</div>
		</td>
	</tr>	
	<?php
	if( $helper->hasPrivacy() )
	{
	?>
	<!-- events type -->
	<tr>
		<td class="key">
		</td>
		<td class="value">
                    <span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_TYPE_TIPS');?>">
                        <input type="checkbox" name="permission" id="permission-private" value="1"<?php echo ($event->permission == COMMUNITY_PRIVATE_EVENT ) ? ' checked="checked"' : '';?> />
			<label for="permission-private" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_EVENTS_PRIVATE_EVENT');?></label>
                    </span>
		</td>
	</tr>
	<?php
	}
	?>
	<?php
	if( $helper->hasInvitation() )
	{
	?>	
	<!-- events allow guest to invite -->
	<tr>
		<td class="key">
		</td>
		<td class="value">
                    <span class="jomNameTips" title="<?php echo JText::_('COM_COMMUNITY_EVENTS_GUEST_INVITE_TIPS'); ?>">
			<input type="checkbox" name="allowinvite" id="allowinvite0" value="1"<?php echo ($event->allowinvite ) ? ' checked="checked"' : '';?> />
			<label for="allowinvite0" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_EVENTS_GUEST_INVITE'); ?></label>
                    </span>
		</td>
	</tr>
	<?php
	}
	?>
	<tr>
			<td class="key"></td>
			<td class="value"><span class="hints"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></span></td>
		</tr>
	
	<!-- event buttons -->
	<tr>
		<td class="key"></td>
		<td class="value">
			<?php echo JHTML::_( 'form.token' ); ?>
			<?php if(!$event->id): ?>
			<input name="action" type="hidden" value="save" />
			<?php endif;?>
			<input type="hidden" name="eventid" value="<?php echo $event->id;?>" />
			<input type="submit" value="<?php echo ($event->id) ? JText::_('COM_COMMUNITY_SAVE_BUTTON') : JText::_('COM_COMMUNITY_EVENTS_CREATE_BUTTON');?>" class="button validateSubmit" onclick="saveContent();" />
			<input type="button" class="button" onclick="history.go(-1);return false;" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON');?>" />
		</td>
	</tr>
	</table>
</div>
</form>
<script type="text/javascript">
	cvalidate.init();
	cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');
	cvalidate.noticeTitle	= '<?php echo addslashes(JText::_('COM_COMMUNITY_NOTICE') );?>';
	
	/*
		The calendar.js does not display properly under IE when a page has been
		scrolled down. This behaviour is present everywhere within the Joomla site.
		We are injecting our fixes into their code by adding the following
		at the end of the fixPosition() function:
		if (joms.jQuery(el).parents('#community-wrap').length>0)
		{
			var anchor   = joms.jQuery(el);
			var calendar = joms.jQuery(self.element);
			box.x = anchor.offset().left - calendar.outerWidth() + anchor.outerWidth();
			box.y = anchor.offset().top - calendar.outerHeight();
		}
		Unobfuscated version of "JOOMLA/media/system/js/calendar.js" was taken from
		http://www.dynarch.com/static/jscalendar-1.0/calendar.js for reference.		
	*/
	joms.jQuery(document).ready(function()
	{
		Calendar.prototype.showAtElement=function(c,d){var a=this;var e=Calendar.getAbsolutePos(c);if(!d||typeof d!="string"){this.showAt(e.x,e.y+c.offsetHeight);return true}function b(j){if(j.x<0){j.x=0}if(j.y<0){j.y=0}var l=document.createElement("div");var i=l.style;i.position="absolute";i.right=i.bottom=i.width=i.height="0px";document.body.appendChild(l);var h=Calendar.getAbsolutePos(l);document.body.removeChild(l);if(Calendar.is_ie){h.y+=document.body.scrollTop;h.x+=document.body.scrollLeft}else{h.y+=window.scrollY;h.x+=window.scrollX}var g=j.x+j.width-h.x;if(g>0){j.x-=g}g=j.y+j.height-h.y;if(g>0){j.y-=g}if(joms.jQuery(c).parents("#community-wrap").length>0){var f=joms.jQuery(c);var k=joms.jQuery(a.element);j.x=f.offset().left-k.outerWidth()+f.outerWidth();j.y=f.offset().top-k.outerHeight()}}this.element.style.display="block";Calendar.continuation_for_the_fucking_khtml_browser=function(){var f=a.element.offsetWidth;var i=a.element.offsetHeight;a.element.style.display="none";var g=d.substr(0,1);var j="l";if(d.length>1){j=d.substr(1,1)}switch(g){case"T":e.y-=i;break;case"B":e.y+=c.offsetHeight;break;case"C":e.y+=(c.offsetHeight-i)/2;break;case"t":e.y+=c.offsetHeight-i;break;case"b":break}switch(j){case"L":e.x-=f;break;case"R":e.x+=c.offsetWidth;break;case"C":e.x+=(c.offsetWidth-f)/2;break;case"l":e.x+=c.offsetWidth-f;break;case"r":break}e.width=f;e.height=i+40;a.monthsCombo.style.display="none";b(e);a.showAt(e.x,e.y)};if(Calendar.is_khtml){setTimeout("Calendar.continuation_for_the_fucking_khtml_browser()",10)}else{Calendar.continuation_for_the_fucking_khtml_browser()}};	
		toggleEventDateTime();					
	});
</script>