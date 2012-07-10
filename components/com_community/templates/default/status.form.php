<?php
/**
 * @package		JomSocial
 * @subpackage 	Template 
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 **/
defined('_JEXEC') OR DIE();
?>


<script type="text/javascript">

(function($) {

var Status,
	StatusUI,
	CreatorViews,
	CreatorMessage,
	CreatorPrivacy,
	CreatorLocation,
	CreatorShareButton,
	CreatorLoadingIndicator,
	InitialCreator,
	CurrentCreator,
	ActivityTarget,
	ActivityType,
	ActivityContainer;

joms.extend({
	status:
	{
		Creator : {},

		submitting: false,

		initialize: function(options)
		{
			Status = this;

			StatusUI = $(options.element);

			ActivityTarget 	= options.activityTarget;
			ActivityType 	= options.activityType;
			ActivityList 	= $(options.activityList);

			if (StatusUI.length < 0)
				return;

			CreatorViews = StatusUI.find('.creator-views');
			CreatorMessage = StatusUI.find('.creator-message');	
			CreatorPrivacy = StatusUI.find('[name=creator-privacy]');
			CreatorLocation = StatusUI.find('.creator-location');
			CreatorShareButton = StatusUI.find('.creator-share');
			CreatorLoadingIndicator = StatusUI.find('.creator-loading');
			CreatorUIs = StatusUI.find('.creator:not(.stub)');
			CreatorTextBox = StatusUI.find('textarea.creator-message');
			
			//maximum char for status
			
			var maxChar = '<?php echo $maxStatusChar;?>';
			CreatorTextBox.keyup(function(){
				var content = CreatorTextBox.val();
				if(content.length > maxChar){
					CreatorTextBox.val(content.substr(0 , maxChar));
				}
			});	

			$.each(CreatorUIs, function(i, CreatorUI)
			{
				Creator = Status.create(CreatorUI);
				
				Creator.View
					.appendTo(CreatorViews);

				if (i==0) InitialCreator = Creator;
			});

			CreatorMessage
				.stretchToFit()
				.autogrow({});

			CreatorShareButton.click(function()
			{
				Status.submit();
			});

			InitialCreator.display();

			joms.privacy.init();
		},

		create: function(CreatorUI)
		{			
			var CreatorUI = $(CreatorUI);
			var CreatorView = CreatorUI.find('.creator-view');
			var CreatorType = CreatorUI.attr('type') || CreatorUI[0].getAttribute('type');

			// Expose creator to these references
			Creator = {
				Status: Status,
				StatusUI: StatusUI,
				UI: CreatorUI,
				Type: CreatorType,
				View: CreatorView,
				Message: CreatorMessage,
				Privacy: CreatorPrivacy,
				Location: CreatorLocation,
				ShareButton: CreatorShareButton,
				LoadingIndicator: CreatorLoadingIndicator,
				display: function()
				{
					Status.switchTo(CreatorType);
				}
			};

			Creator = $.extend(Status.Creator[CreatorType], Creator);

			try { Creator.initialize(); } catch (err) {};

			CreatorUI.click(function()
			{
				Status.switchTo(CreatorType);
			});

			return Creator;
		},

		switchTo: function(CreatorType)
		{
			if (Status.submitting)
				return;

			try {

			CurrentCreator.UI
				.removeClass('active');
			
			CurrentCreator.View
				.removeClass('active');

			StatusUI
				.removeClass('on-' + CurrentCreator.Type);

			CurrentCreator.blur();

			} catch(err) {};

			Creator = Status.Creator[CreatorType];

			Creator.UI
				.addClass('active');

			Creator.View
				.addClass('active');

			StatusUI
				.addClass('on-' + Creator.Type);

			try { Creator.focus(); } catch (err) {};

			CurrentCreator = Creator;
		},

		reset: function()
		{
			CreatorMessage.val('').blur();

			$.each(Status.Creator, function(i, Creator)
			{
				try { Creator.reset(); } catch (err) {};
			});

			InitialCreator.display();
		},

		submit: function()
		{
			if (Status.submitting)
				return;

			if (!Creator.submit())
				return;

			var message    = CreatorMessage.hasClass('hint') ? '' : CreatorMessage.val();

			attachment = (CurrentCreator.getAttachment) ? CurrentCreator.getAttachment() : {};

			attachment.privacy 	= Creator.Privacy.find('option:selected').val();
			attachment.target 	= ActivityTarget;
			attachment.element	= ActivityType;
			attachment.filter 	= ActivityList.find('#activity-type').val();

			Status.add(message, attachment);
		},

		add: function(message, attachment, callback)
		{
			message    = $.trim(message);
			attachment = JSON.stringify(attachment);

			joms.ajax.call('system,ajaxStreamAdd', [message, attachment],
			{
				beforeSend: function()
				{
					CreatorLoadingIndicator.show();

					Status.submitting = true;
				},
				success: function()
				{
					if (typeof(callback)=='function')
						callback.apply(this, arguments);
					
					try { CurrentCreator.created.apply(CurrentCreator, arguments) } catch (err) {};

					Status.reset();

					//joms.activities.initMap();
				},
				error: function()
				{
					try { CurrentCreator.error.apply(CurrentCreator, arguments) } catch (err) {};
				},
				complete: function()
				{
					CreatorLoadingIndicator.hide();

					Status.submitting = false;
				}
			});
		}
	}
});

$(document).ready(function()
{
	joms.status.initialize({
		element: '.community-status',
		activityTarget: <?php echo $target; ?>,
		activityType: '<?php echo $type; ?>',
		activityList: '#activity-stream-container'

	});
});

})(joms.jQuery);

