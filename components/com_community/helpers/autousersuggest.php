<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	user 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php' );
	
/**
 * Function to add input text box with auto user suggest option.
 * param - fieldId			: string - id of the input box
 *       - $hiddenField		: boolean - set true for for a hidden input text that will store the userid
 *       - $hiddenFieldId	: string - id of the hidden input box*        
 *       - width			: int - specify a custom width for the input box 
 *       - minChar			: int - minimum character for performing auto suggest
 *       - delay			: int - delay before getting suggestion from database, milisecond	 
 *       - cacheLength		: int - amount to cache, set to 1 if no cache is needed
 */
function getAutoSuggestInput($fieldId = 'autoSuggestUserName', $hiddenField = false, $hiddenFieldId = 'autoSuggestUserId', $width = 200, $minChar = 3, $delay = 400, $cacheLength = 10)
{
	$js = 'assets/autocomplete-1.0.js';
	CAssets::attach($js, 'js');
	
	$onClickAction		= (!empty($hiddenField))? "onclick=\"joms.jQuery(\'#$hiddenFieldId\').val('+data[1]+');\"" : "";		
	$autoCLink  		= CRoute::_( 'index.php?option=com_community&view=autousersuggest&task=ajaxAutoUserSuggest&no_html=1&tmpl=component' );
	
	$js =<<<SHOWJS
		joms.jQuery().ready(function(){
			joms.jQuery("#$fieldId").autocomplete("$autoCLink", {
				minChars:$minChar, 
				matchSubset:1, 
				delay: 200,
				cacheLength:$cacheLength, 
				multiple:false,
				formatItem: function(data, i, n, value) {
            		return '<div $onClickAction><div style="vertical-align: middle; display: inline; width: 45px; float: left;"><img src="'+data[2]+'" width="30" height="30"/></div><div style="vertical-align: middle; line-height: 25px;">'+data[0]+'</div></div>';
        		},
        		formatResult: function(data, value) {
            		return data[0];
 				}

			});
			joms.jQuery("#$fieldId").result(function(event, data, formatted) {
					if (data)
						joms.jQuery("#$hiddenFieldId").val(data[1]);
			});
			joms.jQuery("#$fieldId").val('');
			joms.jQuery("#$hiddenFieldId").val('');
		});
SHOWJS;
	
	$document =& JFactory::getDocument();
	$document->addScriptDeclaration($js);
	
	$content = '';
	ob_start();
	?>
	<input name="<?php echo $fieldId; ?>" id="<?php echo $fieldId; ?>" type="text" value="" autocomplete="off" style="width: <?php echo $width; ?>px;"/>
	<?php
	if(!empty($hiddenField))
	{
	?>
		<input name="<?php echo $hiddenFieldId; ?>" id="<?php echo $hiddenFieldId; ?>" type="hidden" value=""/>
	<?php
	}
	$content = ob_get_contents();
	ob_end_clean();
	
	return $content;
}
