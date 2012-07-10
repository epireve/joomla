<li id="photo-<?php echo $photo->id; ?>">
	<img src="<?php echo JURI::base().$photo->thumbnail; ?>" alt="" />
	<a class="creator-change-photo" href="javascript: void(0);"><?php echo JText::_('COM_COMMUNITY_PHOTOS_CHANGE'); ?></a>
</li>