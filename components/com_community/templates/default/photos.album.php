<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 * 
 * @param	applications	An array of applications object
 * @param	pagination		JPagination object 
 */
defined('_JEXEC') or die();
?>
<?php
if( $photos && $isOwner )
{
?>
<script type="text/javascript" src="<?php echo rtrim(JURI::root(),'/'); ?>/components/com_community/assets/ui.core.js"></script>
<script type="text/javascript" src="<?php echo rtrim(JURI::root(),'/'); ?>/components/com_community/assets/ui.sortable.js"></script>
<script type='text/javascript'>
joms.jQuery(document).ready(function(){
	joms.jQuery('#community-photo-items').sortable({
		cursor: 'move',
		start: function(event, ui) {
			ui.item.addClass('onDrag');
		},
		stop: function(event, ui) {
			//@rule: Reset the ordering so the next drag will not mess up
			var i = 0;
			joms.jQuery( '#community-photo-items' ).children().each( function(){
				joms.jQuery( this ).attr('id' , 'photo-' + i);
				i++;
			});
			ui.item.removeClass('onDrag');

			// Update all existing ordering.
			var items	= [];
			joms.jQuery( '#community-photo-items img' ).each( function(){
				var photoid	= joms.jQuery(this).attr('id').split('-');
				items.push('app-list[]=' + photoid[1] );
				i++;
			});
			
			// Hide action
			jax.call('community', 'photos,ajaxSaveOrdering', items.join('&') , joms.jQuery('#albumid').val() );
		}
	});
});
</script>
<?php
}
?>
<script type='text/javascript'>
// Not required in this feature page
// Script below separate from top as applies to view on own and others albums
joms.jQuery(document).ready(function(){
	joms.jQuery('.cMapLoc').remove();
});

</script>


<div class="page-actions">
  <?php echo $bookmarksHTML;?>
  <div class="clr"></div>
</div>

