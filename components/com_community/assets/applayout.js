joms.extend({
	editLayout: {
		appPositions  : '.app-position',
		appItems      : '.app-item:not(.app-core)',
		
		activate: function( activityId ) {
			var appPositions   = joms.jQuery(this.appPositions);
			var appItems       = joms.jQuery(this.appItems);
			
			// Prevent selection of text
			appItems.attr('onselectstart', 'return false;')
			        .css('-moz-user-select', 'none');
			
			joms.jQuery(appPositions).sortable({
				
				connectWith: appPositions,
				items      : appItems,
				placeholder: 'app-placeholder',
				tolerance  : 'pointer',
				delay      : 100,

				activate: function(event, ui) {
					
					var appPosition = joms.jQuery(this);
					var appItem     = ui.item;
					
					if (appPosition.children('.app-item').length<1)
					{
						appPosition.css('min-height', appItem.outerHeight());
					}
					
					appPosition.addClass('onDrag');

				},
				deactivate: function(event, ui) {
			
					var appPosition = joms.jQuery(this);
					var appItem     = ui.item;
					
					appPosition.css('min-height', 0)
					           .removeClass('onDrag');

				},
				start: function(event, ui) {
					// Cancel any pending saving tasks
					clearTimeout(joms.editLayout.save);

					var appItem  = ui.item;
					joms.jQuery(appItem).addClass('onDrag');
					joms.jQuery('.app-placeholder').css('height', appItem.outerHeight());
				},
				stop: function(event, ui) {
					var appItem = joms.jQuery(ui.item);					
					appItem.removeClass('onDrag');
				},				
				update: function(event, ui){
					var appItem = joms.jQuery(ui.item);
					var appPosition = joms.jQuery(this);
					var appPositions = joms.jQuery(joms.editLayout.appPositions);
					
					appPosition.addClass('onSave');
					appItem.addClass('onSave');
					
					// Build new order list
					// TODO: Maybe we could use .serialize();
					var newOrder = new Array();

					appPositions.each(function(){

						var appPosition = joms.jQuery(this);						
						var appItems = joms.jQuery(this).children(joms.editLayout.appItems);
						appItems.each(function(appOrder){

							// [appId, appPosition, appOrder]
							var order = [
								joms.jQuery(this).attr('id').split('-')[1],
								appPosition.attr('id').split('pos-profile-')[1],
								appOrder+1
							];

							newOrder.push(order.join(','));
						})

					})

					newOrder = newOrder.join('&');

					// Save new order after layout is idle for 1 second
					joms.editLayout.save = setTimeout(function()
					{						
						jax.call('community', 'apps,ajaxSaveOrder', newOrder);
					}, 1000);
				}
			});

		},
		deactivate: function() {
			joms.jQuery( joms.editLayout.appPositions ).sortable('destroy');
		},

		// TODO: Put this back into joms.apps namespace
		browse: function(appPosition) {
			var ajaxCall = 'jax.call("community","apps,ajaxBrowse","' + appPosition + '");';
			cWindowShow(ajaxCall, '', 550, 100);
		},
		
		// TODO: Put this back into joms.apps namespace
		addApp: function(appName, position){
			jax.call('community', 'apps,ajaxAddApp', appName, position);
		},
		
		addAppToLayout: function(position, html){
			var appItem = joms.jQuery(html);
			appItem.appendTo('#pos-profile-'+position).fadeIn();
			
			joms.editLayout.deactivate();
			joms.editLayout.activate();
		},
		
		doneSaving: function() {
			joms.jQuery('.onSave').removeClass('onSave');
		}
	}
});