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

<script>
	joms.jQuery(document).ready(function(){
		var random_feature = '<?php echo rand(0,count($featuredList) -1) ?>';
		joms.jQuery('div.alb_'+random_feature).show();
		
		//store all the featured album div in an array
		var album_item = [];
		joms.jQuery('div.cFeaturedAlbum').each(function(i){
			album_item[i] = this;
			var extra= '';
			var extra2= 'grey';//remove this later
			//add desired class to the selected nav button base on the randomed result
			if(i == random_feature){
				extra = 'nav-selected';
				extra2 = 'green';
			}
			
			//add the navi button if there is more than one featured album
			<?php if(count($featuredList) > 1) : ?>
			joms.jQuery('div.nav-container').append('<div class="nav-button '+extra+'" id="'+i+'" style="background-color:'+extra2+';"></div>');
			<?php endif; ?>
		});
		
		//highlight the current selected navi button
		
		<?php if(count($featuredList) > 1) : ?>
		joms.jQuery('.nav-button').click(function(){
			var id = joms.jQuery(this).attr('id');
			//hide all featured item before displaying the clicked one
			joms.jQuery('div.cFeaturedAlbum').hide();
			joms.jQuery('div.nav-button').removeClass('nav-selected');
			joms.jQuery('div.nav-button').css('background-color','grey'); //remove this later
			
			joms.jQuery(this).addClass('nav-selected');
			joms.jQuery(this).css('background-color','green');//remove this later
			joms.jQuery(album_item[id]).show();
		});
		
		joms.jQuery('div.next-album').click(function(){
			var top_container = joms.jQuery(this).parent().parent().parent();
			var next_container = top_container.next();
			if(next_container.attr('class') == 'clr'){
				next_container = joms.jQuery('.cFeaturedAlbum').first();
			}
			
			//to navigate the nav button as well
			var albumclass = next_container.attr('class').split(' ').slice(-1).toString().split('_').slice(-1);
			joms.jQuery('.nav-button').css('background-color','grey');
			joms.jQuery('.nav-button#'+albumclass).css('background-color','green');
			
			top_container.hide();
			next_container.show();
		});
		<?php endif; ?>
		
		joms.jQuery('div.previous-album').click(function(){
			var top_container = joms.jQuery(this).parent().parent().parent();
			var prev_container = top_container.prev();
			if(prev_container.attr('class') == undefined){
				prev_container = joms.jQuery('.cFeaturedAlbum').last();
			}
			
			var albumclass = prev_container.attr('class').split(' ').slice(-1).toString().split('_').slice(-1);
			joms.jQuery('.nav-button').css('background-color','grey');
			joms.jQuery('.nav-button#'+albumclass).css('background-color','green');
			
			top_container.hide();
			prev_container.show();
		});
		
		joms.jQuery('a.album-map-link').click(function() {
			if (joms.jQuery('div.cFeatured-Map').css('display') == 'none')
			{
				joms.jQuery('div.cFeatured-Map').css('display', 'block');
			}
			else
			{
				joms.jQuery('div.cFeatured-Map').css('display', 'none');
			}
		});
	});
</script>
<?php

	if($featuredList)://display only if there is featured list