<input type="hidden" name="albumid" value="<?php echo $album->id;?>" id="albumid" />
<div id="photo-album" class="cLayout clrfix">
	<div class="cSidebar clrfix">
		<!-- Album Thumbnail and Details section -->
		<div class="community-album-details album-details">
			<img src="<?php echo $album->thumbnail;?>" /><br/>
			<p>
				<?php echo JText::_('COM_COMMUNITY_BY').' '.$owner->getDisplayName();?>
				<?php echo ' . '.JText::sprintf('COM_COMMUNITY_PHOTOS_ALBUM_LAST_UPDATED', $album->lastUpdated);?>
				<?php if (!empty($album->location)): ?>
				<?php echo ' . '.JText::sprintf('COM_COMMUNITY_PHOTOS_ALBUM_TAKEN_AT_DESC', '<a class="album-map-link" href="javascript:void(0);" onclick="joms.jQuery(\'#album-map\').toggle();">'.$album->location.'</a>');?><br/>
				<br/>
				<?php endif ?>
			</p>		
		</div><!--#community-album-details-->
		<div id="album-map" <?php if($photosmapdefault==0){ ?>style="display:none"<?php } ?>>
			<?php echo $zoomableMap;?>
		</div>
		<br/>
		
		<!-- Tagged Section -->
		<?php if ($people): ?>
		<div class="community-album-people clrfix">
			<strong><?php echo JText::_('COM_COMMUNITY_PHOTOS_IN_THIS_ALBUM'); ?></strong>
			<div>
				<?php $totalpeople = sizeof($people); $count = 1; 
				foreach($people as $peep):?>
					<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $peep->id); ?>" rel="nofollow"><?php echo $peep->getDisplayName(); ?><?php if($count<$totalpeople){ echo ","; } ?></a>
				<?php 
				$count++;
				endforeach; 
				?>
			</div>
		</div>
		<?php endif; ?>
		<!-- End Tagged Section -->
		
		<!-- Other Album Section -->
		<?php 
		if (!empty($otherAlbums)) {
		?>
		<div id="other-albums-label" class="ctitle">
			<h3><?php echo JText::_('COM_COMMUNITY_PHOTOS_OTHER_ALBUMS');?></h3>
		</div>
		<div id="other-albums-container">
			<ul>
				<?php 
				foreach($otherAlbums as $others) { ?>
				<li>
					<div class="album-thumbs">
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=album&albumid=' . $others->id . '&userid=' . $others->creator); ?>">
					   		<img class="cAvatar" src="<?php echo $others->thumbnail; ?>" alt="<?php echo $this->escape($others->name);?>" data="album_prop_<?php echo rand(0,200).'_'.$others->id;?>" width="50" height="50"/>
						</a>
					</div><!--.album-thumbs-->
					<div class="album-meta">
						<div class="album-name"><a href="<?php echo CRoute::_('index.php?option=com_community&view=photos&task=album&albumid=' . $others->id . '&userid=' . $others->creator); ?>"><?php echo $this->escape($others->name); ?></a></div>
						<div class="album-count">
							<?php if(CStringHelper::isPlural($others->count)) {
								echo JText::sprintf('COM_COMMUNITY_PHOTOS_COUNT', $others->count );
								} else {
								echo JText::sprintf('COM_COMMUNITY_PHOTOS_COUNT_SINGULAR', $others->count );
								} ?>
						</div>
					</div>
					<div class="clr"></div>
				</li>
				<?php } //end foreach ?>
			</ul>
			
		</div>
		<?php } //end if ?>
	</div> 
	
	<div class="cMain clrfix">
		<!-- Photo Thumbnail section -->
		<div id="community-photo-items" class="photo-list-item">
			<?php
			if($photos)
			{	
				for( $i=0; $i<count($photos); $i++ )
				{
					$row =& $photos[$i];
			?>
				<div class="photo-item" id="photo-<?php echo $i;?>" title="<?php echo $this->escape($row->caption);?>">
					<a href="<?php echo $row->link;?>"><img src="<?php echo $row->getThumbURI();?>" alt="<?php echo $this->escape($row->caption);?>" id="photoid-<?php echo $row->id;?>" /></a>
					<?php
					if( $isOwner )
					{
					?>
					<div class="photo-action">
						<a href="javascript:void(0);" title="<?php echo JText::_('COM_COMMUNITY_REMOVE');?>" onclick="joms.gallery.confirmRemovePhoto('<?php echo $row->id;?>');" class="remove"><?php echo JText::_('COM_COMMUNITY_REMOVE');?></a>
					</div>
					<?php } ?>
				</div>
		<?php
			}
		}
		else { ?>
				<div class="community-empty-list"><?php echo JText::_('COM_COMMUNITY_PHOTOS_NO_PHOTOS_UPLOADED');?></div>
		<?php } ?>
		
		</div>
		<div class="pagination-container">			
			<?php echo $pagination->getPagesLinks(); ?>
		</div>
		<!-- Like Section -->
		<div id="like-container" style="margin-top: 14px"><?php echo $likesHTML; ?></div>
		<!-- Like Section -->
		
		<!-- Photo Description Section -->
		<?php
		if( ( $isOwner || $isAdmin ) || !empty($album->description) )
		{
		?>
		<div class="community-photo-desc">
			<strong><?php echo JText::_('COM_COMMUNITY_PHOTOS_ALBUM_DESC');?></strong><br />
			<textarea class="community-photo-desc-editable <?php echo ( $isOwner || $isAdmin ) ? 'editable' : '';?>" <?php echo ( $isOwner || $isAdmin ) ? '' : 'readonly disabled="disabled"';?> style="border:medium none; resize:none;"><?php echo (($isOwner || $isAdmin) && empty($album->description)) ? JText::_('COM_COMMUNITY_PHOTOS_SHOW_EDITOR') : $this->escape($album->description); ?></textarea>
		</div>
		<?php
		}
		?>
		
		<!-- Wall Comment Section -->
		<?php if(count($photos) > 0) : ?>
		<div class="album-wall">
			<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_COMMENTS') ?></div>
			<div id="wallForm"><?php echo $wallForm; ?></div>
			<div id="wallContent"><?php echo $wallContent; ?></div>
		</div>
		<?php endif; ?>
		
	</div>
	<!--.cMain-->
	
	<div class="clr"></div>
</div><!--.cLayout-->
<style type="text/css">
	
.community-photo-desc .community-photo-desc-editable{
	background: url("pencil.png") no-repeat scroll left center transparent;
	padding: 0 0 0 15px;
	position: relative;
}

.community-photo-desc-editable.editing {
	border: 1px inset #CCCCCC !important; 
	text-decoration: none;
}
</style>
<script type="text/javascript">
	joms.jQuery(document).ready(function() {
		var photoAlbumDesc = joms.jQuery('.community-photo-desc-editable');
		

		if (photoAlbumDesc.hasClass('editable'))
		{
			photoAlbumDesc
				.stretchToFit()
				.autogrow({lineHeight: 0, minHeight: 0})
				.focus(function()
				 {
					photoAlbumDesc
						.addClass('editing')
						.stretchToFit()
						.data('oldPhotoCaption', photoAlbumDesc.val());
						
					if ( photoAlbumDesc.val() == '<?php echo JText::_('COM_COMMUNITY_PHOTOS_SHOW_EDITOR');?>')
					{
						photoAlbumDesc.val('');
					}
				 })
				.blur(function()
				 {
					photoAlbumDesc
						.removeClass('editing')
						.stretchToFit();

					var oldPhotoCaption = joms.jQuery.trim(photoAlbumDesc.data('oldPhotoCaption'));
					var newPhotoCaption = joms.jQuery.trim(photoAlbumDesc.val());

					if (newPhotoCaption=='' || newPhotoCaption==oldPhotoCaption)
					{
						photoAlbumDesc
							.val(oldPhotoCaption)
							.trigger('autogrow');
						return;
					}

					jax.call('community', 'photos,ajaxSaveAlbumDesc', joms.jQuery('#albumid').val(), newPhotoCaption);
				 });
		}
	});
</script>