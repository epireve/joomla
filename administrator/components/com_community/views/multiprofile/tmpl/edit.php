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
<form name="adminForm" id="adminForm" action="index.php?option=com_community" method="POST" enctype="multipart/form-data">
<table width="100%">
	<tr>
		<td valign="top" width="40%">
			<fieldset>
				<legend><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_DETAILS');?></legend>
				<p><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_DETAILS_INFO');?></p>
				<table class="admintable" cellspacing="1">
					<tbody>
						<tr>
							<td width="300" class="key">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_TITLE' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_TITLE_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_TITLE' ); ?></span>
							</td>
							<td valign="top">
								<input type="text" title="A name identifier for this multiple profile type" maxlength="50" size="50" id="name" name="name" class="text_area" value="<?php echo $this->multiprofile->name;?>">
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_DESCRIPTION' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_DESCRIPTION_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_DESCRIPTION' ); ?></span>
							</td>
							<td valign="top">
								<textarea name="description" id="description" rows="10" cols="50"><?php echo $this->multiprofile->description;?></textarea>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_PUBLISHED' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_PUBLISHED_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_PUBLISHED' ); ?></span>
							</td>
							<td valign="top">
								<?php echo JHTML::_('select.booleanlist' , 'published' , null , is_null( $this->multiprofile->published) ? true : $this->multiprofile->published , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_REQUIRE_APPROVALS' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_REQUIRE_APPROVALS_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_REQUIRE_APPROVALS' ); ?></span>
							</td>
							<td valign="top">
								<?php echo JHTML::_('select.booleanlist' , 'approvals' , null , $this->multiprofile->approvals , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ALLOW_GROUP_CREATION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ALLOW_GROUP_CREATION_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ALLOW_GROUP_CREATION' ); ?></span>
							</td>
							<td valign="top">
								<?php echo JHTML::_('select.booleanlist' , 'create_groups' , null , $this->multiprofile->create_groups , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ALLOW_EVENT_CREATION' ); ?>::<?php echo JText::_('COM_COMMUNITY_CONFIGURATION_ALLOW_EVENT_CREATION_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_CONFIGURATION_ALLOW_EVENT_CREATION' ); ?></span>
							</td>
							<td valign="top">
								<?php echo JHTML::_('select.booleanlist' , 'create_events' , null , $this->multiprofile->create_events , JText::_('COM_COMMUNITY_YES_OPTION') , JText::_('COM_COMMUNITY_NO_OPTION') ); ?>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_MULTIPROFILE_WATERMARK' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_WATERMARK_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_MULTIPROFILE_WATERMARK' ); ?></span>
							</td>
							<td valign="top">
								<div style="float: left;">
									<div style="font-weight:700;text-decoration: underline;margin-bottom: 5px;"><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_WATERMARK');?></div>
									<?php if( !empty( $this->multiprofile->watermark) ){ ?>
										<img src="<?php echo $this->multiprofile->getWatermark();?>" style="border: 1px solid #eee;" />
									<?php } else { ?>
										<?php echo JText::_('N/A');?>
									<?php } ?>
								</div>
								<div style="float: left;margin-left: 20px;">
									<div style="font-weight:700;text-decoration: underline;margin-bottom: 5px;"><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_PREVIEW');?></div>
									<?php if( !empty( $this->multiprofile->thumb) ){ ?>
										<img src="<?php echo $this->multiprofile->getThumbAvatar();?>" style="border: 1px solid #eee;" />
									<?php } else { ?>
										<?php echo JText::_('N/A');?>
									<?php } ?>
								</div>
								<div style="clear: both;"></div>
								<div style="margin-top: 5px;">
									<input type="file" name="watermark" id="watermark" />
									<div><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_MAXIMUM_WATERMARK_IMAGE_SIZE');?></div>
								</div>
							</td>
						</tr>
						<tr>
							<td width="300" class="key" valign="top">
								<span class="hasTip" title="<?php echo JText::_( 'COM_COMMUNITY_MULTIPROFILE_WATERMARK_POSITION' ); ?>::<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_WATERMARK_POSITION_TIPS'); ?>"><?php echo JText::_( 'COM_COMMUNITY_MULTIPROFILE_WATERMARK_POSITION' ); ?></span>
							</td>
							<td valign="top">
								<div class="watermark-position" style="position:relative; width:64px; height:64px; border:1px solid #ccc; z-index:1">
									<img src="<?php echo $this->multiprofile->getThumbAvatar();?>" width="64" height="64" />
									<input type="radio" value="top" id="watermark_top" name="watermark_location" style="position:absolute; margin:0;padding:0; top:0;    left: 25px;"<?php echo ($this->multiprofile->watermark_location == 'top' ) ? ' checked="checked"' : '';?> />
									<input type="radio" value="right" id="watermark_right" name="watermark_location" style="position:absolute; margin:0;padding:0; right:0;  top:  25px;"<?php echo ($this->multiprofile->watermark_location == 'right' ) ? ' checked="checked"' : '';?>>
									<input type="radio" value="bottom" id="watermark_bottom" name="watermark_location" style="position:absolute; margin:0;padding:0; bottom:0; left: 25px;"<?php echo ($this->multiprofile->watermark_location == 'bottom' ) ? ' checked="checked"' : '';?> >
									<input type="radio" value="left" id="watermark_left" name="watermark_location" style="position:absolute; margin:0;padding:0; left:0;   top:  25px;"<?php echo ($this->multiprofile->watermark_location == 'left' ) ? ' checked="checked"' : '';?> >
								</div>
							</td>
						</tr>
					</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset>
				<legend><?php echo JText::_( 'COM_COMMUNITY_MULTIPROFILE_FIELDS');?></legend>
				<p><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_FIELDS_INFO'); ?></p>
				<div>
					<span style="color: red;font-weight:700;"><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_NOTE');?>:</span>
					<span><?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_NOTE_INFO');?></span>
				</div>
				<table class="adminlist" cellspacing="1">
					<thead>
						<tr class="title">
							<th width="1%">#</th>
							<th style="text-align: left;">
								<?php echo JText::_('COM_COMMUNITY_NAME');?>
							</th>
							<th width="15%" style="text-align: center;">
								<?php echo JText::_('COM_COMMUNITY_FIELD_CODE');?>
							</th>
							<th width="15%" style="text-align: center;">
								<?php echo JText::_('COM_COMMUNITY_TYPE');?>
							</th>
							<th width="1%" align="center">
								<?php echo JText::_('COM_COMMUNITY_MULTIPROFILE_INCLUDE');?>
							</th>
						</tr>
					</thead>
					<?php
					$count	= 0;
					$i		= 0;
		
					foreach( $this->fields as $field )
					{
						if($field->type == 'group')
						{
		?>
					<tr class="parent">
						<td  style="background-color: #EEEEEE;">&nbsp;</td>
						<td colspan="4" style="background-color: #EEEEEE;">
							<strong><?php echo JText::_('COM_COMMUNITY_GROUPS');?>
								<span><?php echo $field->name;?></span>
							</strong>
							<div style="clear: both;"></div>
							<input type="hidden" name="parents[]" value="<?php echo $field->id;?>" />
						</td>
					</tr>
						<?php
							$i	= 0;	// Reset count
						}
						else if($field->type != 'group')
						{
							// Process publish / unpublish images
							++$i;
						?>
					<tr class="row<?php echo $i%2;?>" id="rowid<?php echo $field->id;?>">
						<td><?php echo $i;?></td>
						<td><span><?php echo $field->name;?></span></td>
						<td align="center"><?php echo $field->fieldcode; ?></td>
						<td align="center"><?php echo $field->type;?></td>
						<td align="center" id="publish<?php echo $field->id;?>">
							<input type="checkbox" name="fields[]" value="<?php echo $field->id;?>"<?php echo $this->multiprofile->isChild($field->id) ? ' checked="checked"' : '';?> />
						</td>
					</tr>
				<?php
						}
					$count++;
				}
				?>
				</table>
			</fieldset>
		</td>
	</tr>
</table>

<input type="hidden" name="view" value="multiprofile" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->multiprofile->id;?>" />
<input type="hidden" name="option" value="com_community" />
<input type="hidden" name="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>	
</form>