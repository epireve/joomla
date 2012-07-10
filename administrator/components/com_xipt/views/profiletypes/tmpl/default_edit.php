<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');

?>
<script type="text/javascript" src="<?php echo JURI::root().'components/com_xipt/assets/js/jquery1.4.2.js';?>" ></script>
<script type="text/javascript">jQuery.noConflict();</script>
<script language="javascript" type="text/javascript">
<?php 
If(!XIPT_JOOMLA_15)
{
	?>
/** FOR JOOMLA1.6++ **/
Joomla.submitbutton=function(action) {
	submitbutton(action);
}
<?php }?>

function submitbutton(action){	
	var form = document.adminForm;
		switch(action)
		{
		case 'save':case 'apply':			
			if( form.name.value == '' )
			{
				alert( "<?php echo XiptText::_( 'YOU_MUST_PROVIDE_A_PROFILETYPE_NAME', true ); ?>" );
				break;
			}
			<?php
	                $editor =& JFactory::getEditor();
	                echo $editor->save( 'tip' );
	        ?>
		case 'publish':
		case 'unpublish':
		case 'cancel':
		default:
			submitform( action );
		}
	}
	
	jQuery(document).ready(function($){
    	$('#resetAll1').click(function(){
           if(!confirm('Are you confirm to reset properties of all existing users')){
				$('#resetAll0').attr("checked", "checked"); 
           }
        });
	});
</script>

<script>
/** Hide privacy option  and hide watermark positions according to type of watermark**/
jQuery(document).ready(function($){ 
	var disablePrivacy = function (disable){ 
		if(disable == null)
			return;
      $('input[id^=privacyprivacy]').attr('disabled',true);
      $('input[id^=privacynotify]').attr('disabled',true);
	};
     var disablewatermrkposition= function (disable){
           if(disable == null)
               return;

        $('#watermarkparamsxiWatermarkPosition').children('option[value="lt"]').hide();
   		$('#watermarkparamsxiWatermarkPosition').children('option[value="lb"]').hide();
   		$('#watermarkparamsxiWatermarkPosition').children('option[value="rt"]').hide();
   		$('#watermarkparamsxiWatermarkPosition').children('option[value="rb"]').hide(); 
     };
	 

	 if(0 == $('input[name=privacy[jsPrivacyController]]:checked').val()){
	    disablePrivacy(true);
	  }	
	 $('#privacyjsPrivacyController0').click(function(){
		disablePrivacy(true);
	  });

	$('#privacyjsPrivacyController1').click(function(){
	    $('input[id^=privacyprivacy]').attr('disabled',false);
		$('input[id^=privacynotify]').attr('disabled',false);
     });	

	 if(1 == $('input[name=watermarkparams[typeofwatermark]]:checked').val()){
		disablewatermrkposition(true); 
	 }
	$('#watermarkparamstypeofwatermark1').click(function(){
		disablewatermrkposition(true);
	});

	$('#watermarkparamstypeofwatermark0').click(function(){
		$('#watermarkparamsxiWatermarkPosition').children('option[value="lt"]').show();
		$('#watermarkparamsxiWatermarkPosition').children('option[value="lb"]').show();
		$('#watermarkparamsxiWatermarkPosition').children('option[value="rt"]').show();
		$('#watermarkparamsxiWatermarkPosition').children('option[value="rb"]').show();
	 });
	
});
</script>


