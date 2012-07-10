<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	author		string
 * @param	id			integer the wall object id 
 * @param	authorLink 	string link to author 
 * @param	created		string(date)
 * @param	content		string 
 * @param	avatar		string(link) to avatar
 * @params	isMine		boolean is this wall entry belong to me ? 
 */
defined('_JEXEC') or die();
?>
<div id="wall_<?php echo $id; ?>" class="wallComments cComments">
    <div class="cavatar"><?php echo $avatarHTML; ?></div>
    <div class="ccontent-avatar">
    <div class="actor-link">
        <a href="<?php echo $authorLink; ?>"><?php echo $author; ?></a>
    </div>
	
    <div class="cComments-content">
        <span id="wall-message-<?php echo $id;?>"><?php echo $content; ?></span>
		<?php if($isEditable) { ?>
		<div id="wall-edit-container-<?php echo $id;?>"></div>
		<?php } ?>
        <?php echo $commentsHTML; ?>
    </div>
		
	
	<!-- Test newsfeed meta style -->
	<div class="newsfeed-meta small cMeta">		
		<?php echo $created; ?>
		
		<?php if($config->get('wallediting')){ ?>
    <!--TIME LEFT TO EDIT REPLY-->
    <?php if($isEditable){?>
    	&middot; <?php echo JText::sprintf('COM_COMMUNITY_TIME_LEFT_TO_EDIT_REPLY' , $editInterval , '<a onclick="joms.walls.edit(\'' . $id . '\',\'' . $processFunc.'\');" href="javascript:void(0)">' . JText::_('COM_COMMUNITY_EDIT') . '</a>' );?>
    <?php } ?>
    <!--end: TIME LEFT TO EDIT REPLY-->
    <?php } ?>
			
		<div class="clr"></div>
	</div>
	<!-- End Test newsfeed meta style -->
	
	<?php if($isMine) { ?>
	<!-- Allow delete: -->
	<div class="newsfeed-remove">
		<a onclick="wallRemove(<?php echo $id; ?>);return false;" href="javascript:void(0)" class="remove" ><?php echo JText::_('COM_COMMUNITY_WALL_REMOVE');?></a>
	</div>
	<?php } ?>
	
	</div>
	<div class="clr">&nbsp;</div>
</div>

