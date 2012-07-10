<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>


<div class="inbox-list" id="inbox-listing">
	<?php foreach ( $messages as $message ) : ?>
	<div class="inbox-unread" id="message-<?php echo $message->id; ?>">
		<table border="0" cellpadding="2" cellspacing="0" width="100%">
		    <tr>
		        <td width="50">
		        	<a href="<?php echo $message->profileLink; ?>">
		            	<img width="32" src="<?php echo $message->avatar; ?>" alt="<?php echo $this->escape( JString::ucfirst( $message->from_name ) ); ?>" class="cAvatar" />
		            </a>
				</td>
				<td>
					<a class="subject" href="<?php echo CRoute::_('index.php?option=com_community&view=inbox&task=read&msgid='. $message->parent); ?>">
						<img src="<?php echo JURI::root(); ?>components/com_community/templates/default/images/new.gif"  style="vertical-align:middle" />						
						<?php echo $message->subject; ?>
					</a>
					<div class="small">
					    <?php echo $this->escape( $message->from_name ); ?>, 
						<?php
							$postdate =  CTimeHelper::getDate($message->posted_on);
							echo $postdate->toFormat( JText::_('DATE_FORMAT_LC2') );
						?>
					</div>
				</td>
		    </tr>
		</table>
	</div>
	<?php endforeach; ?>
</div>
