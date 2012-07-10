<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	author		string
 * @param	$results	An array of user objects for the search result
 */
 
defined('_JEXEC') or die();
?>


<div class="cToolbarBand">
	<div class="bandContent">

		<script type="text/JavaScript">
		
		jsAdvanceSearch = {
					action: {
						keynum : 0,
						dateFormatDesc : '<?php echo JText::_("COM_COMMUNITY_DATE_FORMAT_DESCRIPTION"); ?>',
						addCriteria: function ( ) {
							var criteria = "";
							var keynum = jsAdvanceSearch.action.keynum;
							
							criteria +='<div id="criteria'+keynum+'" style="margin: 0 0 10px 0;">';
								criteria +='<div id="removelink'+keynum+'"  style="float:left; margin:0 6px 0 2px; padding-top:5px;">';
									criteria +='<a class="remove-left" href="javascript:void(0);" onclick="jsAdvanceSearch.action.removeCriteria(\''+keynum+'\');">';
										criteria +='<?php echo JText::_('COM_COMMUNITY_HIDE_CRITERIA');?>';
									criteria +='</a>';
								criteria +='</div>';
								criteria +='<div id="selectfield'+keynum+'"  style="float:left; margin-right:10px;">';
									criteria +='<select class="inputbox" name="field'+keynum+'" id="field'+keynum+'" onchange="jsAdvanceSearch.action.changeField(\''+keynum+'\');" style="width:150px;">';
										<?php 
										foreach($fields as $label=>$data)
										{
											if($data->published && $data->visible)
											{
										?>
												criteria +='<optgroup label="<?php echo addslashes( JText::_($label) );?>">';
													<?php
													foreach($data->fields as $key=>$field)
													{
														if($field->published && $field->visible && $field->searchable)
														{
															$selected = "";
															if($field->fieldcode == 'username')
															{
																$selected = "SELECTED";
															}
													?>
															criteria +='<option value="<?php echo addslashes($field->fieldcode); ?>" <?php echo $selected; ?>><?php echo JText::_(addslashes(JString::trim($field->name)));?></option>';
													<?php
														}
													}
													?>
												criteria +='</optgroup>';
										<?php
											}
										}
										?>
									criteria +='</select>';
								criteria +='</div>';
								criteria +='<div id="selectcondition'+keynum+'" style="float:left; margin-right:10px;">';
									criteria +='<select class="inputbox" name="condition'+keynum+'" id="condition'+keynum+'" style="width:150px;">';
										criteria +='<option value=""></option>';
									criteria +='</select>';
								criteria +='</div>';
								criteria +='<div id="valueinput'+keynum+'" style="margin-right:10px;float:left;">';
									criteria +='<input class="inputbox" type="text" name="value'+keynum+'" id="value'+keynum+'" style="width:145px;"/>';
								criteria +='</div>';
								criteria +='<div id="valueinput'+keynum+'_2" style="float:left; margin-right:-10px;">';
								criteria +='</div>';
								criteria +='<div id="typeinput'+keynum+'" style="float:left; margin-right:10px; display:none;">';
									criteria +='<input class="inputbox" type="hidden" name="fieldType'+keynum+'" id="fieldType'+keynum+'" value="" style="width:150px;"/>';
								criteria +='</div>';
								criteria +='<div style="clear:both"></div>';
							criteria +='</div>';
									
							var comma = '';
							if(joms.jQuery('#key-list').val()!="")
							{
								var comma = ',';
							}
							joms.jQuery('#key-list').val(joms.jQuery('#key-list').val()+comma+keynum);		
							
							
							
							joms.jQuery('#criteriaContainer').append(criteria);
							jsAdvanceSearch.action.changeField(keynum);
							jsAdvanceSearch.action.keynum++;
						},
						removeCriteria: function ( id ) {
							var inputs = [];
							var _id, _id2;
							_id = joms.jQuery('#key-list').val();
							_id2 = _id.split(',');
							
							joms.jQuery(_id2).each(function() {
								if ( this != id && this != "") {
									// re-populate
									inputs.push(this);								
								}
							});
							
							joms.jQuery("#criteria"+id).remove();
							joms.jQuery('#key-list').val(inputs.join(','));
						},
						getFieldType: function ( fieldcode ) {
							var type;	
							switch(fieldcode)
							{
								<?php
								foreach($fields as $label=>$data)
								{
									if($data->published && $data->visible)
									{
										foreach($data->fields as $key=>$field)
										{
											if($field->published && $field->visible)
											{
										?>		
												case "<?php echo $field->fieldcode; ?>":
													type = "<?php echo $field->type; ?>";
													break;
										<?php
											}
										}						
									}
								}
								?>
								default :
									type = "default";
							}	
							return type;
						},
						getListValue: function ( id, fieldcode ) {
							var list;	
							switch(fieldcode)
							{
								<?php
								foreach($fields as $label=>$data)
								{
									if($data->published && $data->visible)
									{
										foreach($data->fields as $key=>$field)
										{
											if($field->published && $field->visible)
											{
												if(!empty($field->options))
												{
											?>		
													case "<?php echo $field->fieldcode; ?>":
														<?php if ($field->type == 'checkbox') { ?>
															list	= '<div class="clr"></div>';
															list	+= '<div style="padding: 0px; margin-left: 20px; margin-top: 5px;" class="inputbox">';
															<?php
															foreach($field->options as $data)
															{
															?>
																list += '<div style="padding:0 10px 0 0;float: left;"><input type="checkbox" class="inputbox" name="value'+id+'[]" value="<?php echo addslashes($data); ?>"><?php echo JText::_(addslashes(JString::trim($data))); ?></input></div>';	
															<?php
															}
															?>
															list	+= '<div class="clr"></div>';													
															list	+= '</div>'
														<?php } else { ?>
															list = '<select class="inputbox" name="value'+id+'" id="value'+id+'" style="width:157px;">';
															<?php
															foreach($field->options as $data)
															{
															?>			
																list +='<option value="<?php echo addslashes($data); ?>"><?php echo JText::_(addslashes(JString::trim($data))); ?></option>';
															<?php
															}
															?>
															list +='</select>';
															
														<?php } ?> 
														break;
											<?php
												}
											}
										}						
									}
								}
								?>
								default :
									list = '<input class="inputbox" type="text" name="value'+id+'" id="value'+id+'" style="width:145px;"/>';
							}	
							return list;
						},
						changeField: function ( id ) {
							var value, type, condHTML, listValue;
							var cond = [];
							var conditions = new Array();
							conditions['contain']			= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_CONTAIN'))); ?>";
							conditions['between']			= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_BETWEEN'))); ?>";
							conditions['equal']				= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_EQUAL'))); ?>";
							conditions['notequal']			= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_NOT_EQUAL'))); ?>";
							conditions['lessthanorequal']	= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_LESS_THAN_OR_EQUAL'))); ?>";
							conditions['greaterthanorequal']	= "<?php echo addslashes(JString::trim(JText::_('COM_COMMUNITY_GREATER_THAN_OR_EQUAL'))); ?>";
							
							value	= joms.jQuery('#field'+id).val();
							type 	= jsAdvanceSearch.action.getFieldType(value);
							this.changeFieldType(type, id);
										
							switch(type)
							{			
								case 'date'		:
									cond		= ['between', 'equal', 'notequal', 'lessthanorequal', 'greaterthanorequal'];
									listValue	= 0;
									break;
								case 'birthdate':
									cond		= ['between', 'equal', 'lessthanorequal', 'greaterthanorequal'];
									listValue	= 0;
									break;
								case 'checkbox'	:
								case 'radio'	:
								case 'singleselect'	:
								case 'select'	:
								case 'list'		:
									cond	  = ['equal', 'notequal'];
									listValue = this.getListValue(id, value);
									break;
								case 'email'	:
									cond	  = ['equal'];
									listValue = 0;
									break;								
								case 'textarea'	:						
								case 'text'		:
								default			:																			
									if(value == 'useremail')
									{								
										cond	= ['equal'];							
									}
									else
									{
										cond	= ['contain', 'equal', 'notequal'];								
									}							
									listValue = 0;
									break;
							}
					
							condHTML = '<select class="inputbox" name="condition'+id+'" id="condition'+id+'" style="width:150px;" onchange="jsAdvanceSearch.action.changeCondition('+id+');">';
							joms.jQuery(cond).each(function(){
								condHTML +='<option value="'+this+'">'+conditions[this]+'</option>';
							});
							condHTML +='</select>';
							
							joms.jQuery('#selectcondition'+id).html(condHTML);
							jsAdvanceSearch.action.changeCondition(id);
							jsAdvanceSearch.action.calendar(type, id);
							if(listValue!=0){
								joms.jQuery('#valueinput'+id).html(listValue);
							}
						},
						addAltInputField: function(type, id) {
							var cond = joms.jQuery('#condition'+id).val();
							var inputField;
							if(cond == "between"){
								if(type=='birthdate' || type=='date'){
									inputField  = '<input class="inputbox" type="text" name="value'+id+'_2" id="value'+id+'_2" style="width:125px; margin-right:4px" value="" title="'+this.dateFormatDesc+'" readonly="true"/><img class="calendar" src="<?php echo rtrim( JURI::root(), "/"); ?>/components/com_community/assets/calendar.png" alt="calendar" id="value'+id+'_2_img" />';
								}else{
									inputField  = '<input type="text" name="value'+id+'_2" id="value'+id+'_2" style="width:125px; margin-right:4px" value=""/>';
								}
							}else{
								inputField = '';
							}
							joms.jQuery('#valueinput'+id+'_2').html(inputField);
							if(cond == "between"){
								if(type=='date'){
									Calendar.setup({
										inputField: 'value'+id+'_2',		// id of the input field
										ifFormat: '%Y-%m-%d',	// format of the input field
										button: 'value'+id+'_2_img',		// trigger for the calendar (button ID)
										align: 'Tl',				// alignment (defaults to "Bl")
										singleClick: true
									});					
								}
							}
						},
						calendar: function(type, id) {
							var inputField;
							if(type=='birthdate' || type=='date'){
								inputField  = '<a onclick="jsAdvanceSearch.action.toggleAgeSearch('+id+',1);" href="javascript:void(0);" title="Change to Age search"> Age </a><input class="inputbox" type="text" name="value'+id+'" id="value'+id+'" style="width:100px; margin-right:4px" value="" title="'+this.dateFormatDesc+'" readonly="false"/><img class="calendar" src="<?php echo rtrim( JURI::root(), "/"); ?>/components/com_community/assets/calendar.png" alt="calendar" id="value'+id+'_img" />';
							}else{
								inputField  = '<input class="inputbox" type="text" name="value'+id+'" id="value'+id+'" style="width:145px;"/>';
							}
							joms.jQuery('#valueinput'+id).html(inputField);
							if(type=='date'){
								Calendar.setup({
									inputField: 'value'+id,		// id of the input field
									ifFormat: '%Y-%m-%d',	// format of the input field
									button: 'value'+id+'_img',		// trigger for the calendar (button ID)
									align: 'Tl',				// alignment (defaults to "Bl")
									singleClick: true
								});					
							}
						},
						changeFieldType: function(type, id) {
							joms.jQuery('#fieldType'+id).val(type);
						},
						changeCondition: function(id) {
							var type = joms.jQuery('#fieldType'+id).val();							
							this.addAltInputField(type, id);
						},
						toggleAgeSearch: function(id,mode) {
							var cond = joms.jQuery('#condition'+id).val();
							if(mode == 1){
								inputField  = '<a onclick="jsAdvanceSearch.action.toggleAgeSearch('+id+',0);" href="javascript:void(0);" title="Change to Date search"> Date</a><input class="inputbox" type="text" name="value'+id+'" id="value'+id+'" style="width:100px; margin-right:4px" value="" />';
								joms.jQuery('#valueinput'+id).html(inputField);
								if(cond == "between"){
									inputField  = '<input class="inputbox" type="text" name="value'+id+'_2" id="value'+id+'_2" style="width:100px; margin-right:4px" value="" />';
									joms.jQuery('#valueinput'+id+'_2').html(inputField);
								}
							} else {
								jsAdvanceSearch.action.calendar('birthdate',id);
								jsAdvanceSearch.action.addAltInputField('birthdate',id);
							}
						}				
						
					}
				}
					
			joms.jQuery(document).ready( function() {
				var searchHistory, operator;
			<?php if(!empty($filterJson)){?>
				searchHistory = eval(<?php echo $filterJson; ?>);
			<?php }else{?>
				searchHistory = '';
			<?php }?>
				if(searchHistory != ''){
					var keylist = searchHistory['key-list'].split(',');
					var num;
					
					joms.jQuery(keylist).each(function(){
						num = jsAdvanceSearch.action.keynum;
						jsAdvanceSearch.action.addCriteria();
						joms.jQuery('#field'+num).val(searchHistory['field'+this]);
						jsAdvanceSearch.action.changeField(num);
						joms.jQuery('#condition'+num).val(searchHistory['condition'+this]);
						jsAdvanceSearch.action.changeCondition(num);
						
						if(searchHistory['fieldType'+this] == 'checkbox')
						{
							var myVal	= searchHistory['value'+this];
							if(joms.jQuery.isArray(myVal))
							{
								joms.jQuery.each(myVal, function(i, chkVal) {
									joms.jQuery('input[name=value'+num+'[]]').each(function() {
										if(this.value == chkVal)
										{
											this.checked = "checked";
										}
									});
								});
								
							}
						}
						else
						{
							joms.jQuery('#value'+num).val(searchHistory['value'+this]);
						}
						
						if(searchHistory['condition'+this] == 'between'){
							joms.jQuery('#value'+num+'_2').val(searchHistory['value'+this+'_2']);
						}
					})
					
					if(searchHistory.operator == 'and'){
						operator = 'operator_all';
					}else{
						operator = 'operator_any';
					}
				}else{
					operator = 'operator_all';
					jsAdvanceSearch.action.addCriteria();
				}
				joms.jQuery('#'+operator).attr("checked", true);
			});
			
		</script>
	
		<form name="jsform-search-advancesearch" action="" method="GET">
			<div id="criteriaTitle" class="infoGroupTitle">
				<?php echo JText::_("COM_COMMUNITY_CRITERIA"); ?>
			</div>
			<div id="criteriaContainer">
			</div>	
			<div id="optionContainer">
				<div>
					<a class="add" style="padding: 0 0 0 20px; display: inline; text-indent: 0px;" href="javascript:void(0);" onclick="jsAdvanceSearch.action.addCriteria();"><?php echo JText::_("COM_COMMUNITY_ADD_CRITERIA"); ?></a>
				</div>
				<div class="news-separator"></div>
				<div style="">
					<label class="lblradio" style="padding-right: 20px;"><input type="radio" name="operator" id="operator_all" value="and" class="radio"> <?php echo JText::_("COM_COMMUNITY_MATCH_ALL_CRITERIA"); ?></label>
					<label class="lblradio" style="padding-right: 20px;"><input type="radio" name="operator" id="operator_any" value="or" class="radio"> <?php echo JText::_("COM_COMMUNITY_MATCH_ANY_CRITERIA"); ?></label>
					<input type="submit" class="button" value="<?php echo JText::_("COM_COMMUNITY_SEARCH_BUTTON_TEMP");?>" style="margin-left:5px;">
					<input type="hidden" id="key-list" name="key-list" value="" />
					<input type="hidden" name="option" value="com_community" />
					<input type="hidden" name="view" value="search" />
					<input type="hidden" name="task" value="advancesearch" />
					<input type="hidden" name="Itemid" value="<?php echo CRoute::getItemId(); ?>" />
				</div>
			</div>
			<div id="criteriaList"></div>
		</form>
	</div>
	<div class="bandFooter"><div class="bandFooter_inner"></div></div>
</div>