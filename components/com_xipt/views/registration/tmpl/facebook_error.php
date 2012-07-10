<?php /**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
?>
<h4><u><?php echo XiptText::_("INVALID_USERNAME_OR_PASSWORD_AS_FACEBOOK_CONNECT");?></u></h4><br/>
<?php 
if(!$isValidUsername)
	echo XiptText::_("INVALID_USER_NAME_AS_FACEBOOK_CONNECT");

?><br /><br /><?php 

if(!$isValidEmail)
	echo XiptText::_("INVALID_EMAIL_AS_FACEBOOK_CONNECT");
	
?>
<?php 