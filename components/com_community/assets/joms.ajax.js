/*
=========
joms.ajax
=========

joms.ajax = jax + jQuery.ajax().
Jax is like the protocol. jQuery.ajax() is like the transport.

Process flow:
Jax wraps data.
  |
jQuery.ajax() sends the data to server.
  |
Server returns jax responses.
  |
jQuery.ajax() delegate jax responses.
  |
jax.processResponse() executes jax response.
  |
jQuery.ajax() extract callback data
embedded within jax responses.
  |
jQuery.ajax() pass the callback data to
error, success & complete handler.

This is done without modifying neither jax or jQuery.ajax() library.
So existing jax.call() can execute side-by-side without problems.

============================
How to use joms.ajax.call()?
============================

joms.ajax.call(sFunc, sArgs, callback)
joms.ajax.call('controller,function', [arg1, arg2], function(data) {
	// What to do when the ajax call is successful
});

joms.ajax.call(sFunc, sArgs, settings)
joms.ajax.call('controller,function', [arg1, arg2] {
	success: function(data) {
		// What to do when the ajax call is successful
	},
	error: function(data) {
		// What to do when the ajax call is not successful
		// e.g. timeout, 404, 500, etc.
	},

	// For more settings to fiddle with, look up jQuery.ajax() docs.
});


===========================================
Advantage of joms.ajax() with example usage
===========================================

1. Ability to use PHP associative array as JSON as callback data, e.g.

	// ---- PHP side --- //
	$callbackData['url'] = 'http://www.jomsocial.com'
	$callbackData['caption'] = 'JomSocial'
		
	$objResponse->addScriptCall('__callback', $callbackData);
	
	// ---- JS side --- //
	joms.ajax.call('controller,function', [arg1, arg2], function(data)
	{
		console.log(data.url);     // output http://www.jomsocial.com
		console.log(data.caption); // output JomSocial
	});	
	
2. Ability to handle server timeouts or other errors, e.g.

	joms.ajax.call('apps,ajaxSetOrder', [appOrder], {
		success: function(data) {
			// Remove saving indicator
			jQuery('#app-position').removeClass('onSave');
		},
		error: function(data) {
			// Revert app ordering
			jQuery('#app-position').sortable('cancel');
		}
	});

3. Continue writing relevant codes within the same function in JS,
   instead of writing them as addScriptCall() in PHP, while
   preserving the function/variable scope those relevant codes
   could access and reuse, e.g.

	function removePhoto()
	{
		var photo   = jQuery('#photo-63');
		var photoId = photo.attr('id').split('-')[1];
		
		joms.ajax.call('photos,ajaxRemovePhoto', [photoId], function(data)
		{
			// Now I can use back the 'photo' variable
			// $objResponse->addScriptCall('jQuery("#photo-' + $photoId + ')').remove');
	
			jQuery(photo).remove();
		});
	}
*/

joms.extend({
	ajax: {
		execute: function(o)
		{
			// Rearrange jQuery.ajax callback order.
			// jQuery.ajax : beforeSend -> dataFilter -> success/error -> complete
			// joms.ajax   : beforeSend -> beforeDataFilter -> dataFilter -> afterDataFilter -> fail/success/error -> complete
			//
			// joms.ajax callback handlers
			// - beforeSend: function(xhr)
			// - fail      : function(xhr, status, e)
			// - beforeDataFilter: function(jaxData)
			// - afterDataFilter : function(jomsData)
			// - success   : function(arg1, arg2...)  // from $objResponse->addScriptCall('__callback', $arg1, $arg2);
			// - error     : function(arg1, arg2...)  // from $objResponse->addScriptCall('__throwError', $arg1, $arg2);
			// - complete  : function(xhr, status)
			var settings = {};
			joms.jQuery.extend(settings, o);			

			settings.error = function()
			{
				if (o.fail) o.fail.apply(this, arguments);
			}

			settings.dataFilter = function(jaxData, type)
			{
				if (o.beforeDataFilter) o.beforeDataFilter(jaxData);

				// Delegate jax commands to jax.processReponse()
				jax.processResponse(jaxData);
				
				// Extract callback data embedded inside jaxData
				var jomsData = {
					success: joms.ajax.extractJomsData(jaxData, '__callback'),
					error  : joms.ajax.extractJomsData(jaxData, '__throwError')
				}
				
				if (o.afterDataFilter) o.afterDataFilter(jomsData);

				return jomsData;
			}

			settings.success = function(jomsData, status)
			{
				if (jomsData.error.length>0 && o.error)
				{
					joms.jQuery.each(jomsData.error, function(i, args)
					{
						o.error.apply(this, args);
					});
					return;
				}

				if (jomsData.success.length>0 && o.success)
				{
					joms.jQuery.each(jomsData.success, function(i, args)
					{
						o.success.apply(this, args);
					});
				}
			}

			// Create a dummy __callback function so we don't have
			// waste computing cycles trying to filter them out in
			// filterJaxResponse();
			window.__callback = window.__throwError = function() {};
			
			// Use jQuery.ajax() (replacing the not-so-fun jax.submitTask())
			joms.jQuery.ajax(settings);
		},

		/*
		 * joms.ajax.call()
		 *
		 * @param sFunc {String} The server function to call in the format of "controller,function"
		 * @param sArgs {Array} The arguments to pass to the server function
		 * @param o {Function/Object} If passed as a function, it is treated as callback function
		 *                            that gets executed by 'success' event.
		 *                            If passed as an object, it is treated as ajax settings.
		 */
		call: function(sFunc, sArgs, o)
		{
			var settings = {
				url: jax_live_site,
				type: 'POST',
				data: {
					option : 'community',
					task   : 'azrul_ajax',
					no_html: 1,
					func   : sFunc
				},
				dataType: 'text'
			};
			
			// To fix CSRF issues.
			settings.data[ jax_token_var ]	= 1;
			
			// Build Jax-style arguments for the url param
			joms.jQuery.extend(settings.data, this.buildSArgs(sArgs));
			
			// If o is callback function, wrap it back as an ajax settings object.
			if (typeof o=='function')
				o = {success: o};
			
			// Override default settings with provided settings
			joms.jQuery.extend(settings, o);

			this.execute(settings);
		},
		
		/*
		 * joms.ajax.buildSArgs()
		 * [Internal function]
		 *
		 * Modified from jax.call().
		 * Instead of building arguments into a string,
		 * this function builds arguments into a key/value object,
		 * and jQuery.ajax() function will serialize them.
		 *
		 * @param sArgs {array} An array of arguments to pass into the server function.
		 */
		buildSArgs: function(sArgs)
		{
			var data = {};
			
			joms.jQuery(sArgs).each(function(i, a)
			{
				if (typeof a=='string')
					a.replace(/"/g, '&quot;');

				if (!jax.isArray(a))
					a = new Array('_d_', encodeURIComponent(a));
					
				data['arg'+i] = jax.stringify(a);
			})

			return data;
		},

		extractJomsData: function(jaxData, handler)
		{		
			var jomsData = [];
			joms.jQuery.each(eval(jaxData), function()
			{
				if (this[0]=='cs' && this[1]==handler)
				{
					jomsData.push(this.slice(3)[0]);
				}
			});

			return jomsData;
		}
	}	
});