?>
<div class="ctitle featuredTitle"><?php echo JText::_('COM_COMMUNITY_FEATURED_ALBUMS');?></div>
<div id="cFeatured" class="listBy4 cPhotos">
<?php
	$x = 1;
	$album_count = 0;
	foreach($featuredList as $album){
?>
<div class="cFeaturedAlbum alb_<?php echo $album_count; ?>" style="display:none;">
	<div class="cBoxPad cBoxBorder">
		<div class="community-album-details album">
			<?php if (count($featuredList) > 1): ?>
			<!-- prev -->
			<div class="previous-album">
				&laquo; <?php echo JText::_('COM_COMMUNITY_PHOTOS_PREV_ALBUM'); ?>
			</div>
			<!-- next -->
			<div class="next-album">
				<?php echo JText::_('COM_COMMUNITY_PHOTOS_NEXT_ALBUM'); ?> &raquo;
			</div>
			<?php endif; ?>
			
			<!-- album covers -->
			<a class="cFeaturedCover" href="<?php echo CRoute::_($album->getURI()); ?>">
				<img src="<?php echo $album->getRawCoverThumbPath();?>" alt="<?php echo $this->escape($album->name); ?>"  data="album_prop_<?php echo rand(0,200).'_'.$album->id;?>"/>
				<span class="cFeaturedOverlay"><?php echo JText::_('COM_COMMUNITY_STAR'); ?></span>
				
				<!--album-actions-->
				<?php if( $isCommunityAdmin ){?>
				<div class="album-actions">
					<a class="album-action remove-featured" title="<?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?>" onclick="joms.featured.remove('<?php echo $album->id;?>','photos');" href="javascript:void(0);"><?php echo JText::_('COM_COMMUNITY_REMOVE_FEATURED'); ?></a>
				</div>
				<?php } ?>
			</a>
			
			<!-- album name/title -->	
			<div class="cFeaturedTitle"><a href="<?php echo CRoute::_($album->getURI()); ?>"><?php echo $this->escape($album->name);?></a></div>
			
			<!-- album desc -->
			<p class="cFeaturedMeta">
				<?php echo JText::_('COM_COMMUNITY_BY').' '.CFactory::getUser($album->creator)->getDisplayName();?>
				<?php echo ' . '.JText::sprintf('COM_COMMUNITY_PHOTOS_ALBUM_LAST_UPDATED', $album->lastUpdated);?>
				<?php if (!empty($album->location)): ?>
				<?php echo ' . '.JText::sprintf('COM_COMMUNITY_PHOTOS_ALBUM_TAKEN_AT_DESC', '<a class="album-map-link" href="javascript:void(0);">'.$album->location.'</a>');?>
				<div class="cFeatured-Map" style="display:none">
					<?php echo $album->zoomableMap;?>
				</div>
				<?php endif ?>
			</p>	
			
			
			
			<!-- description for the album -->
			<div class="cFeaturedDesc">
				<?php echo $album->description;?>
			</div>
			
			<!-- tagged person -->
			<?php if ($album->tagged): ?>
			<div class="cFeaturedTagged">
				<strong><?php echo JText::_('COM_COMMUNITY_PHOTOS_IN_THIS_ALBUM'); ?></strong>
				<div>
					<?php 
					$totalpeople = sizeof($album->tagged); 
					$count = 1; 
					foreach($album->tagged as $ppl):
						
						//max tagged = 5
						if($count > 5){
							break;
						}
					?>
						<a href="<?php echo CRoute::_('index.php?option=com_community&view=profile&userid=' . $ppl->id); ?>">
							<?php 
								echo $ppl->getDisplayName(); 
								if($count < $totalpeople){ echo ","; } 
							?>
						</a>
					<?php 
					$count++;
					endforeach; 
					?>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Photos from the album -->
			<div id="community-photo-items" class="photo-list-item">
				<p><strong><?php echo JText::_('COM_COMMUNITY_PHOTOS_IMAGES_FROM_ALBUM');?>:</strong></p>
				<?php 
					$photos = $album->photos;
				
					for($i=0; $i<count($photos); $i++) {
						$row =& $photos[$i];
				?>
				<div class="photo-item" id="photo-<?php echo $i;?>" title="<?php echo $this->escape($row->caption);?>">
					<a href="<?php echo $row->link;?>"><img src="<?php echo $row->getThumbURI();?>" id="photoid-<?php echo $row->id;?>" /></a>
				</div>

				<?php } ?>
			</div>
			<br class="clr" \>
		</div>	
	</div>
</div>
<?php
		$x++;
		$album_count++;
	} // end foreach
	
?>
	<div class="clr"></div>
	
	<!-- navigation container -->
	<div class="nav-container">
	</div>
</div>
<br/>
<?php endif; ?>
<!-- end #cFeatured -->
	
<div class="ctitle"><?php echo JText::_('COM_COMMUNITY_PHOTOS_PHOTO_ALBUMS');?></div>