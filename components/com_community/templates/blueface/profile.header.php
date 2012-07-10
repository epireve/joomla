<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') or die();
?>
<?php echo $adminControlHTML; ?>

<div id="profile-header">
    <h2 class="welcometext">
        <?php if( $isMine ): ?>
            <?php echo JText::sprintf('COM_COMMUNITY_PROFILE_WELCOME_BACK', $user->getDisplayName() ); ?>
        <?php else : ?>
            <?php echo $this->escape( $user->getDisplayName() ); ?>
        <?php endif; ?>
    </h2>
        
	<!-- TODO: Message that shows that message is updated -->
	<div id="message" class="notice" style="display: none;"><?php echo JText::_('COM_COMMUNITY_STATUS_UPDATED'); ?></div>

    <?php if ( $isMine ) : ?>
    <!-- begin: #profile-new-status -->
    <script type="text/javascript" language="javascript">
    joms.jQuery(document).ready(function(){
        
        var profileStatus = joms.jQuery('#profile-new-status');
        var statusText    = joms.jQuery('#statustext');
        var saveStatus    = joms.jQuery('#save-status');
        var editStatus    = joms.jQuery('#edit-status');

        statusText.data('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION', '<?php echo addslashes(JText::_('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION')); ?>')

        function setBlankMode() {
            if (joms.jQuery.trim(statusText.val())=='')
            {
                profileStatus.removeClass('editMode')
                             .addClass('blankMode');
                statusText.val(statusText.data('COM_COMMUNITY_PROFILE_STATUS_INSTRUCTION'));
                updateTextarea();
            }
        }

        function setEditMode()
        {
            if (profileStatus.hasClass('blankMode'))
            {
                statusText.val('');
            } else {
                statusText.select();
            }
            
            profileStatus.removeClass('blankMode')
                         .addClass('editMode');
                         
            statusText.data('oldStatusText', statusText.val());

            updateTextarea();
        }
        
        function updateTextarea()
        {
            joms.utils.textAreaWidth(statusText);
            joms.utils.autogrow(statusText);   
        }

        // First time init
        setBlankMode();
        updateTextarea();

        statusText.focus(function()
        {
            setEditMode();
        }).blur(function()
        {
            setTimeout(function(){setBlankMode();}, 200);
        });
    
        saveStatus.click(function()
        {
            var newStatusText = statusText.val();

            if (newStatusText!=statusText.data('oldStatusText'))
            {
                jax.call('community', 'status,ajaxUpdate', statusText.val());
//                 joms.jQuery('#profile-status-message').html(newStatusText);
            }
            
            profileStatus.removeClass('editMode');
        });
        
        editStatus.click(function()
        {
            statusText.trigger('focus');
        });
        
        joms.profile.setStatusLimit( statusText );
    });
    </script>

    <?php else : ?>
        <?php if ( !empty( $user->_status ) ) : ?>
        <div id="profile-status-message" class="statustext"><?php echo $user->getStatus(); ?></div>
        <?php endif; ?>
    <?php endif; ?>

	<?php
		$userstatus->render();
	?>

		<?php if( !$isMine ): ?>
		<ul class="profile-actions">
			<!-- Add Friend -->
			<?php if(!$isFriend && !$isMine && !$isBlocked) { ?>
		    <li class="profile-action add-friend">
		        <a class="jsIcon1 icon-add-friend" href="javascript:void(0)" onclick="joms.friends.connect('<?php echo $profile->id;?>')">
		            <span><?php echo JText::_('COM_COMMUNITY_PROFILE_ADD_AS_FRIEND'); ?></span>
		        </a>
		    </li>
			<?php } ?>

			<!-- Gallery -->
			<?php if($config->get('enablephotos')): ?>
		    <li class="profile-action gallery">
		        <a class="jsIcon1 icon-photos" href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=myphotos&userid='.$profile->id); ?>">
		            <span><?php echo JText::_('COM_COMMUNITY_PHOTOS'); ?></span>
		        </a>
		    </li>
			<?php endif; ?>

			<!-- Blog -->
			<?php if($showBlogLink): ?>
		    <li class="profile-action blog">
				<a class="jsIcon1 icon-blog" href="<?php echo JRoute::_('index.php?option=com_myblog&blogger=' . $user->getDisplayName() . '&Itemid=' . $blogItemId ); ?>">
					<span><?php echo JText::_('COM_COMMUNITY_BLOG'); ?></span>
				</a>
			</li>
			<?php endif; ?>

			<!-- Videos -->                                	
			<?php if($config->get('enablevideos') ): ?>
		    <li class="profile-action videos">
				<a class="jsIcon1 icon-videos" href="<?php echo CRoute::_('index.php?option=com_community&view=videos&task=myvideos&userid='.$profile->id); ?>">
					<span><?php echo JText::_('COM_COMMUNITY_VIDEOS_GALLERY'); ?></span>
				</a>
			</li>
			<?php endif; ?>

			<!-- Write Message -->
			<?php if( !$isMine && $config->get('enablepm') ): ?>
		    <li class="profile-action write-message">
		        <a class="jsIcon1 icon-write" onclick="<?php echo $sendMsg; ?>" href="javascript:void(0);">
		            <span><?php echo JText::_('COM_COMMUNITY_INBOX_SEND_MESSAGE'); ?></span>
		        </a>
		    </li>
			<?php endif; ?>

			<div style="clear: left;"></div>
		</ul>
	<?php endif; ?>


</div>
