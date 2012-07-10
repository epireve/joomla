<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 */
defined('_JEXEC') or die();

?>
<div class="page-actions">
  <?php echo $bookmarksHTML;?>
  <div class="clr"></div>
</div>

<div id="group-buletin-topic">
	<!--Buletin : Avatar-->
	<div class="author-avatar">
		<a href="<?php echo CUrlHelper::userLink($creator->id); ?>"><img class="cAvatar" src="<?php echo $creator->getThumbAvatar(); ?>" border="0" alt="" /></a>
	</div>
	<!--Buletin : Avatar-->

	<!--Buletin : Detail-->
    <div class="buletin-detail">
    
        <!--Buletin : Author & Date-->
        <div class="buletin-created">
            <?php echo JHTML::_('date' , $bulletin->date, JText::_('DATE_FORMAT_LC')); ?>
        </div>
        <!--Buletin : Author & Date-->
        
        <!--Buletin : Entry-->
        <div class="buletin-entry">
            <?php echo $bulletin->message;?>
        </div>
        <!--Buletin : Entry-->

        <!--Buletin : Edit Entry-->
        <div id="bulletin-edit-data" style="display: none;">
            <form name="addnews" method="post" action="<?php echo CRoute::_('index.php?option=com_community&view=groups&task=editnews'); ?>">
                <div>
                    <label style="font-weight: 700;" for="title"><?php echo JText::_('COM_COMMUNITY_GROUPS_BULETIN_TITLE');?></label>
                </div>
                <input type="text" value="<?php echo $bulletin->title;?>" id="title" name="title" class="inputbox" style="width: 94%; margin: 10px 0;" />
                
                <div>
                    <label style="font-weight: 700;" for="description"><?php echo JText::_('COM_COMMUNITY_GROUPS_BULLETIN_DESCRIPTION');?></label>
                </div>
                
            <?php if( $config->get( 'htmleditor' ) == 'none' && $config->getBool('allowhtml') ) { ?>
				<div class="htmlTag"><?php echo JText::_('COM_COMMUNITY_HTML_TAGS_ALLOWED'); ?></div>
			<?php } ?>
                
            <?php
				
				if( !CStringHelper::isHTML($editorMessage) 
					&& $config->get('htmleditor') != 'none' 
					&& $config->getBool('allowhtml') )
				{
					$editorMessage = CStringHelper::nl2br($editorMessage);
				}

                if( $config->get( 'htmleditor' ) )
                {
            ?>
            <script type="text/javascript">
            function saveContent()
            {
				<?php echo $editor->saveText( 'message' ); ?>
				return true;
			}
            </script>
            <?php echo $editor->displayEditor( 'message',  $editorMessage , '95%', '450', '10', '20' , false ); ?>
            <?php
                }
                else
                {
            ?>
                <textarea style="width: 94%; margin: 10px 0;" name="message"><?php echo $editorMessage;?></textarea>
            <?php
                }
            ?>
            
            <div style="text-align: center; padding-top: 20px;">
                <input type="hidden" value="<?php echo $bulletin->groupid;?>" name="groupid" />
                <input type="hidden" value="<?php echo $bulletin->id;?>" name="bulletinid" />
                <?php echo JHTML::_( 'form.token' ); ?>
                <input type="submit" value="<?php echo JText::_('COM_COMMUNITY_SAVE_BUTTON'); ?>" class="button" onclick="saveContent();" />
                <input type="button" class="button" onclick="joms.groups.editBulletin();return false;" value="<?php echo JText::_('COM_COMMUNITY_CANCEL_BUTTON'); ?>" />
            </div>
            </form>
        </div>
        <!--Buletin : Edit Entry-->
    </div>
	<!--Buletin : Detail-->

</div>