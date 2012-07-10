joms.jQuery.extend(
    {
	/*
	 * 
	 */
	createUploadIframe: function(id, uri)
	{
	    //create frame
	    var frameId = 'jUploadFrame' + id;
	    var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
	    if(window.ActiveXObject)
	    {
		if(typeof uri== 'boolean'){
		    iframeHtml += ' src="' + 'javascript:false' + '"';

		}
		else if(typeof uri== 'string'){
		    iframeHtml += ' src="' + uri + '"';

		}
	    }
	    iframeHtml += ' />';
	    joms.jQuery(iframeHtml).appendTo(document.body);

	    return joms.jQuery('#' + frameId).get(0);
	},
	createUploadForm: function(id, fileElementId, data)
	{
	    //create form
	    var formId = 'jUploadForm' + id;
	    var fileId = 'jUploadFile' + id;
	    var form = joms.jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data"></form>');
	    if(data)
	    {
		for(var i in data)
		{
		    joms.jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
		}
	    }
	    var oldElement = joms.jQuery('#' + fileElementId);
	    var newElement = joms.jQuery(oldElement).clone();
	    joms.jQuery(oldElement).attr('id', fileId);
	    joms.jQuery(oldElement).before(newElement);
	    joms.jQuery(oldElement).appendTo(form);


	    
	    //set attributes
	    joms.jQuery(form).css('position', 'absolute');
	    joms.jQuery(form).css('top', '-1200px');
	    joms.jQuery(form).css('left', '-1200px');
	    joms.jQuery(form).appendTo('body');
	    return form;
	},

	ajaxFileUpload: function(s) {
	    // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout
	    s = joms.jQuery.extend({}, joms.jQuery.ajaxSettings, s);
	    var id = new Date().getTime();
	    var form = joms.jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data)=='undefined'?false:s.data));
	    var io = joms.jQuery.createUploadIframe(id, s.secureuri);
	    var frameId = 'jUploadFrame' + id;
	    var formId = 'jUploadForm' + id;
	    // Watch for a new set of requests
	    if ( s.global && ! joms.jQuery.active++ )
	    {
		joms.jQuery.event.trigger( "ajaxStart" );
	    }
	    var requestDone = false;
	    // Create the request object
	    var xml = {};
	    if ( s.global )
		joms.jQuery.event.trigger("ajaxSend", [xml, s]);
	    // Wait for a response to come back
	    var uploadCallback = function(isTimeout)
	    {
		var io = document.getElementById(frameId);
		try
		{
                    if(io.contentDocument)
		    {
			xml.responseText = io.contentDocument.body?io.contentDocument.body.innerHTML:null;
			xml.responseXML = io.contentDocument.XMLDocument?io.contentDocument.XMLDocument:io.contentDocument.document;
		    }
		    else if(io.contentWindow)
		    {
			xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
			if (xml.responseText.indexOf('pre'))
			    xml.responseText = xml.responseText.substring(xml.responseText.indexOf(">") + 1, xml.responseText.lastIndexOf("<"));
			xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;

		    }
		}catch(e)
		{
		    //joms.jQuery.handleError(s, xml, null, e);
		}
		if ( xml || isTimeout == "timeout")
		{
		    requestDone = true;
		    var status;
		    try {
			status = isTimeout != "timeout" ? "success" : "error";
			// Make sure that the request was successful or notmodified
			if ( status != "error" )
			{
			    // process the data (runs the xml through httpData regardless of callback)
			    var data = joms.jQuery.uploadHttpData( xml, s.dataType );
			    // If a local callback was specified, fire it and pass it the data
			    if ( s.success )
				s.success( data, status );

			    // Fire the global callback
			    if( s.global )
				joms.jQuery.event.trigger( "ajaxSuccess", [xml, s] );
			}
		    } catch(e)
		    {
			status = "error";
			//joms.jQuery.handleError(s, xml, status, e);
		    }

		    // The request was completed
		    if( s.global )
			joms.jQuery.event.trigger( "ajaxComplete", [xml, s] );

		    // Handle the global AJAX counter
		    if ( s.global && ! --joms.jQuery.active )
			joms.jQuery.event.trigger( "ajaxStop" );

		    // Process result
		    if ( s.complete )
			s.complete(xml, status);

		    joms.jQuery(io).unbind();

		    setTimeout(function()
			       {	try
					{
					    joms.jQuery(io).remove();
					    joms.jQuery(form).remove();

					} catch(e)
					{
					    //joms.jQuery.handleError(s, xml, null, e);
					}

			       }, 100);

		    xml = null;

		}
	    };
	    // Timeout checker
	    if ( s.timeout > 0 )
	    {
		setTimeout(function(){
			       // Check to see if the request is still happening
			       if( !requestDone ) uploadCallback( "timeout" );
			   }, s.timeout);
	    }
	    try
	    {

		var form = joms.jQuery('#' + formId);
		joms.jQuery(form).attr('action', s.url);
		joms.jQuery(form).attr('method', 'POST');
		joms.jQuery(form).attr('target', frameId);
		if(form.encoding)
		{
		    joms.jQuery(form).attr('encoding', 'multipart/form-data');
		}
		else
		{
		    joms.jQuery(form).attr('enctype', 'multipart/form-data');
		}
		joms.jQuery(form).submit();

	    } catch(e)
	    {
		//joms.jQuery.handleError(s, xml, null, e);
	    }

	    joms.jQuery('#' + frameId).load(uploadCallback);
	    return {abort: function () {}};

	},
	uploadHttpData: function( r, type ) {
	    var data = !type;
	    data = type == "xml" || data ? r.responseXML : r.responseText;
	    // If the type is "script", eval it in global context
	    if ( type == "script" )
		joms.jQuery.globalEval( data );
	    // Get the JavaScript object, if JSON is used.
	    if ( type == "json" ) {
		eval( "data = " + data );
	    }
	    // evaluate scripts within html
	    if ( type == "html" )
		joms.jQuery("<div>").html(data).evalScripts();
	    return data;
	}
    });

