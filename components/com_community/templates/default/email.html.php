<?php
/**
 * @package		JomSocial
 * @subpackage	Core 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div style="background-color:#ededeb;">
<center>
<table width="567" cellpadding="0" cellspacing="0" border="0" style="font-family: Arial, Helvetica, sans-serif;margin-top:30px;margin-bottom:30px;">
<tr><td height="30" style="background-color:#ededeb;"></td></tr>
<tr><td height="3" style="background-color:#605f5f;"></td></tr>
<tr>
	<td height="57" style="background-color:#383838;padding:0 20px;" >
		<h1 style=" font-size: 20px; font-weight: bold;">
		<a href="<?php echo JURI::root();?>" style="text-decoration: none; color: #ffffff;">
		&#x2709; &nbsp;<?php echo $sitename;?></a></h1>
	</td>
</tr>
<tr><td height="15" style="background-color:#EDEDEB;"></td></tr>
<tr>
	<td style="background-color:#ffffff;border:1px solid #d7d7d7;padding:20px;">
		<hr style="border: 0;color: #ededeb;background-color:#ededeb;height:1px;width:100%;text-align: left;">
		<div style="margin:20px auto;font-size:13px;line-height:18px;color:#444444;">
		<?php echo $content; ?><br>
		</div>
		<hr style="border: 0;color: #ededeb;background-color:#ededeb;height:1px;width:100%;text-align: left;">
		<div style="text-align:center;font-size:11px;line-height:15px;color:#444444;margin:15px auto 5px;">
			<?php echo $this->getPoweredByLink();?></a>.
				<br>
			<?php
			if( empty($userid) && !empty($recepientemail) ){
			    echo JText::sprintf('COM_COMMUNITY_EMAIL_INVITE_FRIEND_FOOTER_TEXT', $name, $email, $recepientemail, $sitename, $unsubscribeLink);
			} else {
			    echo JText::sprintf('COM_COMMUNITY_EMAIL_FOOTER_TEXT', $name, $email,$sitename, $unsubscribeLink);
			}

			
			?>

				<br><?php echo $copyrightemail; ?>
		</div>
	</td>
</tr>
<tr><td height="3" style="background-color:#e0dfdf;"></td></tr>
<tr><td height="30" style="background-color:#ededeb;"></td></tr>
</table>
</center>
</div>