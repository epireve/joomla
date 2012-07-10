<?php
/**
 * @package	JomSocial
 * @subpackage 	Template 
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();
?>

<span class="like-snippet">
	
	<span class="like-button" title="<?php echo JText::_('COM_COMMUNITY_LIKE'); ?>"><?php echo $likes; ?></span>
	<span class="dislike-button" title="<?php echo JText::_('COM_COMMUNITY_DISLIKE'); ?>"><?php echo $dislikes; ?></span>

</span>