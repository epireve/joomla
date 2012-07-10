 <?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>
<form id="report-form" name="report-form" action="" method="post">
	<table class="cWindowForm" cellspacing="1" cellpadding="0">
		<tr>
			<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_PREDEFINED_REPORTS');?></td>
			<td class="cWindowFormVal">
				<select id="report-predefined" onchange="if(this.value!=0) joms.jQuery('#report-message').val( this.value ); else joms.jQuery('#report-message').val('');">
					<option selected="selected" value="0"><?php echo JText::_('COM_COMMUNITY_SELECT_PREDEFINED_REPORTS'); ?></option>
					<?php
					if( $reports )
					{
						foreach( $reports as $report )
						{
					?>
						<option value="<?php echo JText::_( $report );?>"><?php echo JText::_( $report ); ?></option>
					<?php
						}
					}
					?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="cWindowFormKey"><?php echo JText::_('COM_COMMUNITY_REPORT_MESSAGE');?><span id="report-message-error"></span></td>
			<td class="cWindowFormVal"><textarea id="report-message" name="report-message" rows="3"></textarea></td>
		</tr>
		<tr class="hidden">
			<td class="cWindowFormKey"></td>
			<td class="cWindowFormVal"><input type="hidden" name="reportFunc" value="<?php echo $reportFunc; ?>" /></td>
		</tr>
	</div>
</form>