</script>

<div class="community-status" style="z-index:10;position:relative">

	<div class="status-author">
		<img src="<?php echo $my->getThumbAvatar(); ?>" />
	</div>

	<div class="status-creator">
		<ul class="creators clrfix">
			<li class="creator stub"><strong><?php echo JText::_('COM_COMMUNITY_SHARE');?></strong></li>

			<?php foreach($creators as $creator) { ?>
				<li class="creator <?php echo $creator->class; ?>" type="<?php echo $creator->type; ?>">
					<a class="creator-menu"><span><?php echo $creator->title; ?></span></a>
					<?php echo $creator->html; ?>
				</li>
			<?php } ?>
		</ul>

		<div class="creator-views">

		</div>

		<div class="creator-message-container">
			<textarea class="creator-message"></textarea>
		</div>		

		<div class="creator-actions clrfix">
			<span class="creator-loading"></span>
			<!-- // Should privacy select box be sensitive to what was the default value?
			// $privacyParams->get('privacyProfileView')
			// $photo->permissions
			// $video->permissions
			 -->
			
			<button class="creator-share button"><?php echo JText::_('COM_COMMUNITY_SHARE');?></button>
			<?php if($type=='profile'): 
				$access =  array( 'public' => true , 'members' => true , 'friends' => true , 'self' => true );
				$userParams		= $my->getParams();
				$profileAccess	= $userParams->get('privacyProfileView');
				$defaultSelect 	= 0;
				switch( $profileAccess ){
					
					case PRIVACY_PRIVATE:
						unset($access['friends']);
						$defaultSelect = PRIVACY_PRIVATE;
					case PRIVACY_FRIENDS:
						unset($access['members']);
						$defaultSelect = PRIVACY_FRIENDS;
					case PRIVACY_MEMBERS:
						unset($access['public']);
						$defaultSelect = PRIVACY_MEMBERS;
					case 0:
					case 10:
						break;
					
				}
				?>
			<div class="js_PriCell"><?php echo CPrivacy::getHTML('creator-privacy', $defaultSelect, 'COMMUNITY_PRIVACY_BUTTON_SMALL', $access ); ?></div>
			<?php endif; ?>
		</div>
            
            <div class="clr"></div>
            
	</div>

</div>
