<?php
 /**
  * @version   $Id: default.php 41647 2011-08-30 21:56:36Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
?>
<div id="rg-<?php echo $passed_params->moduleid; ?>" class="rokgallery-wrapper">
	<div class="rg-ss-container">
		<div class="rg-ss-slice-container" style="width:<?php echo $passed_params->image_width;?>px;height:<?php echo $passed_params->image_height;?>px;">
			<ul class="rg-ss-slice-list">
	            <?php $i=1; foreach ($passed_params->slices as $slice):
	            $slice_title = ($slice->title)?$slice->title:'';
	            $slice_caption = ($slice->caption)?$slice->caption:'';?>
				<li>
		            <div class="rg-ss-slice">  
		            	<?php if ($passed_params->link!='none'):?>
		                	<a <?php echo $slice->rel;?> href="<?php echo $slice->link;?>">
		                <?php endif;?>
		            		<img title="" alt="<?php echo $slice_title;?>" src="<?php echo $slice->imageurl;?>" width="<?php echo $passed_params->image_width;?>" height="<?php echo $passed_params->image_height;?>"/>
		                <?php if ($passed_params->link!='none'):?>
		            		</a>
		            	<?php endif;?>
		            </div>
		            <?php if (($passed_params->title)||($passed_params->caption)):?>
		            <div class="rg-ss-info">
		                <?php if ($passed_params->title):?>
		                	<?php
		                		// span last word
		                		$last_word = array_pop(str_word_count($slice_title, 2));
		                		$position = array_pop(array_keys(str_word_count($slice_title, 2)));

		                		$slice_title = '<span class="title-1">' . substr_replace($slice_title, '</span><span class="title-2">'.$last_word.'</span>', $position, strlen($last_word));
		                	?>
		                	<span class="rg-ss-title"><?php echo $slice_title;?></span>
		                <?php endif;?>
		                <?php if ($passed_params->caption):?>
		            		<span class="rg-ss-caption"><?php echo $slice_caption;?></span>
		            	<?php endif;?>
		            </div>
		            <?php endif;?>
	            </li>
				<?php $i++;?>
	        	<?php endforeach; ?> 
			</ul>
			<?php if ($passed_params->arrows!='no'):?>
			<div class="rg-ss-controls <?php if ($passed_params->arrows=='onhover'):?>onhover<?php endif; ?>">
				<span class="next"></span>
				<span class="prev"></span>
			</div>
			<?php endif;?>
			<?php if ($passed_params->autoplay_enabled == 2): ?>
			<div class="rg-ss-loader">
				<div class="rg-ss-progress"></div>
			</div>
			<?php endif; ?>
		</div>
		<?php if ($passed_params->navigation=='thumbnails'):?>
		<div class="rg-ss-navigation-container arrows-enabled" style="width:<?php echo $passed_params->image_width;?>px;">
			<div class="rg-ss-scroller-container">
				<div class="rg-ss-thumb-scroller">
					<ul class="rg-ss-thumb-list">
						<?php $i=1; foreach ($passed_params->slices as $slice):?>
							<li>
							<?php if ($passed_params->navigation=='thumbnails'):?>
				        	<div class="rg-ss-thumb">
				        		<img title="<?php echo $slice->title;?>" alt="<?php echo $slice->title;?>" src="<?php echo $slice->thumburl;?>" width="<?php echo $passed_params->thumb_width;?>" height="<?php echo $passed_params->thumb_height;?>" />
							</div>
							<?php endif; $i++;?>
							</li>
						<?php endforeach;?>
					</ul>
				</div>
			</div>
			<div class="rg-ss-arrow-left"><span></span></div>
			<div class="rg-ss-arrow-right"><span></span></div>
		</div>
		<?php endif;?>
	</div>
</div>