<div id="JSPT">
<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo XiptText::_('CREATE_NEW_PROFILE_TYPE_FOR_YOUR_SITE');?>
</div>
<div id="error-notice" style="color: red; font-weight:700;"></div>
<div class="xippElements">
	<form enctype="multipart/form-data" action="<?php echo JURI::base();?>index.php?" method="post" name="adminForm" id="adminForm">
		<div class="elementColumn">
			<fieldset class="adminform">
			<legend><?php echo XiptText::_( 'PROFILETYPE_SETTINGS' ); ?></legend>
			
			<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('TITLE'); ?>::<?php echo XiptText::_('PTYPE_TITLE_DESC'); ?>"><?php echo XiptText::_('TITLE'); ?>
						</label>
					</div>
					<div class="paramValue"><input type="text" value="<?php echo $this->data->name;?>" name="name" /></div>
			</div>
			
			<div class="elementParams">
					<div class="paramTitle">				
						<label class="hasTip" title="<?php echo XiptText::_('PUBLISHED'); ?>::<?php echo XiptText::_('PTYPE_PUBLISHED_DESC'); ?>"><?php echo XiptText::_('PUBLISHED');?>
						</label>
					</div>
					<div class="paramValue"><?php echo JHTML::_('select.booleanlist',  'published', '', $this->data->published);?></div>
			</div>
			
			<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('REQUIRE_APPROVAL'); ?>::<?php echo XiptText::_('PTYPE_REQUIRE_APPROVAL_DESC'); ?>">
								<?php echo XiptText::_('REQUIRE_APPROVAL');?>
						</label>
					</div>	
					<div class="paramValue"><?php echo JHTML::_('select.booleanlist',  'approve', '', $this->data->approve );?></div>
			</div>
						
			
			<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('DEFAULT_JOOMLA_USER_TYPE_SETTINGS_FOR_PROFILE'); ?>::<?php echo XiptText::_('PTYPE_DEFAULT_JOOMLA_USER_TYPE_SETTINGS_FOR_PROFILE_DESC'); ?>">
								<?php echo XiptText::_('DEFAULT_JOOMLA_USER_TYPE_SETTINGS_FOR_PROFILE');?>
						</label>
					</div>	
					<div class="paramValue"><?php echo XiptHelperProfiletypes::buildTypes($this->data->jusertype,'jusertype');?></div>
			</div>		
					
			<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('DEFAULT_TEMPLATE_SETTINGS_FOR_PROFILE'); ?>::<?php echo XiptText::_('PTYPE_DEFAULT_TEMPLATE_SETTINGS_FOR_PROFILE_DESC'); ?>">
								<?php echo XiptText::_('DEFAULT_TEMPLATE_SETTINGS_FOR_PROFILE');?>
						</label>
					</div>	
					<div class="paramValue"><?php echo XiptHelperProfiletypes::buildTypes($this->data->template, 'template');?></div>
			</div>			
						
			<div class="elementParams">
					<div class="paramTitle">
						<label class="hasTip" title="<?php echo XiptText::_('DESCRIPTION_OF_PROFILE_TYPE'); ?>::<?php echo XiptText::_('PTYPE_DESCRIPTION_OF_PROFILE_TYPE_DESC'); ?>">
								<?php echo XiptText::_('DESCRIPTION_OF_PROFILE_TYPE');?>
						</label>
					</div>	
					<div class="paramValue"><?php echo $editor->display( 'tip',  htmlspecialchars($this->data->tip, ENT_QUOTES),'350', '200', '60', '20', array('pagebreak', 'readmore') ) ;?></div>
			</div>						
    	</fieldset>
     </div>
     
     <div class="elementColumn">
		<fieldset class="adminform">
		<legend><?php echo XiptText::_( 'PARAMETERS' ); ?>	</legend>
		
			<?php echo $this->pane->startPane("parameters-pane");?>
			<?php echo $this->pane->startPanel(XiptText :: _('ASSIGNMENTS'), 'assignments-page');?>
				
			<div class="elementParams">
					<div class="paramTitle">						
					<label class="hasTip" title="<?php echo XiptText::_('DEFAULT_AVATAR');?>::<?php echo XiptText::_('DEFAULT_AVATAR_DESC'); ?>">
							<?php echo XiptText::_('DEFAULT_AVATAR');?>
					</label>
					</div>
					<div class="paramValue">
					<img src="<?php echo JURI::root().XiptHelperUtils::getUrlpathFromFilePath($this->data->avatar);?>" width="64" height="64" border="0" alt="<?php echo $this->data->avatar; ?>" />
					<div class='clr'></div>
					<input class="inputbox button" type="file" id="file-upload" name="FileAvatar" style="color: #666;" />
					<div class='clr'></div>
					<?php $link = XiptRoute::_('index.php?option=com_xipt&view=profiletypes&task=removeAvatar&id='.$this->data->id.'&oldAvatar='.$this->data->avatar, false); ?>
											<a href="<?php echo $link; ?>"><?php echo XiptText::_('REMOVE_AVTAR'); ?></a>
					</div>
			</div>
			
			<div class="elementParams">
					<div class="paramTitle">					
					<label class="hasTip" title="<?php echo XiptText::_('VISIBLE');?>::<?php echo XiptText::_('VISIBLE_DESC'); ?>">
									<?php echo XiptText::_('VISIBLE');?>
					</label>
					</div>
					<div class="paramValue"><?php echo JHTML::_('select.booleanlist',  'visible', '', $this->data->visible);?></div>
			</div>
			
			<div class="elementParams">
					<div class="paramTitle">												
						<label class="hasTip" title="<?php echo XiptText::_('ALLOW_TEMPLATE');?>::<?php echo XiptText::_('ALLOW_TEMPLATE_DESC'); ?>"><?php echo XiptText::_('ALLOW_TEMPLATE');?>
						</label>
					</div>
					<div class="paramValue"><?php echo JHTML::_('select.booleanlist',  'allowt', '', $this->data->allowt );?></div>
			</div>

			<div class="elementParams">
					<div class="paramTitle">			
						<label class="hasTip" title="<?php echo XiptText::_('SELECT_DEFAULT_GROUP_TO_ASSIGN');?>::<?php echo XiptText::_('SELECT_DEFAULT_GROUP_TO_ASSIGN_DESC'); ?>">
								<?php echo XiptText::_('SELECT_DEFAULT_GROUP_TO_ASSIGN');?>
						</label>
					</div>
					<div class="paramValue"><?php echo XiptHelperProfiletypes::buildTypes($this->data->group,'group',true);?></div>
			</div>				
						
							<?php 
								echo $this->pane->endPanel();								
								
								echo $this->pane->startPanel(XiptText :: _('PRIVACY_SETTINGS'), 'xiprivacysettings-page');
								echo $this->privacyParams->render('privacy');
								echo $this->pane->endPanel();
								
								echo $this->pane->startPanel(XiptText::_('REGISTRATION'), 'xiconfiguration-page');
								echo $this->configParams->render('config');
								echo $this->pane->endPanel();
							
								echo $this->pane->startPanel(XiptText :: _('WATERMARK'), 'watermark-page');
								echo $this->watermarkParams->render('watermarkparams');
								echo $this->pane->endPanel();
								echo $this->pane->startPanel(XiptText::_('RESET_ALL'), 'resetall-page');
							?>
														
							<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
								<?php echo XiptText::_('ON_SAVING_DO_YOU_WANT_TO_RESET_PROPERTIES_OF_ALL_EXISTING_USERS');?>
							</div>
							<div class="elementParams">
								<div class="paramValue"><?php echo JHTML::_('select.booleanlist',  'resetAll', '', '0' ); ?></div>
							</div>	
							
							<?php 					
								echo $this->pane->endPanel();
								echo $this->pane->endPane();
							?>
		</fieldset>
	</div>				

<div class="clr"></div>
	<input type="hidden" name="option" value="com_xipt" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' , 'profiletypes' );?>" />
	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
</div>
<?php 

