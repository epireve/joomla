<script type="text/javascript">
//<![CDATA[

(function($) {

joms.status.Creator['link'] = 
{
	attachment: [],

	focus: function()
	{
		this.Message.defaultValue("<?php echo JText::_('COM_COMMUNITY_STATUS_LINK_HINT'); ?>", 'hint');
	},

	getAttachment: function()
	{
		return { type: 'link' };
	}
}

})(joms.jQuery);

//]]>
</script>

<div class="creator-view type-link">
	<ul class="creator-content"></ul>

	<div class="creator-form">
		<table class="formtable" cellspacing="1" cellpadding="0">
			<tr>
				<td>
					<label for="linkURL" class="label title">
						*<?php echo JText::_('COM_COMMUNITY_LINK_URL');?>
					</label>
				</td>
				<td class="value">
					<input type="text" id="linkUrl" name="linkUrl" class="inputbox required" value="" />			
				</td>
				<td>
					<button class="button"><?php echo JText::_('COM_COMMUNITY_ADD_LINK'); ?></button>
				</td>
			</tr>
		</table>

		<div class="creator-content-action">
			<a class="icon-add" href=""><?php echo JText::_('COM_COMMUNITY_ADD_PHOTO'); ?></a>
		</div>

		<div class="creator-message">
			<textarea></textarea>
		</div>
	</div>
</div>