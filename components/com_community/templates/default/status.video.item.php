<li id="video-<?php echo $video->id; ?>" class="video-item jomNameTips clrfix" title="<?php echo $this->escape( CStringHelper::truncate($video->description , VIDEO_TIPS_LENGTH )); ?>">

    <div class="video-thumb">
		<img src="<?php echo $video->getThumbnail(); ?>" width="<?php echo $videoThumbWidth; ?>" height="<?php echo $videoThumbHeight; ?>" alt="" />
		<span class="video-durationHMS"><?php echo $video->getDurationInHMS(); ?></span>
    </div>

    <div class="video-summary">
		<div class="video-title"><?php echo $video->getTitle(); ?></div>
		<a class="creator-change-video" href="javascript: void;"><?php echo JText::_('COM_COMMUNITY_CHANGE_VIDEO'); ?></a>
		<label class="label title">
			<?php echo JText::_('COM_COMMUNITY_VIDEOS_CATEGORY');?>
		</label>
		<?php echo $categoryHTML; ?>
		<script type="text/javascript">
			function updateCategoryId()
			{
				var catid = joms.jQuery('#category_id').val();
				jax.call('community','videos,ajaxSetVideoCategory', '<?php echo $video->id; ?>', catid );
			}
		</script>
	</div>
</li>