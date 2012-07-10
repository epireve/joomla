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
<form name="jsforms-events-import" action="<?php echo CRoute::getURI();?>" method="post" enctype="multipart/form-data">
<div class="ctitle">
	<h2><?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT_ICAL_DESCRIPTION');?></h2>
</div>
<div class="jsiCalOption">
	<ul class="cResetList">
		<li class="jsiCalSel">
			<input type="radio" id="upload" name="type" checked="checked" onclick="joms.events.switchImport('file');" class="jsLft jsReset" />
			<label for="upload"><?php echo JText::_('COM_COMMUNITY_EVENTS_INPORT_LOCAL');?></label>
		</li>
		<li class="jsiCalSel">
			<input type="radio" id="link" name="type" onclick="joms.events.switchImport('url');" class="jsLft jsReset" />
			<label for="link"><?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT_EXTERNAL');?></label>
		</li>
		<li id="event-import-file">
			<input type="file" name="file" style="width: 200px;" />
		</li>
		<li id="event-import-url" style="display: none;">
			<input type="text" name="url" style="width: 200px;" />
		</li>
		<li>
			<input type="submit" value="<?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT');?>" class="button" />
			<input type="hidden" value="file" name="type" id="import-type" />
			<span><?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT_ERROR');?></span>
		</li>
	</ul>
</div>
</form>
<?php if( $events ) { ?>
<form action="<?php echo CRoute::_('index.php?option=com_community&view=events&task=saveImport');?>" method="post">
	<div class="ctitle" style="padding-top:30px !important">
		<?php echo JText::_('COM_COMMUNITY_EVENTS_EXPORTED');?>
	</div>
	<p><?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT_SELECT');?></p>
	<ul class="jsiCal cResetList">
		<?php
		$i	= 1;
		foreach($events as $event){
		?>
		<li>
			<div class="jsiCalHead jsRel">
				<span class="jsAbs">
				    <input type="checkbox" name="events[]" id="event-<?php echo $i;?>" value="<?php echo $i;?>" class="jsReset" checked />
				</span>
				<label for="event-<?php echo $i;?>"><?php echo $event->getTitle();?></label>
			</div>
                        <div class="jsiCalDesc">
			<?php if ( $event->getDescription() ) {?>
				<p><?php echo $event->getDescription();?></p>
			<?php } else { ?>
				<p><?php echo JText::_('COM_COMMUNITY_EVENTS_DESCRIPTION_ERR0R');?></p>
			<?php } ?>
			</div>
			<div class="jsiCalDetail">
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_START_TIME');?></span>
					<div class="jsiCalData small">: <?php echo $event->getStartDate();?></div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_END_TIME');?></span>
					<div class="jsiCalData small">: <?php echo $event->getEndDate();?></div></div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_TIMEZONE');?></span>
					<div class="jsiCalData small">: 
						<select name="event-<?php echo $i; ?>-offset">
						<?php
						foreach( $timezones as $offset => $value ){
						?>
							<option value="<?php echo $offset;?>" <?php echo ($offset == 0) ? 'selected' : ''?>><?php echo $value;?></option>
						<?php
						}
						?>
						</select>
					</div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_LOCATION');?></span>
					<div class="jsiCalData small">: <?php echo ( $event->getLocation() != '' ) ? $event->getLocation() : JText::_('COM_COMMUNITY_EVENTS_LOCATION_NOT_AVAILABLE');?></div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_CATEGORY');?></span>
					<div class="jsiCalData small">
						<select name="event-<?php echo $i;?>-catid" id="event-<?php echo $i;?>-catid" class="required inputbox">
						<?php
						foreach( $categories as $category )
						{
						?>
							<option value="<?php echo $category->id; ?>"><?php echo JText::_( $this->escape($category->name) ); ?></option>
						<?php
						}
						?>
						</select>
					</div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_GUEST_INVITE');?></span>
					<div class="jsiCalData small">
						<input type="radio" name="event-<?php echo $i;?>-invite" id="event-<?php echo $i;?>-invite-allowed" value="1" checked="checked" />
						<label for="event-<?php echo $i;?>-invite-allowed" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_YES');?></label>
						<input type="radio" name="event-<?php echo $i;?>-invite" id="event-<?php echo $i;?>-invite-disallowed" value="0" />
						<label for="event-<?php echo $i;?>-invite-disallowed" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_NO');?></label>
					</div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_TYPE'); ?></span>
					<div class="jsiCalData small">
						<input type="radio" name="event-<?php echo $i;?>-permission" id="event-<?php echo $i;?>-permission-open" value="0" checked="checked" />
						<label for="event-<?php echo $i;?>-permission-open" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_EVENTS_OPEN_EVENT');?></label>
						<input type="radio" name="event-<?php echo $i;?>-permission" id="event-<?php echo $i;?>-permission-private" value="1" />
						<label for="event-<?php echo $i;?>-permission-private" class="label lblradio"><?php echo JText::_('COM_COMMUNITY_EVENTS_PRIVATE_EVENT');?></label>
					</div>
				</div>
				<div class="clrfix">
					<span class="jsiCalLabel jsLft small"><?php echo JText::_('COM_COMMUNITY_EVENTS_NO_SEAT'); ?></span>
					<div class="jsiCalData small">
						<input type="text" name="event-<?php echo $i;?>-ticket" id="event-<?php echo $i;?>-ticket" value="0" size="10" maxlength="5"/>
					</div>
				</div>
				<input name="event-<?php echo $i;?>-startdate" value="<?php echo $event->getStartDate();?>" type="hidden" />
				<input name="event-<?php echo $i;?>-enddate" value="<?php echo $event->getEndDate();?>" type="hidden" />
				<input name="event-<?php echo $i;?>-title" value="<?php echo $event->getTitle();?>" type="hidden" />
				<input name="event-<?php echo $i;?>-location" value="<?php echo $this->escape($event->getLocation());?>" type="hidden" />
				<input name="event-<?php echo $i;?>-description" value="<?php echo $this->escape($event->getDescription());?>" type="hidden" />
                <input name="event-<?php echo $i;?>-summary" value="<?php echo $event->getSummary();?>" type="hidden" />
			</div>
		</li>
		<?php
			$i++;
		}
		?>
	</ul>
	<div style="text-align: center;margin-top: 10px;"><input type="submit" value="<?php echo JText::_('COM_COMMUNITY_EVENTS_IMPORT');?>" class="button" /></div>
</form>
<?php } ?>