<script type="text/javascript">
//<![CDATA[

(function($) {

joms.status.Creator['message'] =
{
	focus: function()
	{
		this.Message.defaultValue("<?php echo JText::_('COM_COMMUNITY_STATUS_MESSAGE_HINT'); ?>", 'hint');
	},

	submit: function()
	{
		return !this.Message.hasClass('hint');
	},

	getAttachment: function()
	{
		return { type: 'message' };
	}
};

})(joms.jQuery);

//]]>
</script>

<div class="creator-view type-message"></div>