<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();

if( $fields )
{
	$required	= false;
?>
	<form action="<?php echo CRoute::getURI(); ?>" method="post" id="jomsForm" name="jomsForm" class="community-form-validate">
<?php
	foreach( $fields as $name => $fieldGroup )
	{
		$fieldName	= $name == 'ungrouped' ? '' : $name;
?>
		<div class="ctitle">
			<h2><?php echo JText::_( $fieldName ); ?></h2>
		</div>		
		<table class="formtable" cellspacing="1" cellpadding="0" style="width: 98%;">
		<tbody>
<?php
		foreach($fieldGroup as $field )
		{
			$field = JArrayHelper::toObject ( $field );
			if( !$required && $field->required == 1 )
			{
				$required	= true;
			}
			
			$html = CProfileLibrary::getFieldHTML($field);
?>
				<tr>
					<td class="key" valign="top"><label id="lblfield<?php echo $field->id;?>" for="field<?php echo $field->id;?>" class="label"><?php if($field->required == 1) echo '*'; ?><?php echo JText::_($field->name); ?></label></td>
					<td class="value"><?php echo $html; ?></td>					
				</tr>	
<?php
		}
?>
		</tbody>
		</table>
<?php
	}
?>    
	<table class="ccontentTable" cellspacing="3" cellpadding="0">
	<tbody>	
<?php
	if( $required )
	{
?>	
		<tr>
			<td class="listkey" >&nbsp;</td>
			<td class="listvalue"><?php echo JText::_( 'COM_COMMUNITY_REGISTER_REQUIRED_FILEDS' ); ?></td>					
		</tr>
<?php
	}
?>				
		<tr>
			<td class="listkey" >&nbsp;</td>
			<td class="listvalue">
				<div id="cwin-wait" style="display:none;"></div>
				<input class="button validateSubmit" type="submit" id="btnSubmit" value="<?php echo JText::_('COM_COMMUNITY_NEXT'); ?>" name="submit">
			</td>					
		</tr>
	</tbody>
	</table>
	<input type="hidden" name="profileType" value="<?php echo $profileType;?>" />
	<input type="hidden" name="task" value="updateProfile" />
	</form>
	<script type="text/javascript">
	    cvalidate.init();
	    cvalidate.setSystemText('REM','<?php echo addslashes(JText::_("COM_COMMUNITY_ENTRY_MISSING")); ?>');

		joms.jQuery( '#jomsForm' ).submit( function() {
		    joms.jQuery('#btnSubmit').hide();
			joms.jQuery('#cwin-wait').show();
		});
	</script>	
<?php
}
else
{
?>
	<div><?php echo JText::_('COM_COMMUNITY_NO_CUSTOM_PROFILE_CREATED_YET');?></div>
<?php
}
?>