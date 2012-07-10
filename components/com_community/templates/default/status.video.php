<script type="text/javascript">
//<![CDATA[
(function($) {
var Creator;
joms.status.Creator['video'] = 
{
	attachment: {},
	initialize: function()
	{
		Creator = this;
		Creator.Preview = Creator.View.find('.creator-preview');
		Creator.Form = Creator.View.find('.creator-form');
		Creator.Form
			.submit(function()
			{
				Creator.add();
				return false;
			});
		Creator.VideoUrl = Creator.View.find('.creator-video-url');
		Creator.VideoUrl
			.defaultValue("<?php echo JText::_('COM_COMMUNITY_VIDEOS_ENTER_LINK_TIPS'); ?>", 'hint');
		Creator.Hint = Creator.View.find('.creator-hint');
	},

	focus: function()
	{
		this.Message.defaultValue("<?php echo JText::_('COM_COMMUNITY_STATUS_VIDEO_HINT'); ?>", 'hint');
	},

	add: function()
	{
		var videoUrl = Creator.VideoUrl.val();
		joms.ajax.call('videos,ajaxLinkVideoPreview', [videoUrl], {
			beforeSend: function()
			{				
				Creator.LoadingIndicator.show();				
			},
			success: function(video, html)
			{
				Creator.VideoUrl.val('');
				Creator.Hint.hide();
				Creator.Form.hide();
				video.preview = $(html);
				video.preview
					.find('.creator-change-video')
					.click(function()
					{
						Creator.remove();
					});

				Creator.Preview.append(video.preview);
				Creator.attachment = video;
				joms.tooltip.setup();
			},
			error: function(message)
			{
				if ($.trim(message).length>0)
				{
					Creator.Hint
						.html(message)
						.show()
						.fadeOut(5000);
				}
			},
			complete: function()
			{
				Creator.LoadingIndicator.hide();
			}
		});
	},

	remove: function()
	{
		Creator.attachment.preview.remove();
		Creator.attachment = {};
		Creator.Form.show();
	},

	reset: function()
	{
		Creator.remove();
	},

	submit: function()
	{
		return Creator.attachment.id!=undefined;
	},

	error: function(message)
	{
		Creator.Hint
			.html(message);
	},

	getAttachment: function()
	{
		var attachment = {
			type: 'video',
			id:  Creator.attachment.id
		}
		return attachment;
	}
};
})(joms.jQuery);
//]]>
</script>

<div class="creator-view type-video">
	<div class="creator-hint"></div>
	<form class="creator-form">
		<input type="text" name="videoUrl" class="creator-video-url inputbox" value="" size="36" />
		<input type="submit" class="button" value="<?php echo JText::_('COM_COMMUNITY_VIDEOS_ADD'); ?>" />
		<span class="hint"></span>
	</form>
	<ul class="creator-preview"></ul>
</div>