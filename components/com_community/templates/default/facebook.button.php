<?php
/**
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/** detect and display facebook language **/
if (!defined('FACEBOOK_LANG_AVAILABLE')) {
define('FACEBOOK_LANG_AVAILABLE', 1);
}

$lang = &JFactory::getLanguage();
$currentLang =  $lang->get('tag');

$fbLang =   explode(',', trim(FACEBOOK_LANGUAGE) );
$currentLang = str_replace('-','_',$currentLang);
$fbLangScript = '<script src="http://connect.facebook.net/en_GB/all.js" type="text/javascript"></script>';

if(in_array($currentLang,$fbLang)==FACEBOOK_LANG_AVAILABLE){
    $fbLangScript = '<script src="http://connect.facebook.net/'.$currentLang.'/all.js" type="text/javascript"></script>';
}

$fbLangScript = CUrlHelper::httpsURI($fbLangScript);
?>

<div id="fb-root"></div><b><?php echo JText::_('COM_COMMUNITY_OR');?></b>&nbsp;
<script type="text/javascript">
function cFbButtonInitLoop(){
		// keep looping until user status is not 'notConnected'
		if( typeof window.FB != 'undefined'
			&& window.FB._apiKey != '<?php echo $config->get('fbconnectkey');?>' 
			&& typeof window.jomFbButtonInit == 'function' ){
			jomFbinit();
		}
		else
		{ 
			setTimeout("cFbButtonInitLoop();", 500);  
		}
	}

cFbButtonInitLoop();
</script>
<?php echo $fbLangScript; ?>
<script type="text/javascript">
function jomFbButtonInit(){
	FB.init({
		appId: '<?php echo $config->get('fbconnectkey');?>', 
		status: true, 
		cookie: true, 
		oauth: true,
		xfbml: true
		});
	}
	
	 FB.Event.subscribe('auth.login', function(response) {
          //window.location.reload();
        });
	
	/*
	FB.Event.subscribe('auth.logout', function(response) {
	  window.location.reload();
	});
	*/


if( typeof window.FB != 'undefined' ) {
	jomFbButtonInit();
} else {
	window.fbAsyncInit = jomFbButtonInit;
}

</script>
<fb:login-button  onlogin="joms.connect.update();" scope="read_stream,publish_stream,offline_access,email,user_birthday,status_update,user_status"><?php echo JText::_('COM_COMMUNITY_SIGN_IN_WITH_FACEBOOK');?></fb:login-button>

