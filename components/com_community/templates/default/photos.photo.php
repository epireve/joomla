<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

if( $photos )
{
?>

<link rel="stylesheet" type="text/css" href="<?php echo JURI::root(); ?>components/com_community/assets/imgareaselect/css/imgareaselect-default.css" />
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_community/assets/imgareaselect/scripts/jquery.imgareaselect.pack.js"></script>

<div class="page-actions clrfix"></div>
<div id="cGallery">
	<script type="text/javascript">
		joms.gallery.bindKeys();
		var jsPlaylist = {
			album: <?php echo $album->id;?>,
			photos:	[
					<?php
					CFactory::load('libraries', 'storage');
					CFactory::load('helpers', 'image');


					for($i=0; $i < count($photos); $i++ ) 
					{
						$photo	=& $photos[$i];
						$storage = CStorage::getStorage( $photo->storage );
						$imgpath = str_replace('/', DS, $photo->original);

					?>
						{id: <?php echo $photo->id; ?>,
						 loaded: false,
						 caption: '<?php echo addslashes( $photo->caption );?>',
						 thumbnail: '<?php echo $photo->getThumbURI(); ?>',
						 hits: '<?php echo $photo->hits; ?>',
						 url: '<?php  echo $photo->getImageURI(); ?>',
						 originalUrl: '<?php  echo $photo->getOriginalURI(); ?>',
						 tags: [
						 	<?php foreach($photo->tagged as $tagItem){ ?>
						 	{
							 	id:     <?php echo $tagItem->id;?>,
							 	photoId: <?php echo $photo->id; ?>,
							 	userId: <?php echo $tagItem->userid;?>,
							 	displayName: '<?php echo addslashes($tagItem->user->getDisplayName()); ?>',
							 	profileUrl: '<?php echo CRoute::_('index.php?option=com_community&view=profile&userid='.$tagItem->userid, false);?>',
							 	top: <?php echo $tagItem->posx;?>,
							 	left: <?php echo $tagItem->posy;?>,
							 	width: <?php echo $tagItem->width;?>,
							 	height: <?php echo $tagItem->height;?>,
							 	displayTop: null,
							 	displayLeft: null,
							 	displayWidth: null,
							 	displayHeight: null,
							 	canRemove: <?php echo $tagItem->canRemoveTag;?>
							}
							<?php $end = end($photo->tagged); if($end->id != $tagItem->id) echo ',';?>
						 	<?php } ?>
						 ]
						}
					<?php
						$end	= end( $photos );
						if ($end->id!=$photo->id)
							echo ',';
					}
					?>
					],
			currentPlaylistIndex: null,
			language: {
				COM_COMMUNITY_REMOVE: '<?php echo addslashes(JText::_('COM_COMMUNITY_REMOVE'));?>',
				COM_COMMUNITY_PHOTOS_NO_CAPTIONS_YET: '<?php echo addslashes(JText::_('COM_COMMUNITY_PHOTOS_NO_CAPTIONS_YET'));?>',
				COM_COMMUNITY_SET_PHOTO_AS_DEFAULT_DIALOG: '<?php echo addslashes(JText::_('COM_COMMUNITY_SET_PHOTO_AS_DEFAULT_DIALOG'));?>',
				COM_COMMUNITY_REMOVE_PHOTO_DIALOG: '<?php echo addslashes(JText::_('COM_COMMUNITY_REMOVE_PHOTO_DIALOG'));?>',
				COM_COMMUNITY_SELECT_PERSON: '<?php echo addslashes(JText::_('COM_COMMUNITY_SELECT_PERSON')); ?>',
				COM_COMMUNITY_PHOTO_TAG_NO_FRIEND: '<?php echo addslashes(JText::_('COM_COMMUNITY_PHOTO_TAG_NO_FRIEND')); ?>',
				COM_COMMUNITY_PHOTO_TAG_ALL_TAGGED: '<?php echo addslashes(JText::_('COM_COMMUNITY_PHOTO_TAG_ALL_TAGGED')); ?>',
				COM_COMMUNITY_CONFIRM: '<?php echo addslashes(JText::_('COM_COMMUNITY_CONFIRM')); ?>',
				COM_COMMUNITY_PLEASE_SELECT_A_FRIEND: '<?php echo addslashes(JText::_('COM_COMMUNITY_PLEASE_SELECT_A_FRIEND')); ?>'
			},
			config: {
				defaultTagWidth: <?php echo $config->get('tagboxwidth');?>,
				defaultTagHeight: <?php echo $config->get('tagboxheight');?>
			}
		};			
	</script>
	
	<?php if ($default) { ?>

  	<div class="photoViewport">
		<div class="photoDisplay">
			<img class="photoImage"/>
		</div>
		
		<div class="photo_slider">
			<div id='slider_item' style='width:<?php echo (count($photos) * 79);?>px; position:relative;margin:5px 0 5px 10px;'>
				<?php
				for($i=0; $i < count($photos); $i++ ) 
				{
					$photo	=& $photos[$i];
				?>
				<img src="<?php echo $photo->getThumbURI(); ?>" id="photoSlider_thumb<?php echo $photo->id;?>" width="75px" class="image_thumb" onclick="joms.photos.photoSlider.viewImage(<?php echo $photo->id;?>);">
				<?php
				}
				?>
			</div>
		</div>
	
		<div class="photoActions">
			<div class="photoAction _next" onclick="joms.gallery.displayPhoto(joms.gallery.nextPhoto()); joms.photos.photoSlider.switchPhoto();"><img src="" height="50" alt="" /></div>
			<div class="photoAction _prev" onclick="joms.gallery.displayPhoto(joms.gallery.prevPhoto()); joms.photos.photoSlider.switchPhoto();"><img src="" height="50" alt="" /></div>
		</div>
	
		<div class="photoTags">
			<div class="photoTagActions">
				<button class="photoTagAction _select" onclick="joms.gallery.selectNewPhotoTagFriend();"><?php echo JText::_('COM_COMMUNITY_SELECT_PERSON');?></button>
				<button class="photoTagAction _cancel" onclick="joms.gallery.cancelNewPhotoTag(); cWindowHide();"><?php echo JText::_('COM_COMMUNITY_CANCEL');?></button>
			</div>
		</div>

		<div class="photoLoad"></div> 
		
    	<div class="vidSubmenu clrfix">
	    
    		<ul class="cResetList submenu jsApSbMn">
    			<li><span><?php echo JText::_('COM_COMMUNITY_VIDEOS_HITS') ?> <strong class="photoHitsText" id="photo-hits"><?php echo $default->hits; ?></strong></span></li>
			<?php if( ($isOwner || $isAdmin) && ($photo->storage == 'file') ) { ?>
				<li><a title="<?php echo JText::_('COM_COMMUNITY_PHOTOS_ROTATE_LEFT'); ?>" href="javascript:void(0);"  class="jsApIcn jsPhRotL photoRotaterActions" onclick="joms.gallery.rotatePhoto('left')">-<?php echo JText::_('COM_COMMUNITY_PHOTOS_ROTATE_LEFT'); ?></a></li>
				<li><a title="<?php echo JText::_('COM_COMMUNITY_PHOTOS_ROTATE_RIGHT'); ?>" href="javascript:void(0);" class="jsApIcn jsPhRotR photoRotaterActions" onclick="joms.gallery.rotatePhoto('right')">-<?php echo JText::_('COM_COMMUNITY_PHOTOS_ROTATE_RIGHT'); ?></a></li>
			<?php } ?>
			</ul>
			<div id="like-container"></div>
    	</div>
    	  
	</div>
	
	<?php } 

	$groupid = JRequest::getVar('groupid', '', 'REQUEST');
	if(!empty($groupid))
	{
	?>	
		<div class="uploadedBy" id="uploadedBy">
			<?php echo JText::sprintf('COM_COMMUNITY_UPLOADED_BY', CRoute::_('index.php?option=com_community&view=profile&userid='.$photoCreator->id), $photoCreator->getDisplayName()); ?>
		</div>
	<?php
	}
	?>

	<div class="photoCaption">
           <textarea class="photoCaptionText <?php if( $isOwner || $isAdmin ) { ?>editable<?php } ?>" <?php if(!( $isOwner || $isAdmin )) {?> disabled="disabled" <?php } ?>><?php echo $default->caption;?></textarea>

	</div>
	
	<div class="photoDescription">
		<div class="photoSummary"></div>
		<div class="photoTextTags"><?php echo JText::_('COM_COMMUNITY_PHOTOS_IN_THIS_PHOTO'); ?> </div>
	</div>
	
	<?php if( isset($allowTag) && ($allowTag)) { ?>	
	<div class="photoTagging">
		<a id="startTagMode" href="javascript: void(0);" onclick="joms.gallery.startTagMode();"><?php echo JText::_('COM_COMMUNITY_TAG_THIS_PHOTO'); ?></a>
		
		<div class="photoTagSelectFriend">
			<dl id="system-message" class="js-system-message" style="display:none;">
				<dt class="notice"><?php echo JText::_('COM_COMMUNITY_NOTICE');?></dt>
				<dd class="notice message fade">
					<ul>
						<li><?php echo JText::_('COM_COMMUNITY_PLEASE_SELECT_A_FRIEND'); ?></li>
					</ul>
				</dd>
			</dl>
		
			<label for="photoTagFriendFilter"><?php echo JText::_('COM_COMMUNITY_PHOTO_TAG_TYPE_FRIEND'); ?></label>		
			<div class="photoTagFriendFilters">	
				<input type="text" name="photoTagFriendFilter" class="photoTagFriendFilter" id="friend-search-filter" onkeyup="joms.gallery.filterPhotoTagFriend();"/>
			</div>
			
			<label><?php echo JText::_('COM_COMMUNITY_PHOTO_TAG_CHOOSE_FRIEND'); ?></label>
			<div class="photoTagFriends" id="community-invitation-list">
			<!-- HERE -->			
			</div>
			<div id="community-invitation-loadmore">
			<!-- HERE -->
			</div>
		</div>
		
		<div class="photoTagFriendsActions">
			<button class="photoTagFriendsAction _select">[<?php echo JText::_('COM_COMMUNITY_SELECT_PERSON');?>]</button>
			<button class="photoTagFriendsAction _cancel">[<?php echo JText::_('COM_COMMUNITY_CANCEL');?>]</button>
		</div>

		<div class="photoTagInstructions">
			<?php echo JText::_('COM_COMMUNITY_PHOTO_TAG_INSTRUCTIONS'); ?>
			<button class="photoTagInstructionsAction" onclick="joms.gallery.stopTagMode();"><?php echo JText::_('COM_COMMUNITY_PHOTO_DONE_TAGGING'); ?></button>
		</div>
	</div>
	<?php } ?>

	
	
	
</div>


<?php
	if($photos || $default)
	{
?>
<script type="text/javascript" language="javascript">
if( typeof wallRemove !=='function' )
{
	function wallRemove( id )
	{
		if(confirm('<?php echo JText::_('COM_COMMUNITY_WALL_CONFIRM_REMOVE'); ?>'))
		{
			joms.jQuery('#wall_'+id).fadeOut('normal').remove();
			jax.call('community','photos,ajaxRemoveWall', id );
		}
	}
}

</script>
<?php
if( $showWall )
{
?>
<!-- Load walls for this photo -->
<div id="community-photo-walls-title"><?php echo JText::_('COM_COMMUNITY_COMMENTS');?></div>
<?php
}
?>
<div id="community-photo-walls"></div>
<div id="wallContent"></div>

<script type="text/javascript" language="javascript">
joms.jQuery(document).ready(function(){ 
	joms.gallery.init(); 	
	joms.photos.photoSlider._init("slider_item", "image_thumb");
});
</script>

<?php
	}
}
else
{
?>
	<div id="no-photos"><?php echo JText::_('COM_COMMUNITY_NO_PHOTOS_AVAILABLE_FOR_PREVIEW');?></div>
<?php
}
?>