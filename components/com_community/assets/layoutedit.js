joms.extend({
	editLayout: {
		sortElements: '#apps-sortable, #apps-sortable-side-top, #apps-sortable-side-bottom',
		start: function( activityId ){
			
			joms.jQuery('.page-action#editLayout-start').hide();
			joms.jQuery('.page-action#editLayout-stop').show();
			
			// Hide the activity stream for a while
			joms.jQuery('#activity-stream-container').hide();
						
			// IF connected sortable height is less than certain value, fix it
			// cannot applied to all since it seems to messed up some smaller div
			joms.jQuery('.connectedSortable').each(function(index, element){
				if(joms.jQuery(element).height() < 10)
					joms.jQuery(element).css('min-height', 64);
			});
			
			// add move cursor to moveabale object header
			joms.jQuery('div.connectedSortable > div:not(.app-core) > div.app-box-header').css('cursor', 'move');
			joms.jQuery('div.connectedSortable > div:not(.app-core)  div.app-widget-header').css('cursor', 'move');
			
			try{ console.log('start editable layout'); } catch(err){}
			joms.jQuery( joms.editLayout.sortElements ).sortable({
				cursor: 'move',
				connectWith: '.connectedSortable',
				placeholder: 'dragPlaceholder',
				items: '> div:not(.app-core)',
				start: function(event, ui) {
					
					// Maybe we can have an extra class called .app-title instead.
					var appTitle  = ui.item.find('.app-box-title').html() || ui.item.find('.app-widget-title').html();

					ui.item.addClass('onDrag')
							.prepend('<div class="dragOverlay"><strong>'+appTitle+'</strong></div>');
			
					// the placeholder size must match content size
					joms.jQuery('div.dragPlaceholder').height(ui.item.height() - 10);
					joms.jQuery('div.dragOverlay').height(ui.item.innerHeight());
					joms.jQuery('div.dragOverlay').width(ui.item.innerWidth());
					
					// Hide  main content for widgets
					ui.item.find('.app-widget-header').hide();
					ui.item.find('.app-widget-content').hide();
					
					ui.item.find('.app-box-header').hide();
					ui.item.find('.app-box-content').hide();
					
					// Save previous position
					ui.item.data('previousPosition', ui.item.parent('.connectedSortable').attr('id'));
					
				},
				stop: function(event, ui) {					
											
					// This determines whether the app has been dragged to a different position
					if (ui.item.data('previousPosition')!=ui.item.parent('.connectedSortable').attr('id'))
					{
						joms.jQuery(ui.item).html('<div class="ajax-wait" style="background-repeat:no-repeat; width:100%; background-position: center center;">&nbsp;</div>');					
						var currentApp = jQuery(ui.item).attr('id').split('-');
						
						jax.call('community', 'apps,ajaxRefreshLayout', currentApp[1], ui.item.parent('.connectedSortable').attr('id'));
					}
					
					// add move cursor to moveabale object header
					joms.jQuery('div.connectedSortable > div:not(.app-core) > div.app-box-header').css('cursor', 'move');
					joms.jQuery('div.connectedSortable > div:not(.app-core)  div.app-widget-header').css('cursor', 'move');
					
					
					var inputs = [];
					var val = [];
					
					ui.item.removeClass('onDrag');
					
					// Remove the overlay
					joms.jQuery('div.dragOverlay').remove();
					
					// Store
					joms.jQuery('#apps-sortable .app-box').each( function() {				
						var appid = joms.jQuery(this).attr('id').split('-');
						inputs.push('app-list[]=' + appid[1]);
					});

					// Show  main content for widgets
					ui.item.find('.app-widget-header').show();
					ui.item.find('.app-widget-content').show();
					
					ui.item.find('.app-box-header').show();
					ui.item.find('.app-box-content').show();
					
					ui.item.removeClass('onNoDrag');

				},
				over: function(event, ui) {
					ui.item.removeClass('onNoDrag');
				},
				out: function(event, ui) 
				{
					ui.item.addClass('onNoDrag');
				}

			});

		},
		stop: function( activityId , content ){
			
			joms.jQuery('.page-action#editLayout-start').show();
			joms.jQuery('.page-action#editLayout-stop').hide();
			
			// Show the activity stream back			
			joms.jQuery('#activity-stream-container').show();
			
			try{ console.log('stop editable layout'); } catch(err){}
			
			// Disable drag&drop
			joms.jQuery( joms.editLayout.sortElements ).sortable('destroy');
			
			// Disable drag cursor
			joms.jQuery('div.connectedSortable > div:not(.app-core) > div.app-box-header').css('cursor', 'auto');
			joms.jQuery('div.connectedSortable > div:not(.app-core) div.app-widget-header').css('cursor', 'auto');
						
			joms.editLayout.save('content'			, '#apps-sortable');
			joms.editLayout.save('sidebar-top'		, '#apps-sortable-side-top');
			joms.editLayout.save('sidebar-bottom'	, '#apps-sortable-side-bottom');
			
			// Restore min height, css
			joms.jQuery('.connectedSortable').each(function(index, element){
				joms.jQuery(element).css('min-height', 'auto');
			});
			
		},
		
		updateApp: function( appid , position ){
			
		},
		
		save: function( position , containerId ){
			// Go through all the list and save them
			var items = [];
			
			// Start with the main content
			joms.jQuery(containerId).children().each(function() {				
				var appid = joms.jQuery(this).attr('id').split('-');
				items.push('app-list[]=' + appid[1]);
			});
			jax.call('community', 'apps,ajaxSavePosition', position, items.join('&'));
		}
	}
});