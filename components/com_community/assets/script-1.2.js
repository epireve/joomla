/** Include JSON library **/
var JSON;if(!JSON){JSON={}}(function(){function str(a,b){var c,d,e,f,g=gap,h,i=b[a];if(i&&typeof i==="object"&&typeof i.toJSON==="function"){i=i.toJSON(a)}if(typeof rep==="function"){i=rep.call(b,a,i)}switch(typeof i){case"string":return quote(i);case"number":return isFinite(i)?String(i):"null";case"boolean":case"null":return String(i);case"object":if(!i){return"null"}gap+=indent;h=[];if(Object.prototype.toString.apply(i)==="[object Array]"){f=i.length;for(c=0;c<f;c+=1){h[c]=str(c,i)||"null"}e=h.length===0?"[]":gap?"[\n"+gap+h.join(",\n"+gap)+"\n"+g+"]":"["+h.join(",")+"]";gap=g;return e}if(rep&&typeof rep==="object"){f=rep.length;for(c=0;c<f;c+=1){if(typeof rep[c]==="string"){d=rep[c];e=str(d,i);if(e){h.push(quote(d)+(gap?": ":":")+e)}}}}else{for(d in i){if(Object.prototype.hasOwnProperty.call(i,d)){e=str(d,i);if(e){h.push(quote(d)+(gap?": ":":")+e)}}}}e=h.length===0?"{}":gap?"{\n"+gap+h.join(",\n"+gap)+"\n"+g+"}":"{"+h.join(",")+"}";gap=g;return e}}function quote(a){escapable.lastIndex=0;return escapable.test(a)?'"'+a.replace(escapable,function(a){var b=meta[a];return typeof b==="string"?b:"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})+'"':'"'+a+'"'}function f(a){return a<10?"0"+a:a}"use strict";if(typeof Date.prototype.toJSON!=="function"){Date.prototype.toJSON=function(a){return isFinite(this.valueOf())?this.getUTCFullYear()+"-"+f(this.getUTCMonth()+1)+"-"+f(this.getUTCDate())+"T"+f(this.getUTCHours())+":"+f(this.getUTCMinutes())+":"+f(this.getUTCSeconds())+"Z":null};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(a){return this.valueOf()}}var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={"\b":"\\b","\t":"\\t","\n":"\\n","\f":"\\f","\r":"\\r",'"':'\\"',"\\":"\\\\"},rep;if(typeof JSON.stringify!=="function"){JSON.stringify=function(a,b,c){var d;gap="";indent="";if(typeof c==="number"){for(d=0;d<c;d+=1){indent+=" "}}else if(typeof c==="string"){indent=c}rep=b;if(b&&typeof b!=="function"&&(typeof b!=="object"||typeof b.length!=="number")){throw new Error("JSON.stringify")}return str("",{"":a})}}if(typeof JSON.parse!=="function"){JSON.parse=function(text,reviver){function walk(a,b){var c,d,e=a[b];if(e&&typeof e==="object"){for(c in e){if(Object.prototype.hasOwnProperty.call(e,c)){d=walk(e,c);if(d!==undefined){e[c]=d}else{delete e[c]}}}}return reviver.call(a,b,e)}var j;text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return"\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4)})}if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,"@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,"]").replace(/(?:^|:|,)(?:\s*\[)+/g,""))){j=eval("("+text+")");return typeof reviver==="function"?walk({"":j},""):j}throw new SyntaxError("JSON.parse")}}})()
/** END JSON Object **/

// In the case where our joms.jQuery
// is overriden by other jQuery.
if (typeof(joms)=='undefined')
{
	// We will recreate our joms namespace
	// with joms.jQuery pointing to their jQuery.
	joms = {
		jQuery: window.jQuery,
		extend: function(obj){
			this.jQuery.extend(this, obj);
		}
	}
}

var joms_message_sending = false;
joms.extend({
	plugins: {
		extend: function (obj){
			//joms.jQuery.extend(joms.plugin, func);
			joms.jQuery.extend(joms.plugins, obj);
		},
		initialize: function()
		{
			joms.jQuery.each(joms.plugins, function(index, value) {
				try{
				    value.initialize();
				}
				catch(err)
				{
					//Handle errors here
				}
			});
		}
	},
	activities: {
		showMap: function( id, addr ){
			// this function can be used in multiple places. so we need to detect where are we
			// check if we're in any activity streams
			if(joms.jQuery('#newsfeed-map-'+id).length){			
				if(joms.jQuery('#newsfeed-map-'+id+ ' img').length == 0){
					var mapWidth = joms.jQuery('#newsfeed-map-'+id).parent().width();
					var mapHTML = '<img src="http://maps.google.com/maps/api/staticmap?center='+addr+'&amp;zoom=14&amp;size=' + mapWidth + 'x150&amp;sensor=false&amp;markers=color:red|'+addr+'" />';
					mapHTML += '<img src="http://maps.google.com/maps/api/staticmap?center='+addr+'&amp;zoom=5&amp;size=' + mapWidth + 'x150&amp;sensor=false&amp;markers=color:red|'+addr+'" />';
					mapHTML += '<img src="http://maps.google.com/maps/api/staticmap?center='+addr+'&amp;zoom=2&amp;size=' + mapWidth + 'x150&amp;sensor=false&amp;markers=color:red|'+addr+'" />';
					joms.jQuery('#newsfeed-map-'+id+ ' .newsfeed-mapFade').append(mapHTML);
					
					// Reposition the heatzone
					var heatLeft = (mapWidth / 2) - 15;
					joms.jQuery('.newsfeed-map-heatzone').css({
						'top': '40px', left: heatLeft + 'px'
					});
				}
				joms.jQuery('#newsfeed-map-'+id).toggle();
			}
	
			//check if we're in video page
			if(joms.jQuery('#video-'+id).length){
				var mapWidth = joms.jQuery('#video-'+id).find('.video-map-location').width();
				var mapHTML = '<img src="http://maps.google.com/maps/api/staticmap?center='+addr+'&amp;zoom=5&amp;size=' + mapWidth + 'x250&amp;sensor=false&amp;markers=color:red|'+addr+'" />';
				
				if(joms.jQuery('.video-map').height()==0){
				    joms.jQuery('#video-'+id).find('.video-map-location').html(mapHTML);
				    joms.jQuery('#video-'+id).find('.video-map').css({height: 'auto'});
				} else {
				    joms.jQuery('#video-'+id).find('.video-map-location').html('');
				    joms.jQuery('#video-'+id).find('.video-map').css({height: 'auto'});
				}
			}
		},
		getContent: function( activityId ){
				jax.call('community' , 'activities,ajaxGetContent' , activityId );
		},
		setContent: function( activityId , content ){
			joms.jQuery("#profile-newsfeed-item-content-" + activityId ).html( content )
				.removeClass("small profile-newsfeed-item-action").addClass("newsfeed-content-hidden").slideDown();
		},
		showVideo: function( activityId ){
			joms.jQuery('#profile-newsfeed-item-content-' + activityId + ' .video-object').slideDown();
			joms.jQuery('#profile-newsfeed-item-content-' + activityId + ' .video-object embed').css('width' , joms.jQuery('#profile-newsfeed-item-content-' + activityId ).width() );
		},
		selectCustom: function( type ){

			if( type == 'predefined' )
			{
				joms.jQuery( '#custom-text' ).css( 'display' , 'none');
				joms.jQuery( '#custom-predefined').css( 'display' , 'block' );
			}
			else
			{
				joms.jQuery( '#custom-text' ).css( 'display' , 'block' );
				joms.jQuery( '#custom-predefined').css( 'display' , 'none' );
			}
		},
		addCustom: function(){
			if( jQuery('input[name=custom-message]:checked').val() == 'predefined' )
			{
			    var selected		= joms.jQuery('#custom-predefined').val();
			    var selectedText	= joms.jQuery('#custom-predefined :selected').html();
			    
			    if( selected != 'default' )
				{
					jax.call( 'community' , 'activities,ajaxAddPredefined' , selected , selectedText );
 				}
			}
			else
			{
			    customText  =   joms.jQuery.trim( joms.jQuery('#custom-text').val() );

			    if( customText.length != 0 ){
				    jax.call( 'community' , 'activities,ajaxAddPredefined' , 'system.message' , customText );
			    }
			}
		},
		append: function( html ){
			joms.jQuery( '#activity-more,#activity-exclusions' ).remove();
			joms.jQuery( '#activity-stream-container' ).append( html );
			joms.jQuery('body').focus();
		},
		initMap: function()
		{
			if (joms.jQuery('.newsfeed-mapFade') != null || joms.jQuery('.newsfeed-map-heatzone') != null) {
			    if(joms.jQuery('.newsfeed-mapFade').length) {
				    joms.jQuery('.newsfeed-mapFade').live('mouseover',function(e) {
					    joms.jQuery(this).find('img:eq(2)').fadeOut(0);
						
				    });
				    joms.jQuery('.newsfeed-mapFade').live('mouseout',function(e) {
					    joms.jQuery(this).find('img:eq(2)').fadeIn(0);
						
				    });

				    joms.jQuery('.newsfeed-map-heatzone').live('mouseover',function(e) {
					    joms.jQuery(this).parent().find('img:eq(1)').fadeOut();
				    });

				    joms.jQuery('.newsfeed-map-heatzone').live('mouseout',function(e) {
					    joms.jQuery(this).parent().find('img:eq(1)').fadeIn(0);
				    });
			    }
			}
		},
		more: function(){
			// Retrieve the earliest activity id to exclude the activity
			var exclusions = '';
			if(joms.jQuery('.cFeed-item').length != 0){
				exclusions = joms.jQuery('#activity-stream-container .cFeed .cFeed-item').last().attr('id').substring(21);
			}
			
			//check if this is group/event
			var apptype = '';
			var appid = '';
			if(joms.jQuery('#apptype').length != 0){
					apptype = joms.jQuery('#apptype').val();
			}
			
			if(joms.jQuery('#appid').length != 0){
					appid = joms.jQuery('#appid').val();
			}
			
			var categoryFilter = '';
			
			if(joms.jQuery('.all-activity').hasClass('active-state')){
				categoryFilter = 'all';
			}else if(joms.jQuery('.me-and-friends-activity').hasClass('active-state')){
				categoryFilter = 'friends';
			}else if(joms.jQuery('.p-active-profile-activity').hasClass('active-state')){
				categoryFilter = 'self';
			}else if(joms.jQuery('.p-active-profile-and-friends-activity').hasClass('active-state')){
				categoryFilter = 'friends';
			}else{
				categoryFilter = 'self';
			}
			
			// Show loading image
			joms.jQuery( '#activity-more .more-activity-text' ).hide();
			joms.jQuery( '#activity-more .loading' ).show().css( 'float' , 'none' ).css( 'margin' , '5px 5px 0 180px');
			jax.call( 'community' , 'activities,ajaxGetActivities' , exclusions , joms.jQuery( '#community-wrap #activity-type').val(), js_profileId,'',categoryFilter, '' , apptype, appid );
		},
		appendLatest: function(html, delay, text){
			//joms.jQuery( '#activity-exclusions' ).remove();
			joms.jQuery('ul.cFeed').prepend(html);
			
			var totalNewUpdate = joms.jQuery('.newly-added').length;

			if(totalNewUpdate > 0){
				joms.jQuery('#activity-update-click').html(text);
				/*
				if(totalNewUpdate == 1){
					joms.jQuery('#activity-update-click').html("1 new update");
				}else{
					joms.jQuery('#activity-update-click').html(totalNewUpdate+" new updates");
				}
				*/
				joms.jQuery('.joms-latest-activities-container').show();
			}
			setTimeout("reloadActivities();",delay);
		},
		nextActivitiesCheck: function(delay){
			setTimeout("reloadActivities();",delay);
		},
		getLatestContent: function (latestId,isProfilePage){
			// Retrieve exclusions so we won't fetch the same data again.
			var exclusions = joms.jQuery('#activity-exclusions').val();
			var categoryFilter = '';
			
			if(joms.jQuery('.all-activity').hasClass('active-state')){
				categoryFilter = 'all';
			}else if(joms.jQuery('.me-and-friends-activity').hasClass('active-state')){
				categoryFilter = 'friends';
			}else if(joms.jQuery('.p-active-profile-activity').hasClass('active-state')){
				categoryFilter = 'self';
			}else if(joms.jQuery('.p-active-profile-and-friends-activity').hasClass('active-state')){
				categoryFilter = 'friends';
			}else{
				categoryFilter = 'self';
			}
			
			//pause if cWindow is enabled
			if(joms.jQuery('div#cWindow').hasClass('dialog')){
				joms.activities.nextActivitiesCheck(5000);
			}else if(latestId > 0){
				jax.call( 'community' , 'activities,ajaxGetActivities' , '' , joms.jQuery( '#community-wrap #activity-type').val(), js_profileId, latestId, isProfilePage,categoryFilter );
			}
		},
		getLatestAppContent: function (latestId,isProfilePage){
			//check if this is group/event
			var apptype = '';
			var appid = '';
			if(joms.jQuery('#apptype').length != 0){
					apptype = joms.jQuery('#apptype').val();
			}
			
			if(joms.jQuery('#appid').length != 0){
					appid = joms.jQuery('#appid').val();
			}
			
			//pause if cWindow is enabled
			if(joms.jQuery('div#cWindow').hasClass('dialog')){
				joms.activities.nextActivitiesCheck(5000);
			}else if(latestId > 0){
				jax.call( 'community' , 'activities,ajaxGetActivities' , '' , joms.jQuery( '#community-wrap #activity-type').val(), '', latestId, '' , '', apptype, appid );
			}
		},
		remove: function (app, activityid){
			var ajaxCall = "jax.call('community', 'activities,ajaxConfirmDeleteActivity', '"+app+"', '"+activityid+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		}
	},
	apps: {
		windowTitle: '',
		toggle: function (id){
			joms.jQuery(id).children('.app-box-actions').slideToggle('fast');
			joms.jQuery(id).children('.app-box-footer').slideToggle('fast');
			joms.jQuery(id).children('.app-box-content').slideToggle('fast',
				function() {

					joms.jQuery.cookie( id , joms.jQuery(this).css('display') );
					joms.jQuery(id).toggleClass('collapse', (joms.jQuery(this).css('display')=='none'));
				}
			);
		},
		showAboutWindow: function(appName){
			var ajaxCall = "jax.call('community', 'apps,ajaxShowAbout', '"+appName+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		showPrivacyWindow: function(appName){
			var ajaxCall = "jax.call('community', 'apps,ajaxShowPrivacy', '"+appName+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		showSettingsWindow: function(id, appName){
			var ajaxCall = "jax.call('community', 'apps,ajaxShowSettings', '"+id+"', '"+appName+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		savePrivacy: function(){
			var value   = joms.jQuery('input[name=privacy]:checked').val();
			var appName = joms.jQuery('input[name=appname]').val();
			jax.call('community', 'apps,ajaxSavePrivacy', appName, value);
		},
		saveSettings: function(){
			jax.call('community', 'apps,ajaxSaveSettings', jax.getFormValues('appSetting'));
		},
		remove: function(appName){
			var ajaxCall = "jax.call('community', 'apps,ajaxRemove', '"+appName+"');";
			cWindowShow(ajaxCall, this.windowTitle, 450, 100);
		},
		add: function(appName){
			jax.call('community', 'apps,ajaxAdd', appName );
		},
		initToggle: function(){
			joms.jQuery('.app-box').each(function(){
				var id	= '#' + joms.jQuery(this).attr('id');

				if(joms.jQuery.cookie( id )=='none')
				{
					joms.jQuery(id).addClass('collapse');
					joms.jQuery(id).children('.app-box-actions').css('display' , 'none' );
					joms.jQuery(id).children('.app-box-footer').css('display' , 'none' );
					joms.jQuery(id).children('.app-box-content').css('display' , 'none' );
				}
			});
		}
	},
	bookmarks:{
		show: function( currentURI ){
			var ajaxCall = "jax.call('community', 'bookmarks,ajaxShowBookmarks','" + currentURI + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		email: function( currentURI ){

			var formContent	= jax.getFormValues('bookmarks-email');
			var content		= formContent[1][1];
			var email		= formContent[0][1];

			var ajaxCall = "jax.call('community', 'bookmarks,ajaxEmailPage','" + currentURI + "','" +  email + "',\"" + content + "\");";
			cWindowShow( ajaxCall , '', 450, 100);
		}
	},
	report: {
		emptyMessage: '',

		checkReport: function(){
			if( joms.jQuery( '#report-message' ).val() == '' )
			{
				joms.jQuery( '#report-message-error' ).html( this.emptyMessage ).css( 'color' , 'red' );
				return false;
			}
			return true;
		},
		showWindow: function ( reportFunc, arguments ){   
			var ajaxCall	= 'jax.call("community" , "system,ajaxReport" , "' + reportFunc + '","' + location.href + '" ,' + arguments + ');';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		submit: function ( reportFunc , pageLink , arguments ){
			if( joms.report.checkReport() )
			{
			var formVars = jax.getFormValues('report-form');

			var ajaxcall='jax.call("community", "system,ajaxSendReport","' + reportFunc + '","' + location.href + '","' + formVars[1][1] + '" , ' + arguments + ')';

// 				var message	= escape( joms.jQuery('#report-message').val() );
// 				var ajaxcall='jax.call("community", "system,ajaxSendReport","' + reportFunc + '","' + location.href + '","' + message + '" , ' + arguments + ')';
				cWindowShow(ajaxcall, '', 450, 100);
			}
		}
	},
	featured: {
		add: function(uniqueId , controller ){
			var ajaxCall = "jax.call('community', '" + controller + ",ajaxAddFeatured', '"+uniqueId+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		remove: function(uniqueId , controller ){
			var ajaxCall = "jax.call('community','" + controller + ",ajaxRemoveFeatured','" + uniqueId + "');";
			cWindowShow(ajaxCall,'', 450, 100);
		}
	},
	flash: {
		enabled: function(){
			// ie
			try
			{
				try
				{
					// avoid fp6 minor version lookup issues
					// see: http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/
					var axo = new ActiveXObject('ShockwaveFlash.ShockwaveFlash.6');
					try
					{
						axo.AllowScriptAccess = 'always';
					}
					catch(e)
					{
						return '6,0,0';
					}
				}
				catch(e)
				{
				}
				return new ActiveXObject('ShockwaveFlash.ShockwaveFlash').GetVariable('$version').replace(/\D+/g, ',').match(/^,?(.+),?$/)[1];
			// other browsers
			}
			catch(e)
			{
				try
				{
					if(navigator.mimeTypes["application/x-shockwave-flash"].enabledPlugin)
					{
						return (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]).description.replace(/\D+/g, ",").match(/^,?(.+),?$/)[1];
					}
				}
				catch(e)
				{
				}
			}
			return false;
		}
	},
	invitation:{
		showForm: function( users , callback , cid , displayFriends , displayEmail ){
			var ajaxCall	= 'jax.call("community", "system,ajaxShowInvitationForm","' + users + '","' + callback + '","' + cid + '","' + displayFriends + '","' + displayEmail + '")';
			var height		= 520;

			height			= displayFriends != "0" ? height : height - 108;
			height			= displayEmail != "0" ? height : height - 108;

			cWindowShow(ajaxCall, '', 550, height );
		},
		send: function( callback , cid ){
			jax.call( 'community' , 'system,ajaxSubmitInvitation' , callback , cid , jax.getFormValues('community-invitation-form') );
		},
		selectMember:function( element ){
		
			if( joms.jQuery( element + ' input').is(':checked') )
			{
				joms.jQuery(element).clone().appendTo('#community-invited-list');
				joms.jQuery(element).remove();
				joms.jQuery( element ).addClass('invitation-item-invited').children('.invitation-checkbox').show();
			}
			else
			{
				joms.jQuery(element).remove();
				//joms.jQuery( element ).removeClass('invitation-item-invited');
			}
		},
		filterMember:function(){
			joms.jQuery('#community-invitation-list li').each(function(index){
				element =joms.jQuery(this).attr('id');
				if(joms.jQuery('#community-invited-list #'+element).is('li')){
					joms.jQuery(this).remove();
				}
				//check the selected friend list
				if(joms.jQuery('#inbox-selected-to').length > 0 ){
					if(joms.jQuery('#inbox-selected-to #'+element).is('li')){
						joms.jQuery(this).remove();
					}				
				}
			});			
		},
		showResult:function(){
			joms.jQuery('#cInvitationTabContainer div').removeClass('active');
			joms.jQuery('#cInvitationTabContainer #community-invitation').addClass('active');
		},
		showSelected:function(){
			joms.jQuery('#cInvitationTabContainer div').removeClass('active');
			joms.jQuery('#cInvitationTabContainer #community-invited').addClass('active');
		},
		selectNone: function ( listID ){
			joms.jQuery(listID).find('li').each(function() {
				joms.jQuery(this).remove();
			});
		},
		selectAll: function ( listID ){
			joms.jQuery(listID).find('li').each(function() {
				joms.jQuery(this).find('input').attr('checked','checked');
				if( joms.jQuery(this).find('input').attr('checked') ){
					joms.invitation.selectMember('#'+joms.jQuery(this).attr('id'));
				}
			});
		}
	},
	album:{
	  init: function(){
	    joms.jQuery('.album').hover(
	      function() {
	        // only show the hover menu if there's actually something in it
	        if(joms.jQuery(this).find('.album-actions a').length) joms.jQuery(this).find('.album-actions').fadeIn('fast');
	      }, function() {
	        joms.jQuery(this).find('.album-actions').stop(true, true).hide();
	      }
	    );
	
		joms.jQuery('.video-item').hover(
	      function() {
	        // only show the hover menu if there's actually something in it
	        if(joms.jQuery(this).find('.album-actions a').length) joms.jQuery(this).find('.album-actions').fadeIn('fast');
	      }, function() {
	        joms.jQuery(this).find('.album-actions').stop(true, true).hide();
	      }
	    );
	    
	    joms.jQuery('.cFeaturedItem , .featured-item').hover(
        function() {
          // only show the hover menu if there's actually something in it
          if(joms.jQuery(this).find('.album-actions a').length) joms.jQuery(this).find('.album-actions').fadeIn('fast');
        }, function() {
          joms.jQuery(this).find('.album-actions').stop(true, true).hide();
        }
      );
	    
	    
	  }
	},
	memberlist:{
		submit: function(){
		
			if( joms.jQuery('input#title').val() == '' )
			{
				joms.jQuery('#filter-title-error').show();
				return false;
			}
			
			if( joms.jQuery('textarea#description').val() == '' )
			{
				joms.jQuery('#filter-description-error').show();
				return false;
			}
			
			joms.jQuery('#jsform-memberlist-addlist').submit();
		},
		showSaveForm: function( keys , filterJson ){
			var keys = keys.split( ',' );
			var values = Array();
			var avatarOnly	= jQuery('#avatar:checked').val() != 1 ? 0 : 1;
			
			for( var i = 0; i < keys.length ; i++ )
			{
				var tmpArray	= new Array();
				var value		= '';
				var key			= keys[i];
                                
				if( (filterJson['fieldType' + key] == 'date' || filterJson['fieldType' + key] == 'birthdate') && filterJson['condition' + key] == 'between')
				{
					value		= filterJson[ 'value' + keys[i] ] + ',' + filterJson[ 'value' + keys[i] + '_2' ]
				}
				else
				{
					value	= filterJson[ 'value' + keys[ i ] ];
				}

				values[i]	= new Array( 'field=' + filterJson['field'+ keys[i] ] , 
										 'condition=' + filterJson['condition'+ keys[i]] ,
										 'fieldType=' + filterJson['fieldType'+ keys[i]] ,
										 'value=' + value 
									);
			}
			
			var valuesString = '';
			for( var x = 0; x < values.length; x++ )
			{
				valuesString += '"' + values[x] + '"';

				if( (x + 1) != values.length )
					valuesString += ',';
			}
			
			var ajaxCall = 'jax.call("community", "memberlist,ajaxShowSaveForm","' + joms.jQuery("input[@name=operator]:checked").val() + '","' + avatarOnly + '",' + valuesString + ');';
			cWindowShow(ajaxCall, '', 470, 300);
		}
	},
	notifications: {
		showWindow: function (){
			var ajaxCall = 'jax.call("community", "notification,ajaxGetNotification", "")';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		updateNotifyCount: function (){
			var notifyCount	= joms.jQuery('#toolbar-item-notify-count').text();

			if(joms.jQuery.trim(notifyCount) != '' && notifyCount > 0)
			{
				//first we update the count. if the updated count == 0, then we hide the tab.
				notifyCount = notifyCount - 1;
				joms.jQuery('#toolbar-item-notify-count').html(notifyCount);
				if (notifyCount == 0)
				{
					joms.jQuery('#toolbar-item-notify').hide(); 
					setTimeout('cWindowHide()', 1000);
				}
			}
		},
		showRequest: function(){
			var ajaxCall = 'jax.call("community", "notification,ajaxGetRequest", "")';
			cMiniWindowShow(ajaxCall, '', 450, 100);
		},
		showInbox: function(){
			var ajaxCall = 'jax.call("community", "notification,ajaxGetInbox", "")';
			cMiniWindowShow(ajaxCall, '', 450, 100);
		},
		showUploadPhoto: function(albumId, groupId){
			var ajaxCall = 'jax.call("community", "photos,ajaxUploadPhoto","'+albumId+'","'+groupId+'")';
			cWindowShow(ajaxCall, '', 600, 400);
		}
	},
	filters:{
		bind: function(){
			var loading	= this.loading;
			joms.jQuery(document).ready( function()
			{
				//sorting option binding for members display
				joms.jQuery('.newest-member').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
						jax.call('community', 'frontpage,ajaxGetNewestMember', frontpageUsers);
					}
				});
				joms.jQuery('.active-member').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
			            loading( joms.jQuery(this).attr('class') );
						jax.call('community', 'frontpage,ajaxGetActiveMember', frontpageUsers);
					}
				});
				joms.jQuery('.popular-member').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetPopularMember', frontpageUsers);
					}
				});
				joms.jQuery('.featured-member').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetFeaturedMember', frontpageUsers);
					}
				});

				//sorting option binding for activity stream
				joms.jQuery('.all-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
			            loading( joms.jQuery(this).attr('class') );
						joms.ajax.call('frontpage,ajaxGetActivities', [ 'all' ] , {
							success: function()
							{
								joms.jQuery( '#activity-type' ).val( 'all' );
							}
						});
					}
				});
				joms.jQuery('.me-and-friends-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	joms.ajax.call('frontpage,ajaxGetActivities', [ 'me-and-friends' ] , {
							success: function()
							{
								joms.jQuery( '#activity-type' ).val( 'me-and-friends' );
							}
						});
					}
				});
				joms.jQuery('.active-profile-and-friends-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile-and-friends', joms.user.getActive());
					}
				});
				joms.jQuery('.active-profile-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile', joms.user.getActive());
					}
				});
				joms.jQuery('.p-active-profile-and-friends-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile-and-friends', joms.user.getActive(), 'profile');
					}
				});
				joms.jQuery('.p-active-profile-activity').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetActivities', 'active-profile', joms.user.getActive(), 'profile');
					}
				});

				// sorting and binding for videos
				joms.jQuery('.newest-videos').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
						jax.call('community', 'frontpage,ajaxGetNewestVideos', frontpageVideos);
					}
				});
				joms.jQuery('.popular-videos').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetPopularVideos', frontpageVideos);
					}
				});
				joms.jQuery('.featured-videos').bind('click', function() {
				    if ( !joms.jQuery(this).hasClass('active-state') ) {
				        loading( joms.jQuery(this).attr('class') );
				    	jax.call('community', 'frontpage,ajaxGetFeaturedVideos', frontpageVideos);
					}
				});

				// remove last link border
				joms.jQuery('.popular-member').css('border-right', '0').css('padding-right', '0');
				joms.jQuery('.me-and-friends-activity').css('border-right', '0').css('padding-right', '0');
				joms.jQuery('.active-profile-activity').css('border-right', '0').css('padding-right', '0');
			});
		},
		loading: function(element){
			elParent = joms.jQuery('.'+element).parent().parent().attr('id');
			if ( elParent === '' ) {
		        elParent = joms.jQuery('.'+element).parent().attr('id');
			}
		    joms.jQuery('#' + elParent + ' .loading').show();
		    joms.jQuery('#' + elParent + ' a').removeClass('active-state');
		    joms.jQuery('.'+element).addClass('active-state');
		},
		hideLoading: function(){
			joms.jQuery( '.loading' ).hide();
			// rebind the tooltip
			joms.jQuery('.jomTipsJax').addClass('jomTips');
			joms.tooltip.setup();
		}
	},
	groups: {
		invitation: {
			accept: function( groupId ){
				jax.call( 'community' , 'groups,ajaxAcceptInvitation' , groupId )
			},
			reject: function( groupId ){
				jax.call( 'community' , 'groups,ajaxRejectInvitation' , groupId  );
			}
		},
		addInvite: function( element ){
			var parentId = joms.jQuery('#' +element).parent().attr('id');

			if(parentId == "friends-list")
			{
				joms.jQuery("#friends-invited").append(joms.jQuery('#' +element)).html();
			}
			else
			{
				joms.jQuery("#friends-list").append(joms.jQuery('#' +element)).html();
			}
		},
		removeTopic: function( title , groupid , topicid ){
			var ajaxCall = 'jax.call("community","groups,ajaxShowRemoveDiscussion", "' + groupid + '","' + topicid + '");';
			cWindowShow(ajaxCall, title, 450, 100);
		},
		lockTopic: function( title , groupid , topicid ){
			var ajaxCall = 'jax.call("community","groups,ajaxShowLockDiscussion", "' + groupid + '","' + topicid + '");';
			cWindowShow(ajaxCall, title, 450, 100);
		},
		editBulletin: function(){

			if( joms.jQuery('#bulletin-edit-data').css('display') == 'none' )
			{
				joms.jQuery('#bulletin-edit-data').show();
			}
			else
			{
				joms.jQuery('#bulletin-edit-data').hide();
			}

		},
		removeBulletin: function( title , groupid , bulletinid ){
			var ajaxCall = 'jax.call("community", "groups,ajaxShowRemoveBulletin", "' + groupid + '","' + bulletinid + '");';
			cWindowShow(ajaxCall, title, 450, 100);
		},
		unpublish: function( groupId ){
			jax.call( 'community' , 'groups,ajaxUnpublishGroup', groupId);
		},
		leave: function( groupid ){
			var ajaxCall = 'jax.call("community", "groups,ajaxShowLeaveGroup", "' + groupid + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		join : function ( groupid, joinfromdiscussion ){
                    jax.call("community","groups,ajaxJoinGroup",[groupid], [joinfromdiscussion]);
                },
		joinComplete : function( msg ){
			joms.jQuery('input#join').val(msg);
			joms.jQuery('.loading-icon').hide();
			joms.jQuery('#add-reply').show();
			joms.jQuery('#add-reply').click(function(){
				joms.jQuery('div#community-groups-wrap').hide();
				
				joms.jQuery('textarea#wall-message').css('width','100%');
				joms.jQuery('.reply-form').show();
			});
		},
		edit: function(){
			// Check if input is already displayed
			joms.jQuery('#community-group-info .cdata').each(function(){
				// Test if the next div is cinput

				if(joms.jQuery(this).next().html() && joms.jQuery(this).css('display') != 'none' )
					joms.jQuery(this).css('display' , 'none');
				else
					joms.jQuery(this).css('display' , 'block');
			});

			joms.jQuery('#community-group-info .cinput').each(function(){
				if(joms.jQuery(this).css('display') == 'none')
					joms.jQuery(this).css('display' , 'block');
				else
					joms.jQuery(this).css('display' , 'none');
			});

			if(joms.jQuery('div#community-group-info-actions').css('display') != 'none')
				joms.jQuery('div#community-group-info-actions').css('display' , 'none');
			else
				joms.jQuery('div#community-group-info-actions').css('display' , 'block');
		},
		save: function( groupid ){
			var name		= joms.jQuery('#community-group-name').val();
			var description	= joms.jQuery('#community-group-description').val();
			var website		= joms.jQuery('#community-group-website').val();
			var category	= joms.jQuery('#community-group-category').val();
			var approvals	= joms.jQuery("input[@name='group-approvals']:checked").val();

			jax.call('community' , 'groups,ajaxSaveGroup' , groupid , name , description , website , category , approvals);
		},
		update: function( groupName , groupDescription , groupWebsite , groupCategory){
			// Re-update group data
			joms.jQuery('#community-group-data-name').html( groupName );
			joms.jQuery('#community-group-data-description').html( groupDescription );
			joms.jQuery('#community-group-data-website').html( groupWebsite );
			joms.jQuery('#community-group-data-category').html( groupCategory );
			this.edit();
		},
		deleteGroup: function( groupId ){
			var ajaxCall = "jax.call('community', 'groups,ajaxWarnGroupDeletion', '" + groupId + "');";
			cWindowShow(ajaxCall, '', 450, 100, 'error');
		},
		toggleSearchSubmenu: function( e ){

			joms.jQuery(e).next('ul').toggle().find('input[type=text]').focus();
		},
		confirmMemberRemoval: function( memberId, groupId ){

			var ajaxCall = function()
			{
				jax.call("community", "groups,ajaxConfirmMemberRemoval", memberId, groupId);	
			};

			cWindowShow(ajaxCall, '', 450, 80, 'warning');
		},
		removeMember: function( memberId , groupId ){
			var banMember = joms.jQuery('#cWindow input[name=block]').attr('checked');

			if (banMember)
			{
				jax.call('community', 'groups,ajaxBanMember', memberId , groupId );
			} else {
				jax.call('community', 'groups,ajaxRemoveMember', memberId , groupId );
			}			
		}		
	},
	photos: {
		multiUpload: {
			label: {
				filename: 'Filename',
				size:'Size',
				status:'Status',
				filedrag:'Drag files here.',
				addfiles:'Add Files',
				startupload:'Start Upload',
				invalidfiletype:'Invalid File Type',
				exceedfilesize:'Image file size exceeded limit',
				stopupload:'Stop Upload'
			},
			groupid: '',
			noticeDivId:'photoUploaderNotice',
			defaultMsg: '',
			groupEmptyValidateMsg: '',
			uploadingCreateMsg: '',
			uploadingSelectMsg: '',
			refreshNeeded: false,
			_init: function (groupid, uploadMsg)
			{		
				if (groupid != undefined && parseInt(groupid) > 0) 
				{
					joms.photos.multiUpload.groupid = groupid;
				}

				if (uploadMsg != undefined && typeof uploadMsg == "object")
				{
					for (var indexes in uploadMsg)
					{
						joms.photos.multiUpload[indexes] = uploadMsg[indexes];
					}					
				}
				
				pluploader = joms.jQuery("#multi_uploader").pluploadQueue({
					// General settings
					//runtimes: 'silverlight,gears,html5,browserplus,html4',
					runtimes: 'gears,html5,browserplus,html4',
					url: 'index.php?option=com_community&view=photos&task=multiUpload',
					max_file_size: '10mb',
					chunk_size: '10mb',
					unique_names: true,

					// Resize images on clientside if we can
					resize: {width: 2100, height: 2100, quality: 90},

					// Specify what files to browse for
					filters: [
						{title: "Image files", extensions: "jpg,gif,png,jpeg"}
					],

					// Flash/Silverlight paths
					flash_swf_url: 'components/com_community/assets/multiupload_js/plupload.flash.swf',
					silverlight_xap_url: 'components/com_community/assets/multiupload_js/plupload.silverlight.xap',

					// PreInit events, bound before any internal events
					preinit: {
						Init: function(up, info) {
							joms.photos.multiUpload.log('[Init]', 'Info:', info, 'Features:', up.features);							
						},

						BeforeUpload: function() {
							if (joms.jQuery("#new-album").css('display') == 'inline')
							{
								if(joms.photos.multiUpload.groupid!='' && joms.jQuery('#album-name').val()==""){
								    joms.photos.multiUpload.stopUploading();
								    alert(joms.photos.multiUpload.groupEmptyValidateMsg);
								    return;
								} else {
								    joms.photos.multiUpload.stopUploading();
								    var albumName = joms.jQuery('#album-name').val().trim();
								    jax.call('community' , 'photos,ajaxCreateAlbum' , albumName, joms.photos.multiUpload.groupid );
								}
							}	
							else 
							{
								var newAlbumName = joms.jQuery('#albumid option:selected').html();
								var msgToUse = joms.photos.multiUpload.uploadingSelectMsg.replace('%1$s', '<strong>'+newAlbumName+'</strong>');
								joms.photos.multiUpload.displayNotice(msgToUse);
								
								joms.photos.multiUpload.assignUploadUrl( joms.photos.multiUpload.getSelectedAlbumId() );
							}
							
							joms.photos.multiUpload.hideShowInput(true);
							joms.jQuery('#photo-uploader').css('overflow', 'hidden');	
						},

						UploadFile: function(up, file) {
							joms.photos.multiUpload.log('[UploadFile]', file);

							// You can override settings before the file is uploaded
							// up.settings.url = 'upload.php?id=' + file.id;
							// up.settings.multipart_params = {param1: 'value1', param2: 'value2'};
							
							// Disable Upload Button when upload is running
							joms.jQuery('.plupload_start').addClass('plupload_disabled');
						},
						
						FileUploaded: function ( uploader, file, response) {
							
							//uploadData = response.response.split("'")[5];
							try {
								var uploadData = joms.jQuery.parseJSON(response.response);
							}
							catch(e)
							{
								joms.photos.multiUpload.removeQueue(file.id);
								pluploader.pluploadQueue().stop();
								joms.photos.multiUpload.displayNotice('<strong>Error Uploading.</strong>');
							}
							var uploadedFile = pluploader.pluploadQueue().getFile(file.id);
							
							if (uploadData != undefined && uploadedFile != undefined)
							{
								if (uploadData.error != undefined && uploadData.photoId == '') // there is error
								{
									joms.photos.multiUpload.removeQueue(file.id);
									joms.jQuery('#upload-footer').show();
									joms.jQuery('#message-between').hide();
									joms.jQuery('.add-more').hide();
									try {
										pluploader.pluploadQueue().stop();
									} catch(e) {}
									
									joms.photos.multiUpload.showThumbnail();
									joms.photos.multiUpload.displayNotice('<strong>'+uploadData.msg+'</strong>');
									joms.jQuery('.plupload_file_name span').bind('click', function(event) {joms.photos.multiUpload.editTitle(event)});
								}
								else // update photo details into file dictionary
								{
									uploadedFile.imgSrc = uploadData.info;
									uploadedFile.photoId = uploadData.photoId;
									joms.photos.multiUpload.refreshNeeded = true;
								}
							}
							
						},
						FilesAdded: function(up, files) {
							joms.jQuery('.plupload_file_name span').unbind('click');
							joms.jQuery('.plupload_button.plupload_start').show();
							joms.jQuery('.plupload_button.plupload_start').css('display','inline-block');	
						},
						
						FilesRemoved: function(up, files) {
							if (pluploader.pluploadQueue().files.length == 0)
							{
								joms.jQuery('.plupload_button.plupload_start').hide();
							}
						},

						UploadComplete: function (uploader) {
							joms.photos.multiUpload.uploadCompleteShow();
							setTimeout("joms.photos.multiUpload.showThumbnail(); joms.photos.multiUpload.enableEditTitle();", 1000);							
						}
					},

					// Post init events, bound after the internal events
					init: {
						Refresh: function(up) {
							// Called when upload shim is moved
							joms.photos.multiUpload.log('[Refresh]');
						},

						StateChanged: function(up) {
							// Called when the state of the queue is changed
							joms.photos.multiUpload.log('[StateChanged]', up.state == plupload.STARTED ? "STARTED": "STOPPED");
						},

						QueueChanged: function(up) {
							// Called when the files in queue are changed by adding/removing files
							joms.photos.multiUpload.log('[QueueChanged]');
						},

						UploadProgress: function(up, file) {
							// Called while a file is being uploaded
							joms.photos.multiUpload.log('[UploadProgress]', 'File:', file, "Total:", up.total);
						},

						FilesAdded: function(up, files) {
							// Callced when files are added to queue
							joms.photos.multiUpload.log('[FilesAdded]');

							plupload.each(files, function(file) {
								joms.photos.multiUpload.log('  File:', file);
							});
						},

						FilesRemoved: function(up, files) {
							// Called when files where removed from queue
							joms.photos.multiUpload.log('[FilesRemoved]');

							plupload.each(files, function(file) {
								joms.photos.multiUpload.log('  File:', file);
							});
						},

						FileUploaded: function(up, file, info) {
							// Called when a file has finished uploading
							joms.photos.multiUpload.log('[FileUploaded] File:', file, "Info:", info);
						},

						ChunkUploaded: function(up, file, info) {
							// Called when a file chunk has finished uploading
							joms.photos.multiUpload.log('[ChunkUploaded] File:', file, "Info:", info);
						},

						Error: function(up, args) {
							// Called when a error has occured

							// Handle file specific error and general error
							if (args.file) {
								joms.photos.multiUpload.log('[error]', args, "File:", args.file);
							} else {
								joms.photos.multiUpload.log('[error]', args);
							}
						}
					}
				});
				
				//This part added checking for IE7 and will disable multiupload in IE7 or earlier
				if ((pluploader.pluploadQueue().runtime == 'html4') || (joms.jQuery.browser.msie && (parseInt(joms.jQuery.browser.version.substr(0,1)) <= 7)))
				{
					joms.photos.multiUpload.getBrowserNotSupport();
				}
				
				joms.jQuery('.plupload_button.plupload_start').hide();
				
				joms.jQuery("#cwin_close_btn").click(function() { 
					if (joms.photos.multiUpload.refreshNeeded)
					{						
						joms.photos.multiUpload.refreshBrowser();
					}
				});								
			},
			log: function () 
			{
				var str = "";

				plupload.each(arguments, function(arg) {
					var row = "";

					if (typeof(arg) != "string") {
						plupload.each(arg, function(value, key) {
							// Convert items in File objects to human readable form
							if (arg instanceof plupload.File) {
								// Convert status to human readable
								switch (value) {
									case plupload.QUEUED:
										value = 'QUEUED';
										break;

									case plupload.UPLOADING:
										value = 'UPLOADING';
										break;

									case plupload.FAILED:
										value = 'FAILED';
										break;

									case plupload.DONE:
										value = 'DONE';
										break;
								}
							}

							if (typeof(value) != "function") {
								row += (row ? ', ': '') + key + '=' + value;
							}
						});

						str += row + " ";
					} else { 
						str += arg + " ";
					}
				});

				joms.jQuery('#log').val(joms.jQuery('#log').val() + str + "\r\n");
			},
			getSelectedAlbumId: function() {
				if (typeof pluploader == 'undefined')
					return false;
				
				if (joms.jQuery('#album-name').length <= 0)
					return false;
				
				if (joms.jQuery('#album-name').attr('albumid') != undefined && parseInt(joms.jQuery('#album-name').attr('albumid')) > 0 && joms.jQuery("#new-album").css('display') == 'inline')
				{
					return joms.jQuery('#album-name').attr('albumid');
				}
				else
				{
					return joms.jQuery('#albumid').val();
				}
			},
			showThumbnail: function() {
				var uploader = pluploader.pluploadQueue();
				for (var i = 0; i < uploader.files.length; i++)
				{
					var imgNode = document.createElement('img');
					imgNode.id = 'plupload_'+uploader.files[i].id;
					imgNode.setAttribute('class', 'plupupload_thumbnail');
					imgNode.src = uploader.files[i].imgSrc;

					joms.jQuery('#'+uploader.files[i].id+'.plupload_done .plupload_file_name').append(imgNode);
				}
				
				// Show uploaded complete notice msg.
				var newAlbumName = joms.jQuery('#albumid option:selected').html();
				var msgToUse = joms.photos.multiUpload.uploadedCompleteMsg.replace('%1$s', '<strong>'+newAlbumName+'</strong>');
				joms.photos.multiUpload.displayNotice(msgToUse);
			},
			assignUploadUrl: function( albumId )
			{
				if (typeof pluploader == 'undefined')
					return false;
				
				var urlDefault = 'index.php?option=com_community&view=photos&task=multiUpload';
				joms.jQuery("#multi_uploader").pluploadQueue().settings.url = urlDefault + '&albumid='+albumId;
			},
			assignNewAlbum: function ( albumId, albumName ) {
				//	joms.jQuery('#album-name').attr('albumid', albumId);
				//	assignUploadUrl( albumId );
				if (typeof pluploader == 'undefined')
					return false;
				
				if (joms.jQuery('#albumid').length <= 0)
					return false;
				
				if (albumName != undefined) // User create new album
				{
					var newAlbumName = albumName;					
				
					var newOption = new Option(newAlbumName, albumId);			
					joms.jQuery('#albumid').append(newOption).val(albumId);				
					joms.photos.multiUpload.assignUploadUrl( albumId );
					joms.jQuery('#album-name').val('');
				}
				else // User select album from dropdown
				{
					joms.jQuery('#albumid').val(albumId);
					var newAlbumName = joms.jQuery('#albumid option:selected').html();
				}
				
				// Show Notice Album XXX Created. Photo(s) uploading...
				var msgToUse = joms.photos.multiUpload.uploadingCreateMsg.replace('%1$s', '<strong>'+newAlbumName+'</strong>');
				joms.photos.multiUpload.displayNotice(msgToUse);				
				
				joms.photos.multiUpload.showExistingAlbum();
				
				setTimeout("joms.jQuery('#multi_uploader').pluploadQueue().start()", 500);
			},			
			showExistingAlbum: function() {
				joms.jQuery("#select-album").css('display','inline');
				joms.jQuery("#new-album").hide();
				joms.jQuery("#newalbum").hide();
				if(pluploader.pluploadQueue().files.length>0){
				    joms.jQuery('.plupload_start').removeClass('plupload_disabled');
				 }


			},
			createNewAlbum: function() {
				joms.jQuery("#select-album").hide();

				joms.jQuery("#newalbum").show();
				joms.jQuery("#new-album").css('display','inline');
			},			
			startUploading: function() {
				joms.jQuery("#multi_uploader").pluploadQueue().start();
			},
			stopUploading: function() {
				joms.jQuery("#multi_uploader").pluploadQueue().stop();
			},			
			goToAlbum: function( goUrl ) {
				document.location.href = goUrl;
			},
			getBrowserNotSupport: function() {
				jax.call('community' , 'photos,ajaxGotoOldUpload', joms.photos.multiUpload.getSelectedAlbumId(),joms.photos.multiUpload.groupid );
			},			
			goToOldUpload: function( goUrl ) {
				document.location.href = goUrl;
			},
			refreshBrowser: function() {
				window.location.reload();
			},
			updateLabel:function(labels) {
				labels = joms.jQuery.parseJSON(labels);
				var labelToUpdate = ['filename', 'size', 'status', 'filedrag', 'addfiles', 'startupload', 'stopupload'];
				
				for (var i = 0; i < labelToUpdate.length; i++)
				{
					if (typeof labels[labelToUpdate[i]] != 'undefined' && labels[labelToUpdate[i]] != '')
					{
						joms.photos.multiUpload.label[labelToUpdate[i]] = labels[labelToUpdate[i]];
					}
				}
			},
			enableEditTitle: function() {		
				if (joms.jQuery('.plupload_file_name span').length > 0)
				{
					joms.jQuery('.plupload_file_name span').unbind('click');
					joms.jQuery('.plupload_file_name span').bind('click', function(event) {joms.photos.multiUpload.editTitle(event)});
				}
			},
			// Enable changing photo title
			editTitle: function( event ) {
				joms.jQuery('.plupload_file_name span').unbind('click');
				var obj = (event.target) ? event.target : event.which;				
				var fileId = joms.jQuery(obj).parent().parent().attr('id');
				
				var editInput = '<input type="text" target="' + fileId + '" name="photoTitle" value="'+joms.jQuery(obj).html()+'" />';
				joms.jQuery(obj).html(editInput);
				
				joms.jQuery(obj).find('input').unbind("keypress").unbind("focusout");
				joms.jQuery(obj).find('input').bind('keypress', function(event) {
					keyCode = event.keyCode;
					if (keyCode == 13)
					{
						joms.photos.multiUpload.saveCaption(this, joms.jQuery(this).parent());
						joms.jQuery('.plupload_file_name span').bind('click', function(event) {joms.photos.multiUpload.editTitle(event)});
					}
				});
				joms.jQuery(obj).find('input').bind('focusout', function() {
					joms.photos.multiUpload.saveCaption(this, joms.jQuery(this).parent());
					joms.jQuery('.plupload_file_name span').bind('click', function(event) {joms.photos.multiUpload.editTitle(event)});
				});
			},
			saveCaption: function(inputObj, spanObj) {					
				var fileId = joms.jQuery(inputObj).attr('target');
				var currentFile = pluploader.pluploadQueue().getFile(fileId);
				
				var value = joms.jQuery(inputObj).val();
				currentFile.name = value;
				var saveValue = encodeURIComponent(value);
				
				jax.call("community", "photos,ajaxSaveCaption", currentFile.photoId, saveValue, false);
				joms.jQuery(spanObj).html(value);
			},
			displayNotice: function(msg) {
				joms.jQuery('#'+joms.photos.multiUpload.noticeDivId).html(msg);
			},
			hideShowInput: function(hide)
			{
				if (hide != undefined && hide === true)
				{
					joms.jQuery('#upload-header').hide();
					joms.jQuery('.custom_plupload_buttons').hide();
				}
				else
				{
					joms.jQuery('#upload-header').show();
					joms.jQuery('.custom_plupload_buttons').show();
					joms.jQuery('.plupload_button.plupload_start').hide();
				}
			},
			uploadCompleteShow: function() {				
				joms.jQuery('div#upload-footer').show();
				joms.ajax.call( 'photos,ajaxUpdateCounter', [joms.photos.multiUpload.getSelectedAlbumId()]);
			},
			removeQueue: function(fileId)
			{
				var fileQueue = pluploader.pluploadQueue().files;
				var fileToDelete = [];
				var count = 0;
				for (var i = 0; i < fileQueue.length; i++)
				{
					if (fileQueue[i].id == fileId)
					{
						fileToDelete[count] = fileQueue[i];
						count++;
					}
					else
					{
						if (count > 0)
						{
							fileToDelete[count] = fileQueue[i];
							count++;
						}
					}
				}
				
				for (var i = 0; i < fileToDelete.length; i++)
				{
					pluploader.pluploadQueue().removeFile(fileToDelete[i]);
					fileToDelete[i] = '';
				}
			}
		},
		uploadAvatar : function(type,id){
			var jaxCall = jax.call("community","photos,ajaxUploadAvatar",type,id);
			cWindowShow(jaxCall, '', 450, 100);
		},
		ajaxUpload : function(type,id){
			var url = joms.jQuery('#jsform-uploadavatar').prop('action');

			joms.jQuery.ajaxFileUpload
			(
				{
					url:url,
					secureuri:false,
					fileElementId:'filedata',
                                        dataType:'json',
					beforeSend:function(){},
					complete:function(){},
					success:function(data,status)
					{
						if(data.error=='true')
						{
							joms.jQuery('span.error').remove();
							joms.jQuery( '#avatar-upload' ).prepend( '<span class="error">' + data.msg + '</span>');
							return false;
						}
						else
						{
							url = data.msg;
							joms.jQuery('span.error').remove();
							var oldH = joms.jQuery('#cWindowContent').height();
							var oldAvatar = '';
							var html = '';

							joms.jQuery('#thumb-crop').css('min-height',0);
							joms.jQuery('#large-avatar-pic').prop('src',url);
							joms.jQuery('#thumb-hold  >img').prop('src',url);
							joms.jQuery('div.status-anchor img').prop('src',url);
							joms.jQuery('#large-avatar-pic').load(function (){

								var newH = joms.jQuery('#cWindowContent').height();

								if(oldH > newH){
										var  valueH = '-='+ (oldH-newH);
								}else if(oldH < newH){
										var  valueH = '+='+ (newH-oldH);
								}

							   joms.jQuery('#cwin_ml , #cwin_mr, #cWindowContentOuter, #cWindowContentWrap').animate({'height' : valueH});
							   joms.photos.ajaxImgSelect();

							   delete oldH;
							   delete newH;
							   delete valueH;
							});
							switch(type)
							{
								case 'event':
									oldAvatar = joms.jQuery('#community-event-avatar > img').prop('src');

									joms.jQuery('#community-event-avatar > img').prop('src',url);

									if(oldAvatar.search('assets/event.png') > -1){
											jax.call('community', 'photos,ajaxUploadAvatar',type,id);
									}

									break;
								case 'group':
									oldAvatar = joms.jQuery('#community-group-avatar > img').prop('src');

									joms.jQuery('#community-group-avatar > img').prop('src',url);

									if(oldAvatar.search('assets/group.png') > -1){
											jax.call('community', 'photos,ajaxUploadAvatar',type,id);
									}
									break;
								case 'profile':
									oldAvatar = joms.jQuery('.profile-avatar > img').prop('src');

									joms.jQuery('.profile-avatar > img').prop('src',url);

									if (data.info.length != 0)
									{
											var thumbUrl = data.info;
											// Update Status Bar Thumbnail
											joms.jQuery('.status-author > img').prop('src',thumbUrl);
											// Update News feed thumbnail
											joms.jQuery('.newsfeed-avatar img:[author="'+id+'"]').prop('src',thumbUrl);
									}
									if(oldAvatar.search('assets/user.png') > -1){
											jax.call('community', 'photos,ajaxUploadAvatar',type,id);
									}
									break;
							}
						}
					}
				}
			)
			return false;
		},
		ajaxImgSelect : function(){
		    var img = document.getElementById('large-avatar-pic');
		    var imgH = joms.jQuery(img).height();
			var imgW = 160;
			if(imgH < 160){imgW = imgH;}
			if(imgH > 160){imgH = 160;}

			// Create select object
			joms.jQuery('#large-avatar-pic').imgAreaSelect(
					{maxWidth: 160, maxHeight: 160, handles: true ,aspectRatio: '1:1',
					  x1: 0, y1: 0, x2: imgW, y2: imgH,
					  show: false, hide: true, enable: false,
					  parent:'#cWindow',
					  minHeight:64, minWidth:64,
					  onInit:joms.photos.previewThumb,
					  onSelectChange:joms.photos.previewThumb
					}
			);

			var ias = joms.jQuery('#large-avatar-pic').imgAreaSelect({instance: true});
				ias.setOptions({show: true, hide: false, enable:true});
				ias.update();
		},
		saveThumb : function (type,id) {
			var ias = joms.jQuery('#large-avatar-pic').imgAreaSelect({instance: true});
				var obj = ias.getSelection();
				jax.call('community', 'photos,ajaxUpdateThumbnail', type, id, obj.x1, obj.y1, obj.width, obj.height );
		},
		previewThumb : function (img,selection){
				var scaleX = 64 / (selection.width || 1);
				var scaleY = 64 / (selection.height || 1);

			   joms.jQuery('#thumb-hold  >img').css(
						  {
							  width: Math.round(scaleX * 160) + 'px',
							  height: Math.round(scaleY * this.height) + 'px',
							  marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
							  marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
						  }
					  );
		},
		ajaxRemoveImgSelect : function(){

			joms.jQuery('.imgareaselect-selection').parent().remove()

			joms.jQuery('.imgareaselect-outer').remove()
		},
		/*
		 * Since version 2.4
		 * for Photo slider
		 */
		photoSlider : {
			moveSpace : 4,
			partialOpacity: 0.6,
			fullOpacity: 1,
			intervalTime: 30,
			timer: '',
			controlObj: '',
			event: '',
			parentElem: '',
			stopAnimeId: '',
			img_thumbId: 'photoSlider_thumb',
			img_thumbClass: 'currentView',
			thumbnail: {
				width: 0,
				height:0
			},
			/*
			 * @param1 String sliding html element id
			 * @param2 String classname of items inside sliding html element (use to stop sliding when mouse focus on item)
			 * @param3 JSON options to override sliding configuration
			 */
			_init: function(elementId, stopAnimeId, options) {
				if (options != undefined)
				{
					joms.photos.photoSlider.updateConfig(options);
				}
				var objElement = joms.jQuery('#'+elementId);
				joms.photos.photoSlider.controlObj = objElement;
				joms.photos.photoSlider.parentElem = joms.jQuery(objElement.parent());
				
				if (stopAnimeId != undefined && stopAnimeId != '')
				{					
					joms.photos.photoSlider.stopAnimeId = stopAnimeId;
					joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).css('opacity', joms.photos.photoSlider.partialOpacity);
					var index = joms.gallery.getPlaylistIndex(joms.gallery.currentPhoto().id);
					joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).eq(index).css('opacity',joms.photos.photoSlider.fullOpacity);
				}
				
				joms.photos.photoSlider.parentElem.bind({
					'mouseover': function( event ) { 
						var elemWidth = joms.photos.photoSlider.getValue(joms.jQuery(this).css('width'));
						var elemHeight = joms.photos.photoSlider.getValue(joms.jQuery(this).css('height'));
						var offset = joms.jQuery(this).offset();

						if ((event.pageY > (offset.top + elemHeight * 0.1)) && (event.pageY < (offset.top + elemHeight * 0.9)))
						{	
							joms.photos.photoSlider.moveContent( event ); 
						}
					},
					'mousemove': function( event ) {joms.photos.photoSlider.updateMousePos( event );},
					'mouseout': function( event ) {joms.photos.photoSlider.reset( event );} 
				});
				
				// Resize according to parent's width to avoid breaking in certain browser styling
				var customwidth = joms.photos.photoSlider.parentElem.parent().width();
				joms.jQuery(".photo_slider").css('width', customwidth+'px');
				
				joms.photos.photoSlider.switchPhoto();
			},
			updateConfig: function(options) {
				var availableOption = ['moveSpace', 'partialOpacity', 'fullOpacity', 'intervalTime'];
				
				if (typeof options == 'object')
				{
					for (var i = 0; i < availableOption.length; i++)
					{
						if (options[availableOption[i]] != undefined && parseInt(options[availableOption[i]]) != 'NaN' && options[availableOption[i]] > 0)
						{
							joms.photos.photoSlider[availableOption[i]] = options[availableOption[i]];
						}	
					}
				}
			},
			moveContent: function(event) {
				joms.photos.photoSlider.parentElem.unbind("mouseover");
				
				joms.photos.photoSlider.timer = setInterval("joms.photos.photoSlider.animate()", joms.photos.photoSlider.intervalTime); 

				if (joms.photos.photoSlider.stopAnimeId != '' && joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).length > 0)
				{					
					var thumbnailItem = joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).eq(0);
					joms.photos.photoSlider.thumbnail.width = joms.photos.photoSlider.getValue(thumbnailItem.css('width'));
					joms.photos.photoSlider.thumbnail.height = joms.photos.photoSlider.getValue(thumbnailItem.css('height'));
					
					/*joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).unbind("mouseover");
					joms.jQuery('.'+joms.photos.photoSlider.stopAnimeId).bind("mouseover", function() {
						joms.jQuery(this).unbind("mousemove");
						joms.jQuery(this).bind("mousemove", function( event ) {
							//if mouse in middle of the thumbnail, clear the interval, and set the interval if the mouse is out from the thumbnail
							var elemWidth = joms.photos.photoSlider.thumbnail.width;
							var elemHeight = joms.photos.photoSlider.thumbnail.height;
							var offset = joms.jQuery(this).offset();
							if ((event.pageX > (offset.left + elemWidth * 0.3)) && (event.pageX < (offset.left + elemWidth * 0.7))
							&& (event.pageY > (offset.top + elemHeight * 0.3)) && (event.pageY < (offset.top + elemHeight * 0.7)))
							{	
								joms.photos.photoSlider.stop();
								joms.jQuery(this).css('opacity',joms.photos.photoSlider.fullOpacity);
								joms.jQuery(this).bind("mouseout", function () {
									joms.jQuery(this).unbind("mouseout").unbind("mousemove");
								});
							}
						});
					});*/
				}
			}, 
			animate: function()	{							
				// Disable photo slider in photo tag mode
				if (joms.jQuery('#startTagMode').length > 0 && joms.jQuery('#startTagMode').css('display') == 'none')
				{
					joms.photos.photoSlider.stop();
					return false;
				}
				
				var leftPos = joms.photos.photoSlider.getValue(joms.photos.photoSlider.controlObj.css('left'));
				var sliderWidth = joms.photos.photoSlider.getValue(joms.photos.photoSlider.controlObj.css('width'));
				var elemWidth = joms.photos.photoSlider.getValue(joms.photos.photoSlider.parentElem.css('width'));

				var midVal = elemWidth / 2;

				if (joms.photos.photoSlider.getMouseXPosition() > midVal)
				{
					if ((( leftPos + sliderWidth ) - elemWidth) >= 0) //end of div		
					{
						joms.photos.photoSlider.controlObj.css('left', (leftPos - joms.photos.photoSlider.moveSpace) + 'px');
					}
				}
				else
				{
					if (leftPos < 0)
					{
						joms.photos.photoSlider.controlObj.css('left', (leftPos + joms.photos.photoSlider.moveSpace) + 'px');
					}
				}
			},
			updateMousePos: function(event) {
				joms.photos.photoSlider.event = event; 	
			},
			getMouseXPosition: function() {
				return joms.photos.photoSlider.event.pageX - joms.photos.photoSlider.parentElem.offset().left;	
			},
			getMouseYPosition: function() {
				return joms.photos.photoSlider.event.pageY - joms.photos.photoSlider.parentElem.offset().top;		
			},
			getValue: function(strVal) {
				if (strVal == '' || strVal == 'auto')
				{
					intVal = 0;
				}
				else
				{
					intVal = parseInt(strVal.replace('px',''));
					
					if (typeof intVal != 'number' || intVal == 'NaN')
					{
						intVal = 0;
					}
				}

				return intVal;
			},
			// display image in photo canvas when thumbnail being clicked
			viewImage: function(photoid) {				
				// Disable switch photo in photo tag mode
				if (joms.jQuery('#startTagMode').length > 0 && joms.jQuery('#startTagMode').css('display') == 'none')
				{
					return false;
				}
				
				if (joms.gallery != undefined && jsPlaylist != undefined)
				{
					joms.photos.photoSlider.stop(); 
				
					joms.gallery.displayPhoto(jsPlaylist.photos[joms.gallery.getPlaylistIndex( photoid )]); 
					joms.photos.photoSlider.switchPhoto();
				}				
			},
			// switch the highlight when user click on next/prev navigator
			switchPhoto: function() {
				if (joms.photos.photoSlider.controlObj == undefined || joms.photos.photoSlider.controlObj == '')
				{
					return false;
				}
				
				var url = document.location.href;
				if (url.match('#') && url.split('#')[1].match('photoid='))
				{
					url = url.split('#')[1];
					if (url.match('&'))
					{
						url = url.split('&')[0];
					}						
					var currentPhotoId = joms.photos.photoSlider.img_thumbId + url.split('=')[1];
				}
				else
				{
					var currentPhotoId = joms.photos.photoSlider.controlObj.find(img).eq(0).attr('id');
				}
				
				joms.photos.photoSlider.controlObj.find('img').removeClass(joms.photos.photoSlider.img_thumbClass);
				joms.photos.photoSlider.controlObj.find('img[id="'+currentPhotoId+'"]').addClass(joms.photos.photoSlider.img_thumbClass);
				
				joms.jQuery('.image_thumb').css('opacity',joms.photos.photoSlider.partialOpacity);
				joms.jQuery('img#'+currentPhotoId).css('opacity',joms.photos.photoSlider.fullOpacity);
			},
			// use for updating thumbnail when there is photo rotation
			updateThumb: function(id, thumbSrc) {
				var finder = 'img[id="'+joms.photos.photoSlider.img_thumbId+id+'"]';
				if (joms.photos.photoSlider.controlObj.find(finder).length == 1)
				{
					joms.photos.photoSlider.controlObj.find(finder).attr('src', thumbSrc);
				}
			},
			// use for updating photoslider for photo removal
			removeThumb: function() {				
				joms.photos.photoSlider.controlObj.find('img[class*="'+joms.photos.photoSlider.img_thumbClass+'"]').remove();
				var numItems = joms.photos.photoSlider.controlObj.find('img').length
				var itemSpace = 79;
				joms.photos.photoSlider.controlObj.css('width',  (numItems * itemSpace) +'px');
			},
			// avoid stopping sliding when mouse falls between the gap among the images thumbnails
			reset: function( event ) {
				var offset = joms.photos.photoSlider.parentElem.offset();

				// Prevent propagation effect and messed up the animation
				if ((event.pageX > offset.left && event.pageX < (offset.left + joms.photos.photoSlider.getValue(joms.photos.photoSlider.parentElem.css('width')))) 
					&& (event.pageY > offset.top && event.pageY < (offset.top + joms.photos.photoSlider.getValue(joms.photos.photoSlider.parentElem.css('height')))))
				{			
					return false;
				}

				joms.photos.photoSlider.stop();
			},
			// stop the sliding anitmation
			stop: function() {
				clearInterval(joms.photos.photoSlider.timer);		
				joms.photos.photoSlider.timer = '';
				joms.photos.photoSlider.event = '';
				joms.photos.photoSlider.parentElem.unbind("mouseover").unbind("mousemove").unbind("mouseout");
				joms.photos.photoSlider._init(joms.photos.photoSlider.controlObj.attr('id'), joms.photos.photoSlider.stopAnimeId);
			}
		}
	},
	tooltips: {
		currentJaxCall:'',
		currentJElement: '',
		currentTimeout: '',
		minitipStyle: {top:0, left:0, width:500, height: 100},
		showDialog: function(classCheck) {
			if (joms.tooltips.currentJElement == '' || joms.tooltips.currentJaxCall == '')
			{
				if (joms.tooltips.currentTimeout != '')
					clearTimeout(joms.tooltips.currentTimeout);
				return false;
			}
			
			if (joms.tooltips.currentJElement.hasClass(classCheck))
			{
				//cMinitipShow(joms.tooltips.currentJaxCall, '', joms.tooltips.minitipStyle.width, 100);
				joms.minitip._init(joms.tooltips.currentJaxCall, '', joms.tooltips.minitipStyle.width, joms.tooltips.minitipStyle.height);
				joms.tooltips.repositionMinitip();
				joms.jQuery('#'+joms.minitip.id.canvas).addClass(classCheck);
				joms.jQuery('#'+joms.minitip.id.canvas).attr('currentMinitip', joms.tooltips.currentJElement.attr('id'));
			}				
		},
		addMinitipContent: function(html) {
			if (joms.jQuery('#'+joms.minitip.id.canvas).length > 0)
			{	
				//cMinitipAddContent(html);
				joms.tooltips.repositionMinitip();
				joms.minitip.addContent(html);
			}
		},
		repositionMinitip: function() {
			var topToUse = joms.tooltips.currentJElement.offset().top - (joms.jQuery('#'+joms.minitip.id.canvas).height() + 20);
			if (joms.jQuery.browser.msie && joms.jQuery.browser.version.substr(0,1) <= 7)
			{
				var leftToUse = joms.tooltips.currentJElement.offset().left - 65;
			}
			else
			{
				var leftToUse = joms.tooltips.currentJElement.offset().left + 30;
			}			

			joms.jQuery('#'+joms.minitip.id.canvas).css(
			{
				'top'   : topToUse,
				'left'  : leftToUse
			});
		},
		setDelay: function(jElement, jaxCall, classCheck, widthToUse, heightToUse, event) {
			if (jElement.attr('id') == undefined)
			{
				jElement.attr('id', 'temp_minitooltip'+Math.random().toString());
			}
			
			if (joms.jQuery('#'+joms.minitip.id.canvas).length > 0)
			{
				if (jElement.attr('id') == joms.jQuery('#'+joms.minitip.id.canvas).attr('currentMinitip'))
				{
					return false;
				}
			}
			
			if (widthToUse != undefined && parseInt(widthToUse) > 0)
			{
				joms.tooltips.minitipStyle.width = widthToUse;
			}
			
			if (heightToUse != undefined && parseInt(heightToUse) > 0)
			{
				joms.tooltips.minitipStyle.height = heightToUse;
			}
			
			joms.tooltips.currentJElement = jElement;
			
			joms.tooltips.currentJElement.bind("mouseout", function() {
				joms.jQuery(this).removeClass(classCheck);
				clearTimeout(joms.tooltips.currentTimeout);
				joms.tooltips.reset();
				joms.minitip.hide();
			});
			
			joms.tooltips.currentJElement.addClass(classCheck);
			joms.tooltips.currentJaxCall = jaxCall;
			joms.tooltips.currentTimeout = setTimeout("joms.tooltips.showDialog('" + classCheck + "')", 500);
		},
		reset: function() {
			joms.tooltips.currentJaxCall = '';
			//joms.tooltips.currentJElement = '';
			joms.tooltips.currentTimeout = '';	
		}
	},
	friends: {
		saveTag: function(){
			var formVars = jax.getFormValues('tagsForm');
			jax.call("community", "friends,ajaxFriendTagSave", formVars);
			return false;
		},
		saveGroup: function(userid) {
			if(document.getElementById('newtag').value == ''){
			    window.alert('TPL_DB_INVALIDTAG');
			}else{
				jax.call("community", "friends,ajaxAddGroup",userid,joms.jQuery('#newtag').val());
			}
		},
		cancelRequest: function( friendsId ){
			var ajaxCall = 'jax.call("community" , "friends,ajaxCancelRequest" , "' + friendsId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		connect: function( friendid ){
			var ajaxCall = 'jax.call("community", "friends,ajaxConnect", '+friendid+')';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		addNow: function(){
		 	var formVars = jax.getFormValues('addfriend');
		 	jax.call("community", "friends,ajaxSaveFriend",formVars);
		 	return false;
		},
		confirmFriendRemoval: function( friendId ){

			var ajaxCall = function()
			{
				jax.call("community", "friends,ajaxConfirmFriendRemoval", friendId);	
			};

			cWindowShow(ajaxCall, '', 450, 80, 'warning');
		},
		remove: function( friendId ) {
			var blockFriend = joms.jQuery('#cWindow input[name=block]').attr('checked');

			var ajaxCall;
			if (blockFriend)
			{
				ajaxCall = function()
				{
					jax.call("community", "friends,ajaxBlockFriend", friendId);
				};
			} else {
				ajaxCall = function()
				{
					jax.call("community", "friends,ajaxRemoveFriend", friendId);
				};
			}

			cWindowShow(ajaxCall, '', 450, 80, 'warning');
		},
		updateFriendList: function (friends,error) {
			currentFriends = '';
			noFriend = '';
			if(joms.jQuery('#community-invitation-list').hasClass('load-more')){
				currentFriends = joms.jQuery('#community-invitation-list').html();
			} 
			else {
				//check empty result
				if( joms.jQuery.trim(friends) == ''){
					noFriend = error;
				}
			}
			newFriends = currentFriends + friends + noFriend;
			joms.jQuery('#community-invitation-list').html(newFriends);		
			//removed selected friend form the list
			joms.invitation.filterMember();	
			//set result list as active
			joms.jQuery('.cInvitationTab #ctab-result').click();
		},
		loadFriend: function (name,callback,cid,limitstart,limit){
			if(joms.jQuery('#community-invitation-list').hasClass('load-more')){
				joms.jQuery('#community-invitation-list').removeClass('load-more');
			}
			jax.call('community','system,ajaxLoadFriendsList',name,callback,cid,limitstart,limit);
		},
		loadMoreFriend: function (callback,cid,limitstart,limit){
			name = joms.jQuery('#friend-search-filter').val();
			joms.jQuery('#community-invitation-list').addClass('load-more');
			jax.call('community','system,ajaxLoadFriendsList',name,callback,cid,limitstart,limit);			
		},
		showForm: function( users , callback , cid , displayFriends ){
			var ajaxCall	= 'jax.call("community", "system,ajaxShowFriendsForm","' + users + '","' + callback + '","' + cid + '","' + displayFriends + '")';
			var height		= 520;

			height			= displayFriends != "0" ? height : height - 108;
			height			= height - 108;

			cWindowShow(ajaxCall, '', 550, height );
		},
		selectFriends: function() {
			//reset old list
			//joms.jQuery('#inbox-selected-to').html('');
			joms.jQuery("#community-invited-list li").each( function(index) {
				var friendId = joms.jQuery(this).attr('id');
				if(joms.jQuery('#inbox-selected-to #'+friendId).length > 0 ) {
				} else {
					var friend = joms.jQuery(this).clone();
					friend.appendTo('#inbox-selected-to');				
				}
			});
			joms.jQuery('.invitation-check label').empty();
			cWindowHide();
		}
	},
	messaging: {
		loadComposeWindow: function(userid){
			var ajaxCall = 'jax.call("community", "inbox,ajaxCompose", '+userid+')';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		sendCompleted: function(){
			joms_message_sending = false;
		},
		send: function(){
			if(joms_message_sending) return false;
			joms_message_sending = true;
			var formVars = jax.getFormValues('writeMessageForm');
			jax.call("community", "inbox,ajaxSend", formVars);
			return false;
		},
		confirmDeleteMarked: function(task){
		    var ajaxCall = 'jax.call("community", "inbox,ajaxDeleteMessages", "'+task+'")'
		    cWindowShow(ajaxCall, '', 450, 100);
		},
		deleteMarked: function(task){
		    joms.jQuery("#inbox-listing INPUT[type='checkbox']").each( function() {
			if ( joms.jQuery(this).attr('checked') ) {
			    if(task == 'inbox')
				jax.call( 'community', 'inbox,ajaxRemoveFullMessages', joms.jQuery(this).attr('value'));
			    else
				jax.call( 'community', 'inbox,ajaxRemoveSentMessages', joms.jQuery(this).attr('value'));
			}
		    });
		    return false;
		}
	},
	walls: {
		insertOrder: 'prepend',
		add: function ( uniqueId, addFunc ){

			jax.loadingFunction = function()
			{
				joms.jQuery('#wall-message,#wall-submit').attr('disabled', true);
			}

			jax.doneLoadingFunction = function()
			{
				joms.jQuery('#wall-message,#wall-submit').attr('disabled', false);
			};

			if(typeof getCacheId == 'function')
			{
				cache_id = getCacheId();
			}
			else
			{
				cache_id = "";
			}

			jax.call('community', addFunc, joms.jQuery('#wall-message').val(), uniqueId, cache_id);
		},
		insert: function( html ){
			joms.jQuery('#wall-message').val('');
			if(joms.walls.insertOrder == 'prepend'){
				joms.jQuery('#wallContent').prepend(html);
			} else{
				// append
				joms.jQuery('#wallContent .wallComments:last').after(html);
			}
		},
		remove: function( type , wallId , contentId ){
			if(confirm('Are you sure you want to delete this wall?'))
			{
				jax.call('community' , type + ',ajaxRemoveWall' , wallId , contentId );
				joms.jQuery('#wall_' + wallId ).fadeOut('normal', function(){joms.jQuery(this).remove()});

				// Process ajax calls
			}
		},
		update: function( id , message ){
			//Hide popups
			cWindowHide();

			//Update the existing html codes
			joms.jQuery( '#wall_' + id ).replaceWith( message );
		},
		save: function( id , editableFunc ){
			jax.call('community' , 'system,ajaxUpdateWall' , id , joms.jQuery('#wall-edit-' + id).val() , editableFunc );
		},
		edit: function( id , permissionFunc ){

			if( joms.jQuery('#wall-edit-' + id ).val() != null )
			{
				joms.jQuery('#wall-message-'+ id).show();
				joms.jQuery('#wall-edit-container-'+ id).remove();
			}
			else
			{
				// Hide current message
				joms.jQuery('#wall-message-' + id ).hide();
				joms.jQuery('#wall_' + id + ' div.content').prepend( '<span id="wall-edit-container-' + id + '"></span>').prepend('<div class="loading" style="display:block;float: left;"></div>');

				jax.call('community' , 'system,ajaxEditWall' , id , permissionFunc );
				joms.utils.textAreaWidth('#wall-edit-' + id );
				joms.utils.autogrow('#wall-edit-' + id);
			}
		},
		more: function(){
			// Pass the necessary params to ajaxGetOlderWalls
			var groupId			= joms.jQuery('#wall-groupId').val();
			var discussionId	= joms.jQuery('#wall-discussionId').val();
			var limitStart		= joms.jQuery('#wall-limitStart').val();
			
			// Show loading image
			joms.jQuery( '#wall-more .more-wall-text' ).hide();
			joms.jQuery( '#wall-more .loading' ).show().css( 'float' , 'none' ).css( 'margin' , '5px 5px 0 180px');
			jax.call( 'community' , 'system,ajaxGetOlderWalls' , groupId, discussionId, limitStart );
		},
		append: function( html ){
			//joms.jQuery("#wallContent div.wallComments:last").css('border-bottom', '1px dotted #333');
			joms.jQuery( '#wall-more,#wall-groupId,#wall-discussionId,#wall-limitStart' ).remove();
			joms.jQuery( '#wall-containter' ).append( html );
		},
		prepend: function( html ){
			joms.jQuery( '#wall-more' ).remove();
			joms.jQuery( '#wall-groupId' ).remove();
			joms.jQuery( '#wall-discussionId' ).remove();
			joms.jQuery( '#wall-limitStart' ).remove();
			joms.jQuery( '#wall-containter' ).prepend( html );
		},
		showVideoWindow: function ( videoId ){   
			var ajaxCall	= 'jax.call("community" , "videos,ajaxShowVideoWindow", "' + videoId + '");';
			cWindowShow(ajaxCall, '', 640, 360);
		}
	},
	toolbar: {
		timeout: 500,
		closetimer: 0,
		ddmenuitem: 0,
		open: function( id ){

			if ( joms.jQuery('#'+id).length > 0 ) {
				// cancel close timer
				joms.toolbar.cancelclosetime();

				// close old layer
				if(joms.toolbar.ddmenuitem)
				{
					joms.toolbar.ddmenuitem.style.visibility = 'hidden';
				}

				// get new layer and show it
				joms.toolbar.ddmenuitem = document.getElementById(id);
				joms.toolbar.ddmenuitem.style.visibility = 'visible';
			}
		},
		close: function(){
			if(joms.toolbar.ddmenuitem)
			{
				joms.toolbar.ddmenuitem.style.visibility = 'hidden';
			}
		},
		closetime: function(){
			joms.toolbar.closetimer	= window.setTimeout( joms.toolbar.close , joms.toolbar.timeout );
		},
		cancelclosetime: function(){
			if( joms.toolbar.closetimer )
			{
				window.clearTimeout( joms.toolbar.closetimer );
				joms.toolbar.closetimer = null;
			}
		}
	},
	registrations:{
		showTermsWindow: function( fb ){
                        var ajaxCall = 'jax.call("community", "register,ajaxShowTnc", "'+ fb +'")';
			cWindowShow(ajaxCall, this.windowTitle , 600, 350);
		},
		authenticate: function(){
			jax.call("community", "register,ajaxGenerateAuthKey");
		},
		authenticateAssign: function(){
			jax.call("community", "register,ajaxAssignAuthKey");
		},
		assignAuthKey: function(fname, lblname, authkey){
			eval("document.forms['" + fname + "'].elements['" + lblname + "'].value = '" + authkey + "';");
		},
		showWarning: function(message, title) {
			cWindowShow('joms.jQuery(\'#cWindowContent\').html(\''+message+'\')' , title , 450 , 200 , 'warning');
		}
	},
	
	miniwall:{
	  initialize: function() {
	    //alert('init');
	    joms.jQuery('.wall-coc-item').hover(
	      function() {       // hover in function
	        joms.jQuery(this).find('.wall-coc-remove-link').stop(true, true).fadeIn('fast');
	      },
	      function () {      // hover out function
	        joms.jQuery(this).find('.wall-coc-remove-link').stop(true, true).fadeOut('fast');
	      }
	    );
	  },
		add: function(id){
			var cmt = joms.jQuery('#wall-cmt-'+ id +' textarea').val();
			cmt = joms.jQuery.trim(cmt);
			if(cmt.length > 0) 
			{
				joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-action.add').attr('disabled', true);
				joms.jQuery('#wall-cmt-'+ id +' .wall-coc-errors').hide();
				
				jax.loadingFunction = function() {
				  joms.jQuery('#wall-cmt-'+ id +' textarea').attr('disabled', true);
					
					joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-actions').append('<em class="wall-cmt-loading">Posting...</em>');
					jax.loadingFunction = function() {}				// clear the callbacks after each call
				};
				
				jax.doneLoadingFunction = function() {
					// joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-actions em').html('Comment posted!');
					// setTimeout("joms.jQuery('#wall-cmt-"+ id +" .wall-coc-form-actions em').fadeOut()", 800);
					joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-actions').find('em').remove();
					joms.jQuery('#wall-cmt-'+ id +' textarea').attr('disabled', false).val('');
					joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-action.add').attr('disabled', false);
					
					// update comment count
					cmtCountObj = joms.jQuery('#wall-cmt-'+id).parent().parent().find('.wall-cmt-count');
					curCmtCount = parseInt(cmtCountObj.html());
					cmtCountObj.parent().fadeOut('fast', function() {
              // plus one and update the value again
              cmtCountObj.html(curCmtCount + 1);
              cmtCountObj.parent().fadeIn('fast');
          });
					joms.miniwall.initialize();
					jax.doneLoadingFunction = function() {}			// clear the callbacks after each call
				};
				
				jax.call("community", "system,ajaxStreamAddComment", id, cmt);
			}
		},
		insert: function(id, text){
			// Form must be there, if a user cannot comment
			joms.jQuery('#wall-cmt-'+ id +' .wallform').before(text);
			// comment with zero comment could will have this class to hide the whole
			// comment area, remove it after we insert a new comment
			joms.jQuery('#wall-cmt-'+ id +' .wallnone').removeClass('wallnone');
			joms.miniwall.cancel(id);
			joms.miniwall.initialize();
		},
		loadall: function(id, text){
			// remove all element first
			joms.jQuery('#wall-cmt-'+id+' .wall-coc-item').remove();

			// replace the 'more' link with proper content
			joms.jQuery('#wall-cmt-'+id+' .wallmore').replaceWith(text);
			joms.miniwall.initialize();
		},
		cancel: function(id){
			joms.jQuery('#wall-cmt-'+ id +' textarea').val('');
			//joms.jQuery('#wall-cmt-'+ id +' .show-cmt').show();
			joms.jQuery('#wall-cmt-'+ id +' .wall-coc-errors').hide();
			joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-action.add').removeAttr('disabled');
			joms.jQuery('#wall-cmt-'+ id +' form').hide();
			joms.jQuery('#wall-cmt-'+ id +' .wallnone').css('display', 'none');
			
			/**
			 * Modified by Adam Lim on 13 July 2011
			 * Added condition to verify if there is any comment posted
			 * will only show [Reply] if there is comment, hide everything otherwise
			 */
			if (joms.jQuery('#wall-cmt-'+ id +' .wall-coc-item').length <= 0)//test
			{
				joms.jQuery('#wall-cmt-'+ id + ' .cComment').addClass('wallnone');
				joms.jQuery('#wall-cmt-'+ id + ' .wallicon-like').removeClass('wallnone');
			}
			else
			{                   
				joms.jQuery('#wall-cmt-'+ id +' .show-cmt').show();
			}
		},
		remove: function(id){
		  
		  var cmtCountObj = joms.jQuery('#wall-'+id).parent().parent().parent().find('.wall-cmt-count');
		  
		  // change comment color to red while its being removed via ajax
		  jax.loadingFunction = function() {
          joms.jQuery('#wall-'+id)
            .css({backgroundColor: '#ffdddd'})
            .find('.wall-coc-remove-link').show().html('<em class="wall-cmt-loading wall-cmt-loading-inline">Removing...</em>');
          jax.loadingFunction = function() {}       // clear the callbacks after each call
        };
		  
      // setup callbacks after loading
      jax.doneLoadingFunction = function() {
          // find the currently shown value of the comment count
          var curCmtCount = parseInt(cmtCountObj.html());
          //alert(curCmtCount);
          if(curCmtCount > 0) {
            
            // if this is the last comment, remove the last  'Reply' link as well
            if(curCmtCount == 1) {
              joms.jQuery('#wall-'+id).parent().parent().find('.cComment:last').addClass('wallnone');
            }
            
            // flash the count so it looks more interesting
            cmtCountObj.parent().fadeOut('fast', function() {
              // minus one and update the value again
              cmtCountObj.html(curCmtCount - 1);
              cmtCountObj.parent().fadeIn('fast');
            });
          }
          // now remove the display of that particular comment
          joms.jQuery('#wall-'+id).fadeOut('slow', function() {
            joms.jQuery(this).remove();
          }).find('.wall-coc-remove-link').hide();
          joms.miniwall.initialize();
          jax.doneLoadingFunction = function() {}     // clear the callbacks after each call
      };
		  
			jax.call('community', 'system,ajaxStreamRemoveComment', id);
		},
		show: function(id){
			try{
			var w = joms.jQuery('#'+ id +' form').parent().width();

			joms.jQuery('#wall-cmt-'+ id +' .wall-coc-form-action.add').removeAttr('disabled');
			joms.jQuery('#wall-cmt-'+ id +' form').width(w).show();
			joms.jQuery('#wall-cmt-'+ id +' .show-cmt').hide();
			
			// The wall element is hidden if there is no comment at all, show it
			joms.jQuery('#wall-cmt-'+ id + ' .wallnone').removeClass('wallnone');
			
			// We need to remove all other textarea that were added by the autogrow function
			joms.jQuery('#wall-cmt-'+ id +' textarea:[name!="comment"]').remove();			
			
			// @todo: should only autogrow once.
			var textarea = joms.jQuery('#wall-cmt-'+ id +' textarea');
			if( !textarea.data('autogrow') )
			{
				joms.utils.textAreaWidth(textarea);
				joms.utils.autogrow(textarea);
				
				textarea.focus();
	
				textarea.blur(function(){
					if (joms.jQuery(this).val()=='') joms.miniwall.cancel(id);
				}).data('autogrow', true);
			}
			} catch(e){
				// donothing really
			}
			
		}
	},
		
	comments:{
		add: function(id){
			var cmt = joms.jQuery('#'+ id +' textarea').val();
			if(cmt != '') {
				joms.jQuery('#'+ id +' .wall-coc-form-action.add').attr('disabled', true);
				if(typeof getCacheId == 'function')
				{
					cache_id = getCacheId();
				}
				else
				{
					cache_id = "";
				}
				jax.call("community", "plugins,walls,ajaxAddComment", id, cmt, cache_id);
			}
		},
		insert: function(id, text){
			joms.jQuery('#'+ id +' form').before(text);
			joms.comments.cancel(id);
		},
		remove: function(obj){
			var cmtDiv = joms.jQuery(obj).parents('.cComment');
			var index  = joms.jQuery( cmtDiv ).index();
			try{console.log(index);} catch(err){}
			var parentId = joms.jQuery(obj).parents('.cComment').parent().attr('id');
			try{console.log(parentId);} catch(err){}
			jax.call("community", "plugins,walls,ajaxRemoveComment", parentId, index);
		},
		cancel: function(id){
			joms.jQuery('#'+ id +' textarea').val('');
			joms.jQuery('#'+ id +' form').hide();
			joms.jQuery('#'+ id +' .show-cmt').show();
			joms.jQuery('#'+ id + ' .wall-coc-errors').hide();
			joms.jQuery('#'+ id +' .wall-coc-form-action.add').removeAttr('disabled');
		},
		show: function(id){
			var w = joms.jQuery('#'+ id +' form').parent().width();

			joms.jQuery('#'+ id +' .wall-coc-form-action.add').removeAttr('disabled');
			joms.jQuery('#'+ id +' form').width(w).show();
			joms.jQuery('#'+ id +' .show-cmt').hide();

			var textarea = joms.jQuery('#'+ id +' textarea');
			if( !textarea.data('autogrow') ){
				joms.utils.textAreaWidth(textarea);
				joms.utils.autogrow(textarea);
	
				textarea.blur(function(){
					if (joms.jQuery(this).val()=='') joms.comments.cancel(id);
			}).data('autogrow', true);
			}
		}
	},
	utils: {
		// Resize the width of the giventext to follow the innerWidth of
		// another DOM object
		// The textarea must be visible
		textAreaWidth: function(target) {
			with (joms.jQuery(target))
			{
				css('width', '100%');
				// Google Chrome doesn't return correct outerWidth() else things would be nicer.
				// css('width', width()*2 - outerWidth(true));
				css('width', width() - parseInt(css('borderLeftWidth'))
				                     - parseInt(css('borderRightWidth'))
				                     - parseInt(css('padding-left'))
				                     - parseInt(css('padding-right')));
			}
		},

		autogrow: function (id, options) {
			if (options==undefined)
				options = {};

			// In JomSocial, by default every autogrow element will have a 300 maxHeight.
			options.maxHeight = options.maxHeight || 300;

			joms.jQuery(id).autogrow(options);
		}
	},
	maps: {
		mapsObj: null,
		geocoder: null,
		init: function (){
		     if (joms.jQuery('.cMapFade') != null || joms.jQuery('.cMapHeatzone') != null) {
				
				joms.jQuery('.cMapFade').live('mouseover',function(e) {
					joms.jQuery(this).find('img:eq(2)').fadeOut(0);
				});

				joms.jQuery('.cMapFade').live('mouseout',function(e) {
					joms.jQuery(this).find('img:eq(2)').fadeIn(0);
				});

				joms.jQuery('.cMapHeatzone').live('mouseover',function(e) {
					joms.jQuery(this).parent().find('img:eq(1)').fadeOut(0);
				});

				joms.jQuery('.cMapHeatzone').live('mouseout',function(e) {
					joms.jQuery(this).parent().find('img:eq(1)').fadeIn(0);
				});

		     }
		},
		initialize: function(target, address, title, info) {
			if(typeof google.maps =='undefined')
			{
				// Google map is not loaded yet, wait another 1 seconds?
				setTimeout('joms.maps.initialize(\''+target+'\', \''+address+'\')', 1000);
			}
			else
			{
				joms.maps.geocoder = new google.maps.Geocoder();
				joms.maps.geocoder.geocode( {'address': address}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {

						if(joms.maps.mapsObj == null)
						{
							joms.maps.mapsObj = new Array();
						}
						// @todo: unoptimized, should not load a random location first
						var latlng = new google.maps.LatLng(-34.397, 150.644);
					    var mapOptions = {
					      zoom: 14,
					      center: latlng,
					      mapTypeId: google.maps.MapTypeId.ROADMAP
					    }

					    // Map id is incremented for each map
					    var mapId = joms.maps.mapsObj.length;

					    joms.maps.mapsObj[mapId] = new google.maps.Map(document.getElementById(target), mapOptions);

						joms.maps.mapsObj[mapId].setCenter(results[0].geometry.location);
						var marker = new google.maps.Marker({
							map: joms.maps.mapsObj[mapId],
							position: results[0].geometry.location,
							title:title
						});

						if(info.length > 0){
							var infowindow = new google.maps.InfoWindow({
							    content: info
							});

							google.maps.event.addListener(marker, 'click', function() {
								var mapId  = joms.jQuery('div#'+target).data('maps');
								infowindow.open(joms.maps.mapsObj[mapId], marker);
							});
						}

						// Store map object in the div id
						joms.jQuery('div#'+target).data('maps', mapId);

					} else {
						alert("Geocode was not successful for the following reason: " + status);
					}
				});
			}
		},
		addMarker: function(target, lat, lng, title, info) {
			if(joms.maps.mapsObj == null)
			{
				// Google map is not loaded yet, wait another 1 seconds?
				setTimeout('joms.maps.addMarker(\''+target+'\', '+lat+', '+lng+', \''+title+'\', \''+info+'\')', 1000);
			}
			else
			{
				var mapId  = joms.jQuery('div#'+target).data('maps');
				var myLatlng = new google.maps.LatLng(lat,lng);
				var marker = new google.maps.Marker({
					position: myLatlng,
					map: joms.maps.mapsObj[mapId],
					title:title
				});

				if(info.length > 0){
					var infowindow = new google.maps.InfoWindow({
					    content: info
					});

					google.maps.event.addListener(marker, 'click', function() {
						var mapId  = joms.jQuery('div#'+target).data('maps');
						infowindow.open(joms.maps.mapsObj[mapId], marker);
					});
				}

			}
		}
	},
	connect: {
	    checkRealname: function( value ){
	        var tmpLoadingFunction  = jax.loadingFunction;
			jax.loadingFunction = function(){};
			jax.doneLoadingFunction = function(){jax.loadingFunction = tmpLoadingFunction;};
			jax.call('community','connect,ajaxCheckName', value);
		},
	    checkEmail: function( value ){
	        var tmpLoadingFunction  = jax.loadingFunction;
			jax.loadingFunction = function(){};
			jax.doneLoadingFunction = function(){jax.loadingFunction = tmpLoadingFunction;};
			jax.call('community','connect,ajaxCheckEmail', value);
		},
		checkUsername: function( value ){
	        var tmpLoadingFunction  = jax.loadingFunction;
			jax.loadingFunction = function(){};
			jax.doneLoadingFunction = function(){jax.loadingFunction = tmpLoadingFunction;};
		    jax.call('community','connect,ajaxCheckUsername', value);
		},
		// Displays popup that requires user to update their details upon
		update: function(){
			var ajaxCall = "jax.call('community', 'connect,ajaxUpdate' );";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		updateEmail: function(){
		    joms.jQuery('#facebook-email-update').submit();
		},
		importData: function(){
		    var importStatus    = joms.jQuery('#importstatus').is(':checked') ? 1 : 0;
		    var importAvatar    = joms.jQuery('#importavatar').is(':checked') ? 1 : 0 ;
		    jax.call('community','connect,ajaxImportData',  importStatus , importAvatar );
		},
		mergeNotice: function(){
			var ajaxCall = "jax.call('community','connect,ajaxMergeNotice');";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		merge: function(){
			var ajaxCall = "jax.call('community','connect,ajaxMerge');";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		validateUser: function(){
			// Validate existing user
			var ajaxCall = "jax.call('community','connect,ajaxValidateLogin','" + joms.jQuery('#existingusername').val() + "','" + joms.jQuery('#existingpassword').val() + "');";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		newUser: function(){
			var ajaxCall = "jax.call('community','connect,ajaxShowNewUserForm');";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		existingUser: function(){
			var ajaxCall = "jax.call('community','connect,ajaxShowExistingUserForm');";
			cWindowShow(ajaxCall, '', 450, 200);
		},
		selectType: function(){
			if(joms.jQuery('[name=membertype]:checked').val() == '1' )
			{
                                var tnc = joms.jQuery('#tnc:checked').val();
                                
                                if((joms.jQuery('#tnc:checked').length == 0) && (joms.jQuery('#tnc').length == 1))
                                {
                                    joms.jQuery('span#err_msg').css('display','block');
                                    return false;
                                }
                                joms.connect.newUser();
			}
			else
			{
				joms.connect.existingUser();
			}
		},
		validateNewAccount: function(){
			// Check for errors on the forms.
			jax.call('community','connect,ajaxCheckEmail', joms.jQuery('#newemail').val() );
			jax.call('community','connect,ajaxCheckUsername', joms.jQuery('#newusername').val() );
			jax.call('community','connect,ajaxCheckName', joms.jQuery('#newname').val() );

			var isValid	= true;
			if(joms.jQuery('#newname').val() == "" || joms.jQuery('#error-newname').css('display') != 'none')
			{
				isValid = false;
			}

			if(joms.jQuery('#newusername').val() == "" || joms.jQuery('#error-newusername').css('display') != 'none')
			{
				isValid = false;
			}

			if(joms.jQuery('#newemail').val() == '' || joms.jQuery('#error-newemail').css('display') != 'none' )
			{
				isValid = false;
			}

			if(isValid)
			{
				var profileType = '';
				if (joms.jQuery('.jsProfileType').length > 0 && joms.jQuery('.jsProfileType input').length > 0)
				{
					profileType = (joms.jQuery('.jsProfileType input:checked').length > 0) ? joms.jQuery('.jsProfileType input:checked').val() : profileType;
				}
				
				var ajaxCall = "jax.call('community', 'connect,ajaxCreateNewAccount' , '" + joms.jQuery('#newname').val() + "', '" + joms.jQuery('#newusername').val() + "','" + joms.jQuery('#newemail').val() + "','" + profileType + "');";
				cWindowShow(ajaxCall, '', 450, 200);
			}
		}
	},

	// Video component
	videos: {
		playProfileVideo: function(id, userid){
			jax.call('community', 'profile,ajaxPlayProfileVideo', id, userid);
		},
		linkConfirmProfileVideo: function(id){
			var ajaxCall = "jax.call('community', 'profile,ajaxConfirmLinkProfileVideo', '" + id + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		linkProfileVideo: function(id){
			var ajaxCall = "jax.call('community', 'profile,ajaxLinkProfileVideo', '" + id + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		removeConfirmProfileVideo: function(userid, videoid){
			var ajaxCall = "jax.call('community', 'profile,ajaxRemoveConfirmLinkProfileVideo', '" + userid + "', '" + videoid + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		removeLinkProfileVideo: function(userid, videoid){
			var ajaxCall = "jax.call('community', 'profile,ajaxRemoveLinkProfileVideo', '" + userid + "', '" + videoid + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		showEditWindow: function(id , redirectUrl ){

			if( typeof redirectUrl == 'undefined' )
				redirectUrl	= '';

			var ajaxCall = "jax.call('community', 'videos,ajaxEditVideo', '"+id+"' , '" + redirectUrl + "');";
			cWindowShow(ajaxCall, '' , 450, 400);
		},
		deleteVideo: function(videoId, currentTask){
			var ajaxCall = "jax.call('community' , 'videos,ajaxRemoveVideo', '" + videoId + "','" + currentTask + "');";
			cWindowShow(ajaxCall, '', 450, 150);
		},
		playerConf: {
			// Default flowplayer configuration here
		},
		addVideo: function(creatortype, groupid) {
			if(typeof creatortype == "undefined" || creatortype == "")
			{
				var creatortype="";
				var groupid = "";
			}
			var ajaxCall = "jax.call('community', 'videos,ajaxAddVideo', '" + creatortype + "', '" + groupid + "');";
			cWindowShow(ajaxCall, '', 600, 500);
		},
		linkVideo: function(creatortype, groupid) {
			var ajaxCall = "jax.call('community', 'videos,ajaxLinkVideo', '" + creatortype + "', '" + groupid + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		uploadVideo: function(creatortype, groupid) {
			var ajaxCall = "jax.call('community', 'videos,ajaxUploadVideo', '" + creatortype + "', '" + groupid + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		submitLinkVideo: function() {
			var isValid = true;

			videoLinkUrl = "#linkVideo input[name='videoLinkUrl']";
			if(joms.jQuery.trim(joms.jQuery(videoLinkUrl).val())=='')
			{
				joms.jQuery(videoLinkUrl).addClass('invalid');
				isValid = false;
			}
			else
			{
				joms.jQuery(videoLinkUrl).removeClass('invalid');
			}

			if (isValid)
			{
				joms.jQuery('#cwin-wait').css("margin-left","20px");
				joms.jQuery('#cwin-wait').show();

				document.linkVideo.submit();
			}
		},
		submitUploadVideo: function() {
			var isValid = true;

			videoFile = "#uploadVideo input[name='videoFile']";

			if(joms.jQuery.trim(joms.jQuery(videoFile).val())=='')
			{
				joms.jQuery(videoFile).addClass('invalid');
				isValid = false;
			}
			else
			{
				joms.jQuery(videoFile).removeClass('invalid');
			}

			videoTitle = "#uploadVideo input[name='title']";
			if(joms.jQuery.trim(joms.jQuery(videoTitle).val())=='')
			{
				joms.jQuery(videoTitle).addClass('invalid');
				isValid = false;
			}
			else
			{
				joms.jQuery(videoTitle).removeClass('invalid');
			}

			if (isValid)
			{
				joms.jQuery('#cwin-wait').css("margin-left","20px");
				joms.jQuery('#cwin-wait').show();

				document.uploadVideo.submit();
			}
		},
		fetchThumbnail: function(videoId){
			var ajaxCall = "jax.call('community' , 'videos,ajaxFetchThumbnail', '" + videoId + "','myvideos');";
			cWindowShow(ajaxCall, '', 450, 150);
		},
		toggleSearchSubmenu: function(e){
			joms.jQuery(e).next('ul').toggle().find('input[type=text]').focus();
		}
	},
	users: {
		banUser: function( userId , isBlocked ){
			var ajaxCall = "jax.call('community', 'profile,ajaxBanUser', '" + userId + "' , '" + isBlocked + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		removePicture: function( userId ){
			var ajaxCall = "jax.call('community', 'profile,ajaxRemovePicture', '" + userId + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		updateURL: function( userId ){
			var ajaxCall = "jax.call('community', 'profile,ajaxUpdateURL', '" + userId + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		uploadNewPicture: function( userId ){
			var ajaxCall = "jax.call('community', 'profile,ajaxUploadNewPicture', '" + userId + "');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
	   blockUser: function( userId ){
			var ajaxCall = 'jax.call("community", "profile,ajaxBlockUser", "' + userId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
       },
	   unBlockUser: function( userId, layout ){
	        layout = layout || null;
			var ajaxCall = 'jax.call("community", "profile,ajaxUnblockUser", "' + userId + '", "' + layout + '");';
			cWindowShow(ajaxCall, '', 450, 100);
       }
	},

	user: {
		getActive: function( ){
			// return the current active user
			return js_profileId;
		}
	},

	events: {
		deleteEvent: function( eventId ){
			var ajaxCall = "jax.call('community', 'events,ajaxWarnEventDeletion', '" + eventId + "');";
			cWindowShow(ajaxCall, '', 450, 100, 'warning');
		},
		join: function( eventId ){
			var ajaxCall = 'jax.call("community", "events,ajaxRequestInvite", "' + eventId + '", location.href );';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		leave: function( eventId ){
			var ajaxCall = 'jax.call("community", "events,ajaxIgnoreEvent", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		sendmail: function( eventId ){
			var ajaxCall = 'jax.call("community", "events,ajaxSendEmail", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 300);
		},
		confirmBlockGuest: function(userId, eventId){
			var ajaxCall = 'jax.call("community", "events,ajaxConfirmBlockGuest", "' + userId + '", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		blockGuest: function(userId, eventId){
			var ajaxCall = 'jax.call("community", "events,ajaxBlockGuest", "' + userId + '", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		confirmUnblockGuest: function(userId, eventId){
			var ajaxCall = 'jax.call("community", "events,ajaxConfirmUnblockGuest", "' + userId + '", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		unblockGuest: function(userId, eventId){
			var ajaxCall = 'jax.call("community", "events,ajaxUnblockGuest", "' + userId + '", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		confirmRemoveGuest: function(userId, eventId){
			var ajaxCall = 'jax.call("community", "events,ajaxConfirmRemoveGuest", "' + userId + '", "' + eventId + '");';
			cWindowShow(ajaxCall, '', 450, 80, 'warning');
		},
		removeGuest: function(userId, eventId)
		{		
			var blockGuest = joms.jQuery('#cWindow input[name=block]').attr('checked');

			var ajaxCall = ''
			if (blockGuest)
			{
				ajaxCall = 'jax.call("community", "events,ajaxBlockGuest", "' + userId + '", "' + eventId + '");';
			} else {
				ajaxCall = 'jax.call("community", "events,ajaxRemoveGuest", "' + userId + '", "' + eventId + '");';
			}

			cWindowShow(ajaxCall, '', 450, 100, 'warning');
		},
		joinNow: function(eventId){
			jax.call("community" , "events,ajaxJoinInvitation" , eventId );
		},
		rejectNow: function(eventId){
			jax.call('community' , 'events,ajaxRejectInvitation' , eventId );
		},
		toggleSearchSubmenu: function(e){
			joms.jQuery(e).next('ul').toggle().find('input[type=text]').focus();
		},
		displayNearbyEvents: function(location){
			joms.ajax.call('events,ajaxDisplayNearbyEvents', [location], {
				success: function(html)
				{
				    joms.jQuery('#community-event-nearby-listing').html(html);

				}
			});
		},
		getDayEvent: function (day, month, year){
			//show loading image
			jax.loadingFunction = function(){joms.jQuery('.loading-icon').show();};
			jax.doneLoadingFunction = function(){joms.jQuery('.loading-icon').hide();};
			
			jax.cacheCall('community', 'events,ajaxGetEvents', day, month, year);
		},
		displayDayEvent: function(response){
			joms.jQuery('.events-list').html('');
			
			var day_event = eval('(' + response + ')');
			
			if(day_event.length > 0){
				//events exist, unhide the 'happening event'
				joms.jQuery('strong.happening_title').show();
			}else{
				joms.jQuery('strong.happening_title').hide();
			}
			
			for(var i = 0; i < day_event.length; i++){
				joms.jQuery('.events-list').html(joms.jQuery('.events-list').html()+'<a href="'+day_event[i]['link']+'"> ' + day_event[i]['title'] + '</a>');
				
				if(i + 1 != day_event.length){
					joms.jQuery('.events-list').html(joms.jQuery('.events-list').html()+',');
				}
			}
		},
		getCalendar: function (month, year){
			jax.cacheCall('community','events,ajaxGetCalendar', month, year);
		},
		displayCalendar: function (response){
			joms.jQuery('div#event').html(response);
			init_calendar();
		},
		switchImport: function( importType ){

			if( importType == 'file' )
			{
				joms.jQuery('#event-import-url').css('display' , 'none' );
				joms.jQuery('#event-import-file').css('display' , 'block' );
				joms.jQuery('#import-type').val( 'file' );
			}

			if( importType == 'url' )
			{
				joms.jQuery('#event-import-file').css('display' , 'none' );
				joms.jQuery('#event-import-url').css('display' , 'block' );
				joms.jQuery('#import-type').val('url');
			}
		},
                showMapWindow: function(){
                        var ajaxCall = 'jax.call("community", "events,ajaxShowMap");';
			cWindowShow(ajaxCall, '', 450, 100);
                },
                showDesc:   function(){
                    joms.jQuery('#event-discription').show();
                    joms.jQuery('#event-description-link').hide();                   
                },
                submitRSVP:function(eventId,e){
                     // Remove the onclick attribute value to avoid double processing
                    rsvpres = e.value;
                    joms.ajax.call('events,ajaxUpdateStatus',[eventId,rsvpres],{
                        success:function(html){
                            joms.jQuery("#community-event-members").replaceWith(html);
                            var cssClass = joms.jQuery(e.children[rsvpres-1]).prop('class');
                            joms.jQuery(e.parentNode.children[0]).prop('class',null);
                            joms.jQuery(e.parentNode.children[0]).addClass(cssClass);
                        }
                    });
                }
	},
	profile: {
		confirmRemoveAvatar: function(){
			var ajaxCall = 'jax.call("community", "profile,ajaxConfirmRemoveAvatar");';
			cWindowShow(ajaxCall, '', 450, 100);
		},
		setStatusLimit: function( textAreaElement ){
			joms.jQuery( textAreaElement ).keyup(function(){
				var max = parseInt( joms.jQuery(this).attr('maxlength'));
				if( joms.jQuery(this).val().length > max)
				{
					joms.jQuery(this).val( joms.jQuery(this).val().substr(0, joms.jQuery(this).attr('maxlength')));
				}
				joms.jQuery('#profile-status-notice span').html( (max - joms.jQuery(this).val().length) );
			});
		}
	},
	privacy: {
		init: function(){
			joms.jQuery('select.js_PrivacySelect').each(function() {
				var tmpHTML = "";
				var currValue;
				
				// get current value of pre-selected item from the dropdown
				joms.jQuery(this).find('option').each(function() {
					if(joms.jQuery(this).attr('selected')) {
						currValue = joms.jQuery(this).val();
					}
				});
				
				// construct HTML
				tmpHTML += "<dl class='js_dropDownMaster'>\n";
				tmpHTML += "<dt name=" + currValue + " class='js_dropDown js_dropSelect-" + currValue + "'><strong>" + joms.jQuery(this).find('option[selected="selected"]').text() + "</strong><span></span></dt>\n";
				tmpHTML += "<dd>\n<ul class='js_dropDownParent'>\n";
				
				joms.jQuery(this).find('option').each(function() {
					var currOptVal = joms.jQuery(this).val();			
					
					// add extra class for currently selected option
					if(currOptVal == currValue) {
						tmpHTML += "<li class='js_dropDownCurrent'>";
					} else {
						tmpHTML += "<li>";
					}
					
					tmpHTML += "<a href='javascript:void()' name='" + currOptVal + "' class='js_dropDownChild js_dropDown-" + currOptVal + "'>" + joms.jQuery(this).text() + "</a></li>\n";
				});
				
				tmpHTML += "</ul>\n</dd>\n</dl>";
				
				// write HTML
				joms.jQuery(this).parent().prepend(tmpHTML);
				
				// hide original select box
				joms.jQuery(this).hide();
		
			});
			
			joms.jQuery('.js_dropDownChild').die('click');
			joms.jQuery('.js_dropDownChild').live('click',function(e) {
				e.preventDefault();
				var selectedVal = joms.jQuery(this).attr('name');
				var selectedText = "";
				
				
				// once clicked, change the select to pick that one
				joms.jQuery(this).closest('.js_PriContainer').find('option').each(function() {
					// traverse through each select box and mark the same value as 'selected' = true
					if(joms.jQuery(this).val() == selectedVal) {
						joms.jQuery(this).attr('selected', 'selected');
						selectedText = joms.jQuery(this).text();
					} else {
						joms.jQuery(this).attr('selected', false);
					}
				});
				
				// get current selection value
				var dropDownObj = joms.jQuery(this).parent().parent().parent().parent().find('dt');
				var currShowVal = dropDownObj.attr('name');
				
				dropDownObj.removeClass('js_dropSelect-' + currShowVal).addClass('js_dropSelect-' + selectedVal).attr('name', selectedVal).html('<strong>' + selectedText + '</strong><span></span>');
				
				
				// hide box after selecting
				// joms.jQuery(this).parent().parent().parent().parent().data('state',0).removeClass('js_Current').find('dd').hide();
				joms.privacy.closeAll();
			});
			
			// click trigger to open
			joms.jQuery('.js_dropDownMaster dt').die('click');
			joms.jQuery('.js_dropDownMaster dt').live('click', function(e) {
				e.preventDefault();
				if (joms.jQuery(this).parent().data('state')) {
					// joms.jQuery(this).parent().data('state',0).removeClass('js_Current').find('dd').hide();
					joms.privacy.closeAll();
					joms.jQuery('body').unbind('click');
				} else {
					joms.privacy.closeAll();
					joms.jQuery(this).parent().parent().addClass('js_PrivacyOpen');
					joms.jQuery(this).parent().data('state',1).addClass('js_Current').find('dd').show();
					joms.jQuery('body').bind('click', function(e) {
						var tarObj = joms.jQuery(e.target);
						
						if(tarObj.parents('.js_PriContainer').length == 0) {
							joms.privacy.closeAll();
						}
					});
				}	
			});
		},
		closeAll: function() {
		  joms.jQuery('.js_PriContainer').removeClass('js_PrivacyOpen');
			joms.jQuery('.js_dropDownMaster').each(function() {
				joms.jQuery(this).data('state',0).removeClass('js_Current').find('dd').hide();
			});
		}
	},
	tooltip: {
		setup: function( ){
		
			joms.jQuery('.jomNameTips').tipsy({live: true, gravity: 'sw'});
			//return;
			
			// Hide all active visible qTip
			joms.jQuery('.qtip-active').hide();
			setTimeout('joms.jQuery(\'.qtip-active\').hide()', 150);
			try{clearTimeout(joms.jQuery.fn.qtip.timers.show);} catch(e){}

			// Scan the document and setup the tooltip that has .jomTips
			joms.jQuery(".jomTips").each(function(){
		    	var tipStyle = 'tipNormal';
		    	var tipWidth = 220;
		    	var tipPos	 = {corner: {target: 'topMiddle',tooltip: 'bottomMiddle'}}
		    	var tipShow  = true;
		    	var tipHide	 = {when: {event: 'mouseout'}, effect: {length: 10}}

		    	if(joms.jQuery(this).hasClass('tipRight'))
				{
		    		tipStyle = 'tipRight';
		    		tipWidth = 320;
		    		tipPos	 = {corner: {target: 'rightMiddle',tooltip: 'leftMiddle'}}
		    	}

		    	if(joms.jQuery(this).hasClass('tipWide'))
				{
		    		tipWidth = 420;
		    	}

		    	if(joms.jQuery(this).hasClass('tipFullWidth'))
				{
		    		tipWidth = joms.jQuery(this).innerWidth()-20;
		    	}

		    	// Split the title and the content
		    	var title = '';
		    	var content = joms.jQuery(this).attr('title');
				var contentArray = '';
				if(content){
					contentArray = content.split('::');
				}

				// Remove the 'title' attributes from the existing .jomTips classes
				joms.jQuery( this ).attr('title' , '' );

				if(contentArray.length == 2)
				{
					content = contentArray[1];
					title = {text: contentArray[0]} ;
				} else
					title = title = {text: ''} ;;


		    	joms.jQuery(this).qtip({
		    		content: {
					   text: content,
					   title: title
					},
					style: {name:tipStyle , width: tipWidth},
					position: tipPos,
					hide: tipHide,
					show: {solo: true, effect: {length: 50}}
			 	}).removeClass('jomTips');
			});

			return true;
		},

		setStyle: function() {
			joms.jQuery.fn.qtip.styles.tipNormal = { // Last part is the name of the style
				width: 320,
				border: {
					width: 7,
					radius: 5
				},
				tip: true,
				name: 'dark' // Inherit the rest of the attributes from the preset dark style
			}

			joms.jQuery.fn.qtip.styles.tipRight = { // Last part is the name of the style
				tip: 'leftMiddle',
				name: 'tipNormal' // Inherit the rest of the attributes from the preset dark style
			}

			return true;
		}
	},
	like : {
		extractData: function(id) {
		    id = id.split('-');
		    var data = [];
		    data['element'] = id[1];
		    data['itemid']  = id[2];

			// replace element _ with .
			data['element'] = data['element'].replace('_', '.');
		    return data;
		},
		like: function(e){
		    var like  = joms.jQuery(e).parents('.like-snippet');
		    var data = this.extractData(like.attr('id'));

		    // Remove the onclick attribute value to avoid double processing
		    joms.jQuery(e).attr('onclick', '');

		    joms.ajax.call('system,ajaxLike', [data['element'], data['itemid']], {
			success: function(html)
			{
			    like.replaceWith(html);
			}
		    });
		},
		dislike: function(e){
		    var like  = joms.jQuery(e).parents('.like-snippet');
		    var data = this.extractData(like.attr('id'));

		    // Remove the onclick attribute value to avoid double processing
		    joms.jQuery(e).attr('onclick', '');

		    joms.ajax.call('system,ajaxDislike', [data['element'], data['itemid']], {
			success: function(html)
			{
			    like.replaceWith(html);
			}
		    });
		},
		unlike: function(e){
		    var like  = joms.jQuery(e).parents('.like-snippet');
		    var data = this.extractData(like.attr('id'));

		    // Remove the onclick attribute value to avoid double processing
		    joms.jQuery(e).attr('onclick', '');

		    joms.ajax.call('system,ajaxUnlike', [data['element'], data['itemid']], {
			success: function(html)
			{
			    like.replaceWith(html);
			}
		    });
		}
	},
	tag: {
		add: function(element, id){
			jax.call('community', 'system,ajaxAddTag', element, id, joms.jQuery('#tag-addbox').val());
		},
		pick : function(element, id, tag){
			jax.call('community', 'system,ajaxAddTag', element, id, tag );
		},
		remove: function(id){
			jax.call('community', 'system,ajaxRemoveTag', id);
		},
		moreHide: function(element, id){
			joms.jQuery('#tag-list li').each(function(i, l){
				if(i > 8){
					// @todo: limit should come from global config
					joms.jQuery(l).hide();
				}
			});
			joms.jQuery('.more-tag-show').show();
			joms.jQuery('.more-tag-hide').hide();
		},
		moreShow: function(element, id){
			joms.jQuery('#tag-list li').each(function(i, l){
				if(i > 8){
					// @todo: limit should come from global config
					joms.jQuery(l).show();
				}
			});
			joms.jQuery('.more-tag-show').hide();
			joms.jQuery('.more-tag-hide').show();
		},
		toggleMore: function(element, id){
			// Hide/show other tags
			joms.jQuery('#tag-list li').each(function(i, l){
				if(i > 8){
					// @todo: limit should come from global config
					joms.jQuery(l).toggle();
				}
			});
		},
		list: function(tag){
			var ajaxCall = "jax.call('community', 'system,ajaxShowTagged', '"+tag+"');";
			cWindowShow(ajaxCall, '', 450, 100);
		},
		edit: function(element, cid){
			// start edit window
			joms.tag.moreShow(element, cid);
			var tagClass = element+"-"+cid;
			joms.jQuery('#tag-editor.tag-editor-'+tagClass+',.tag-token a.tag-delete').show();
		},
		
		done: function(element, cid){
			joms.tag.moreHide(element, cid);
			var tagClass = element+"-"+cid;
			joms.jQuery('#tag-editor.tag-editor-'+tagClass+',.tag-token a.tag-delete').hide();
		}
	},
	geolocation : {
		showNearByEvents: function( location ){
		    joms.jQuery('#community-event-nearby-listing').show();
		    joms.jQuery('#showNearByEventsLoading').show();

		    // Check if already have the input
		    if( typeof(location) == 'undefined' )
		    {
			// Check if the browsers support W3C Geolocation API
			if( navigator.geolocation )
			{
			    navigator.geolocation.getCurrentPosition(function(location)
			    {
				var lat	=   location.coords.latitude;
				var lng	=   location.coords.longitude;

				// Reverse Geocoding - Google Maps Javascript API V3 Services
				geocoder	=   new google.maps.Geocoder();
				var latlng	=   new google.maps.LatLng( lat, lng );

				geocoder.geocode({'latLng': latlng}, function(results, status){
				    if( status == google.maps.GeocoderStatus.OK ){
					if ( results[4] ){
					    location = results[4].formatted_address;
					    joms.geolocation.setCookie( 'currentLocation', location );
					    joms.events.displayNearbyEvents( location );
					}
				    } else {
					alert("Geocoder failed due to: " + status);
				    }
				});
			    });
			}
			else // If the browser not support W3C Geolocation API, show the error message
			{
			    alert('Sorry, your browser does not support this feature.');
			    joms.jQuery('#community-event-nearby-listing').hide();
			    joms.jQuery('#showNearByEventsLoading').hide();
			}
		    }
		    else
		    {
			joms.events.displayNearbyEvents( location );
		    }
		},
		validateNearByEventsForm: function(){
		    var location   =   joms.jQuery('#userInputLocation').val();

		    if( location.length != 0 )
		    {
			joms.geolocation.showNearByEvents( location );
		    }
		},
		setCookie: function( c_name, value ){
		    var exdate=new Date();

		    // Calculate expiry date 1 hour from now
		    exdate.setTime(exdate.getTime() + (60 * 60 * 1000));

		    document.cookie=c_name+ "=" +escape(value)+";expires="+exdate;
		},
		getCookie: function( c_name ){
		    if (document.cookie.length>0)
		    {
			c_start=document.cookie.indexOf(c_name + "=");
			if (c_start!=-1)
			{
			    c_start=c_start + c_name.length+1;
			    c_end=document.cookie.indexOf(";",c_start);
			    if (c_end==-1) c_end=document.cookie.length;
			    return unescape(document.cookie.substring(c_start,c_end));
			}
		    }
		    return "";
		}
	}
});


// close layer when click-out
joms.jQuery(document).click( function() {
    joms.toolbar.close();
});


/*
* Toolbar notification counter update
* @param string jQuery selector
* @param string/int number of counts to increase/decrease
*/
function update_counter(selector, count){
	
	//e.g selector = '#jsMenuNotif > .notifcount'
	//validate the count number
	if(!count){
		count = 0;
	}
	var currnum = parseInt(jQuery(selector).html(), 10); 
	count = parseInt(count, 10);
	if(currnum <= 1) {
		jQuery(selector).css('display','none');
	} else{ 
		jQuery(selector).html(currnum + count); 
	}
}


function get_cookies_array() {

    var cookies = { };

    if (document.cookie && document.cookie != '')
	{
		var split = document.cookie.split(';');
		for (var i = 0; i < split.length; i++)
		{
			var name_value = split[i].split("=");
			name_value[0] = name_value[0].replace(/^ /, '');
			cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
		}
    }

    return cookies;

}


// Document ready
joms.jQuery(document).ready(function () {
	joms.tooltip.setStyle();
	joms.tooltip.setup();
	joms.apps.initToggle();
	joms.plugins.initialize();
	
	if(joms.jQuery('.album-actions').length) {
		joms.album.init();
	}
	
	if(joms.jQuery('.wall-coc-item').length) {
	  joms.miniwall.initialize();
	}
	
	joms.activities.initMap();
	joms.maps.init();
	
	// Initialize tab
	var i = 0;
	
	// Init the tab status by populating them with jQuery data (loop)
	joms.jQuery('.cTabsBar li').each(function() {

		// add event and ID dynamically to each found tabs
		joms.jQuery(this)
			.attr('id','cTab-' + i)
			.children('a').click( function(){
				if(joms.jQuery(this).parent('li').hasClass('cTabDisabled')){
					// defocus selection
					joms.jQuery(this).blur();
					// do nothing on disabled tab
					return;
				}
				joms.jQuery('.cTabsBar li').removeClass('cTabCurrent');
				joms.jQuery('.cTabsContent').removeClass('cTabsContentCurrent').trigger('onAfterHide');

				var tabid = joms.jQuery(this).parent('li').index();

				joms.jQuery('#cTab-' + tabid).addClass('cTabCurrent');
				joms.jQuery('#cTabContent-' + tabid).addClass('cTabsContentCurrent').trigger('onAfterShow');

			});

		joms.jQuery('.cTabsContentWrap .cTabsContent:eq(' + (i) + ')')
			.attr('id','cTabContent-' + i);

		if(joms.jQuery(this).hasClass('cTabCurrent')) {
			joms.jQuery('.cTabsContentWrap .cTabsContent:eq(' + (i) + ')').data('status',1);
		}

		i++;
	});
});

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */
joms.jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = joms.jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};




(function($) {
    $.fn.autogrow = function(o) {
		
		var o = o || {};

        this.filter('textarea').each(function(){

			var textarea = $(this);

			if (textarea.hasClass('shadow')) return;

			var shadow = textarea.data('shadow');
			var offset = textarea.outerHeight() - textarea.innerHeight();

			if (!shadow)
			{
				shadow =
					textarea
						.clone()
						.unbind()
						.removeAttr('name')
						.addClass('shadow')
						.css(
						{
							'position'   : 'absolute',
							'visibility' : 'hidden',
							'height'     : 0
						})
						.insertAfter(textarea);

				if (o.lineHeight==undefined) o.lineHeight = shadow.val(' ')[0].scrollHeight;

				textarea
					.data('shadow', shadow)
					.bind('focus blur keyup keypress autogrow', autogrow);
			}

			if (o.minHeight==undefined) o.minHeight = textarea.height();
			if (o.maxHeight==undefined) o.maxHeight = 0;

			function autogrow()
			{
				shadow.val(textarea.val());

				// IE bug
				shadow[0].scrollHeight;

				var height = shadow[0].scrollHeight;

				if (height > o.maxHeight && o.maxHeight > 0)
				{
					height = o.maxHeight;
					textarea.css('overflow', 'auto');
				} else {
					//height = (height < o.minHeight) ? o.minHeight : height + o.lineHeight;
					height = (height < o.minHeight) ? o.minHeight : height;
					textarea.css('overflow', 'hidden');
				}
				
				textarea.height(height);
			}

            autogrow();
        });

        return this;
    }
})(joms.jQuery);


/*
 * jquery.qtip. The jQuery tooltip plugin
 *
 * Copyright (c) 2009 Craig Thompson
 * http://craigsworks.com
 *
 * Licensed under MIT
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Launch  : February 2009
 * Version : 1.0.0-rc3
 * Released: Tuesday 12th May, 2009 - 00:00
 * Debug: jquery.qtip.debug.js
 */
(function(f){f.fn.qtip=function(B,u){var y,t,A,s,x,w,v,z;if(typeof B=="string"){if(typeof f(this).data("qtip")!=="object"){f.fn.qtip.log.error.call(self,1,f.fn.qtip.constants.NO_TOOLTIP_PRESENT,false)}if(B=="api"){return f(this).data("qtip").interfaces[f(this).data("qtip").current]}else{if(B=="interfaces"){return f(this).data("qtip").interfaces}}}else{if(!B){B={}}if(typeof B.content!=="object"||(B.content.jquery&&B.content.length>0)){B.content={text:B.content}}if(typeof B.content.title!=="object"){B.content.title={text:B.content.title}}if(typeof B.position!=="object"){B.position={corner:B.position}}if(typeof B.position.corner!=="object"){B.position.corner={target:B.position.corner,tooltip:B.position.corner}}if(typeof B.show!=="object"){B.show={when:B.show}}if(typeof B.show.when!=="object"){B.show.when={event:B.show.when}}if(typeof B.show.effect!=="object"){B.show.effect={type:B.show.effect}}if(typeof B.hide!=="object"){B.hide={when:B.hide}}if(typeof B.hide.when!=="object"){B.hide.when={event:B.hide.when}}if(typeof B.hide.effect!=="object"){B.hide.effect={type:B.hide.effect}}if(typeof B.style!=="object"){B.style={name:B.style}}B.style=c(B.style);s=f.extend(true,{},f.fn.qtip.defaults,B);s.style=a.call({options:s},s.style);s.user=f.extend(true,{},B)}return f(this).each(function(){if(typeof B=="string"){w=B.toLowerCase();A=f(this).qtip("interfaces");if(typeof A=="object"){if(u===true&&w=="destroy"){while(A.length>0){A[A.length-1].destroy()}}else{if(u!==true){A=[f(this).qtip("api")]}for(y=0;y<A.length;y++){if(w=="destroy"){A[y].destroy()}else{if(A[y].status.rendered===true){if(w=="show"){A[y].show()}else{if(w=="hide"){A[y].hide()}else{if(w=="focus"){A[y].focus()}else{if(w=="disable"){A[y].disable(true)}else{if(w=="enable"){A[y].disable(false)}}}}}}}}}}}else{v=f.extend(true,{},s);v.hide.effect.length=s.hide.effect.length;v.show.effect.length=s.show.effect.length;if(v.position.container===false){v.position.container=f(document.body)}if(v.position.target===false){v.position.target=f(this)}if(v.show.when.target===false){v.show.when.target=f(this)}if(v.hide.when.target===false){v.hide.when.target=f(this)}t=f.fn.qtip.interfaces.length;for(y=0;y<t;y++){if(typeof f.fn.qtip.interfaces[y]=="undefined"){t=y;break}}x=new d(f(this),v,t);f.fn.qtip.interfaces[t]=x;if(typeof f(this).data("qtip")=="object"){if(typeof f(this).attr("qtip")==="undefined"){f(this).data("qtip").current=f(this).data("qtip").interfaces.length}f(this).data("qtip").interfaces.push(x)}else{f(this).data("qtip",{current:0,interfaces:[x]})}if(v.content.prerender===false&&v.show.when.event!==false&&v.show.ready!==true){v.show.when.target.bind(v.show.when.event+".qtip-"+t+"-create",{qtip:t},function(C){z=f.fn.qtip.interfaces[C.data.qtip];z.options.show.when.target.unbind(z.options.show.when.event+".qtip-"+C.data.qtip+"-create");z.cache.mouse={x:C.pageX,y:C.pageY};p.call(z);z.options.show.when.target.trigger(z.options.show.when.event)})}else{x.cache.mouse={x:v.show.when.target.offset().left,y:v.show.when.target.offset().top};p.call(x)}}})};function d(u,t,v){var s=this;s.id=v;s.options=t;s.status={animated:false,rendered:false,disabled:false,focused:false};s.elements={target:u.addClass(s.options.style.classes.target),tooltip:null,wrapper:null,content:null,contentWrapper:null,title:null,button:null,tip:null,bgiframe:null};s.cache={mouse:{},position:{},toggle:0};s.timers={};f.extend(s,s.options.api,{show:function(y){var x,z;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"show")}if(s.elements.tooltip.css("display")!=="none"){return s}s.elements.tooltip.stop(true,false);x=s.beforeShow.call(s,y);if(x===false){return s}function w(){if(s.options.position.type!=="static"){s.focus()}s.onShow.call(s,y);if(f.browser.msie){s.elements.tooltip.get(0).style.removeAttribute("filter")}}s.cache.toggle=1;if(s.options.position.type!=="static"){s.updatePosition(y,(s.options.show.effect.length>0))}if(typeof s.options.show.solo=="object"){z=f(s.options.show.solo)}else{if(s.options.show.solo===true){z=f("div.qtip").not(s.elements.tooltip)}}if(z){z.each(function(){if(f(this).qtip("api").status.rendered===true){f(this).qtip("api").hide()}})}if(typeof s.options.show.effect.type=="function"){s.options.show.effect.type.call(s.elements.tooltip,s.options.show.effect.length);s.elements.tooltip.queue(function(){w();f(this).dequeue()})}else{switch(s.options.show.effect.type.toLowerCase()){case"fade":s.elements.tooltip.fadeIn(s.options.show.effect.length,w);break;case"slide":s.elements.tooltip.slideDown(s.options.show.effect.length,function(){w();if(s.options.position.type!=="static"){s.updatePosition(y,true)}});break;case"grow":s.elements.tooltip.show(s.options.show.effect.length,w);break;default:s.elements.tooltip.show(null,w);break}s.elements.tooltip.addClass(s.options.style.classes.active)}return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_SHOWN,"show")},hide:function(y){var x;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"hide")}else{if(s.elements.tooltip.css("display")==="none"){return s}}clearTimeout(s.timers.show);s.elements.tooltip.stop(true,false);x=s.beforeHide.call(s,y);if(x===false){return s}function w(){s.onHide.call(s,y)}s.cache.toggle=0;if(typeof s.options.hide.effect.type=="function"){s.options.hide.effect.type.call(s.elements.tooltip,s.options.hide.effect.length);s.elements.tooltip.queue(function(){w();f(this).dequeue()})}else{switch(s.options.hide.effect.type.toLowerCase()){case"fade":s.elements.tooltip.fadeOut(s.options.hide.effect.length,w);break;case"slide":s.elements.tooltip.slideUp(s.options.hide.effect.length,w);break;case"grow":s.elements.tooltip.hide(s.options.hide.effect.length,w);break;default:s.elements.tooltip.hide(null,w);break}s.elements.tooltip.removeClass(s.options.style.classes.active)}return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_HIDDEN,"hide")},updatePosition:function(w,x){var C,G,L,J,H,E,y,I,B,D,K,A,F,z;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"updatePosition")}else{if(s.options.position.type=="static"){return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.CANNOT_POSITION_STATIC,"updatePosition")}}G={position:{left:0,top:0},dimensions:{height:0,width:0},corner:s.options.position.corner.target};L={position:s.getPosition(),dimensions:s.getDimensions(),corner:s.options.position.corner.tooltip};if(s.options.position.target!=="mouse"){if(s.options.position.target.get(0).nodeName.toLowerCase()=="area"){J=s.options.position.target.attr("coords").split(",");for(C=0;C<J.length;C++){J[C]=parseInt(J[C])}H=s.options.position.target.parent("map").attr("name");E=f('img[usemap="#'+H+'"]:first').offset();G.position={left:Math.floor(E.left+J[0]),top:Math.floor(E.top+J[1])};switch(s.options.position.target.attr("shape").toLowerCase()){case"rect":G.dimensions={width:Math.ceil(Math.abs(J[2]-J[0])),height:Math.ceil(Math.abs(J[3]-J[1]))};break;case"circle":G.dimensions={width:J[2]+1,height:J[2]+1};break;case"poly":G.dimensions={width:J[0],height:J[1]};for(C=0;C<J.length;C++){if(C%2==0){if(J[C]>G.dimensions.width){G.dimensions.width=J[C]}if(J[C]<J[0]){G.position.left=Math.floor(E.left+J[C])}}else{if(J[C]>G.dimensions.height){G.dimensions.height=J[C]}if(J[C]<J[1]){G.position.top=Math.floor(E.top+J[C])}}}G.dimensions.width=G.dimensions.width-(G.position.left-E.left);G.dimensions.height=G.dimensions.height-(G.position.top-E.top);break;default:return f.fn.qtip.log.error.call(s,4,f.fn.qtip.constants.INVALID_AREA_SHAPE,"updatePosition");break}G.dimensions.width-=2;G.dimensions.height-=2}else{if(s.options.position.target.add(document.body).length===1){G.position={left:f(document).scrollLeft(),top:f(document).scrollTop()};G.dimensions={height:f(window).height(),width:f(window).width()}}else{if(typeof s.options.position.target.attr("qtip")!=="undefined"){G.position=s.options.position.target.qtip("api").cache.position}else{G.position=s.options.position.target.offset()}G.dimensions={height:s.options.position.target.outerHeight(),width:s.options.position.target.outerWidth()}}}y=f.extend({},G.position);if(G.corner.search(/right/i)!==-1){y.left+=G.dimensions.width}if(G.corner.search(/bottom/i)!==-1){y.top+=G.dimensions.height}if(G.corner.search(/((top|bottom)Middle)|center/)!==-1){y.left+=(G.dimensions.width/2)}if(G.corner.search(/((left|right)Middle)|center/)!==-1){y.top+=(G.dimensions.height/2)}}else{G.position=y={left:s.cache.mouse.x,top:s.cache.mouse.y};G.dimensions={height:1,width:1}}if(L.corner.search(/right/i)!==-1){y.left-=L.dimensions.width}if(L.corner.search(/bottom/i)!==-1){y.top-=L.dimensions.height}if(L.corner.search(/((top|bottom)Middle)|center/)!==-1){y.left-=(L.dimensions.width/2)}if(L.corner.search(/((left|right)Middle)|center/)!==-1){y.top-=(L.dimensions.height/2)}I=(f.browser.msie)?1:0;B=(f.browser.msie&&parseInt(f.browser.version.charAt(0))===6)?1:0;if(s.options.style.border.radius>0){if(L.corner.search(/Left/)!==-1){y.left-=s.options.style.border.radius}else{if(L.corner.search(/Right/)!==-1){y.left+=s.options.style.border.radius}}if(L.corner.search(/Top/)!==-1){y.top-=s.options.style.border.radius}else{if(L.corner.search(/Bottom/)!==-1){y.top+=s.options.style.border.radius}}}if(I){if(L.corner.search(/top/)!==-1){y.top-=I}else{if(L.corner.search(/bottom/)!==-1){y.top+=I}}if(L.corner.search(/left/)!==-1){y.left-=I}else{if(L.corner.search(/right/)!==-1){y.left+=I}}if(L.corner.search(/leftMiddle|rightMiddle/)!==-1){y.top-=1}}if(s.options.position.adjust.screen===true){y=o.call(s,y,G,L)}if(s.options.position.target==="mouse"&&s.options.position.adjust.mouse===true){if(s.options.position.adjust.screen===true&&s.elements.tip){K=s.elements.tip.attr("rel")}else{K=s.options.position.corner.tooltip}y.left+=(K.search(/right/i)!==-1)?-6:6;y.top+=(K.search(/bottom/i)!==-1)?-6:6}if(!s.elements.bgiframe&&f.browser.msie&&parseInt(f.browser.version.charAt(0))==6){f("select, object").each(function(){A=f(this).offset();A.bottom=A.top+f(this).height();A.right=A.left+f(this).width();if(y.top+L.dimensions.height>=A.top&&y.left+L.dimensions.width>=A.left){k.call(s)}})}y.left+=s.options.position.adjust.x;y.top+=s.options.position.adjust.y;F=s.getPosition();if(y.left!=F.left||y.top!=F.top){z=s.beforePositionUpdate.call(s,w);if(z===false){return s}s.cache.position=y;if(x===true){s.status.animated=true;s.elements.tooltip.animate(y,200,"swing",function(){s.status.animated=false})}else{s.elements.tooltip.css(y)}s.onPositionUpdate.call(s,w);if(typeof w!=="undefined"&&w.type&&w.type!=="mousemove"){f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_POSITION_UPDATED,"updatePosition")}}return s},updateWidth:function(w){var x;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"updateWidth")}else{if(w&&typeof w!=="number"){return f.fn.qtip.log.error.call(s,2,"newWidth must be of type number","updateWidth")}}x=s.elements.contentWrapper.siblings().add(s.elements.tip).add(s.elements.button);if(!w){if(typeof s.options.style.width.value=="number"){w=s.options.style.width.value}else{s.elements.tooltip.css({width:"auto"});x.hide();if(f.browser.msie){s.elements.wrapper.add(s.elements.contentWrapper.children()).css({zoom:"normal"})}w=s.getDimensions().width+1;if(!s.options.style.width.value){if(w>s.options.style.width.max){w=s.options.style.width.max}if(w<s.options.style.width.min){w=s.options.style.width.min}}}}if(w%2!==0){w-=1}s.elements.tooltip.width(w);x.show();if(s.options.style.border.radius){s.elements.tooltip.find(".qtip-betweenCorners").each(function(y){f(this).width(w-(s.options.style.border.radius*2))})}if(f.browser.msie){s.elements.wrapper.add(s.elements.contentWrapper.children()).css({zoom:"1"});s.elements.wrapper.width(w);if(s.elements.bgiframe){s.elements.bgiframe.width(w).height(s.getDimensions.height)}}return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_WIDTH_UPDATED,"updateWidth")},updateStyle:function(w){var z,A,x,y,B;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"updateStyle")}else{if(typeof w!=="string"||!f.fn.qtip.styles[w]){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.STYLE_NOT_DEFINED,"updateStyle")}}s.options.style=a.call(s,f.fn.qtip.styles[w],s.options.user.style);s.elements.content.css(q(s.options.style));if(s.options.content.title.text!==false){s.elements.title.css(q(s.options.style.title,true))}s.elements.contentWrapper.css({borderColor:s.options.style.border.color});if(s.options.style.tip.corner!==false){if(f("<canvas>").get(0).getContext){z=s.elements.tooltip.find(".qtip-tip canvas:first");x=z.get(0).getContext("2d");x.clearRect(0,0,300,300);y=z.parent("div[rel]:first").attr("rel");B=b(y,s.options.style.tip.size.width,s.options.style.tip.size.height);h.call(s,z,B,s.options.style.tip.color||s.options.style.border.color)}else{if(f.browser.msie){z=s.elements.tooltip.find('.qtip-tip [nodeName="shape"]');z.attr("fillcolor",s.options.style.tip.color||s.options.style.border.color)}}}if(s.options.style.border.radius>0){s.elements.tooltip.find(".qtip-betweenCorners").css({backgroundColor:s.options.style.border.color});if(f("<canvas>").get(0).getContext){A=g(s.options.style.border.radius);s.elements.tooltip.find(".qtip-wrapper canvas").each(function(){x=f(this).get(0).getContext("2d");x.clearRect(0,0,300,300);y=f(this).parent("div[rel]:first").attr("rel");r.call(s,f(this),A[y],s.options.style.border.radius,s.options.style.border.color)})}else{if(f.browser.msie){s.elements.tooltip.find('.qtip-wrapper [nodeName="arc"]').each(function(){f(this).attr("fillcolor",s.options.style.border.color)})}}}return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_STYLE_UPDATED,"updateStyle")},updateContent:function(A,y){var z,x,w;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"updateContent")}else{if(!A){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.NO_CONTENT_PROVIDED,"updateContent")}}z=s.beforeContentUpdate.call(s,A);if(typeof z=="string"){A=z}else{if(z===false){return}}if(f.browser.msie){s.elements.contentWrapper.children().css({zoom:"normal"})}if(A.jquery&&A.length>0){A.clone(true).appendTo(s.elements.content).show()}else{s.elements.content.html(A)}x=s.elements.content.find("img[complete=false]");if(x.length>0){w=0;x.each(function(C){f('<img src="'+f(this).attr("src")+'" />').load(function(){if(++w==x.length){B()}})})}else{B()}function B(){s.updateWidth();if(y!==false){if(s.options.position.type!=="static"){s.updatePosition(s.elements.tooltip.is(":visible"),true)}if(s.options.style.tip.corner!==false){n.call(s)}}}s.onContentUpdate.call(s);return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_CONTENT_UPDATED,"loadContent")},loadContent:function(w,z,A){var y;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"loadContent")}y=s.beforeContentLoad.call(s);if(y===false){return s}if(A=="post"){f.post(w,z,x)}else{f.get(w,z,x)}function x(B){s.onContentLoad.call(s);f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_CONTENT_LOADED,"loadContent");s.updateContent(B)}return s},updateTitle:function(w){if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"updateTitle")}else{if(!w){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.NO_CONTENT_PROVIDED,"updateTitle")}}returned=s.beforeTitleUpdate.call(s);if(returned===false){return s}if(s.elements.button){s.elements.button=s.elements.button.clone(true)}s.elements.title.html(w);if(s.elements.button){s.elements.title.prepend(s.elements.button)}s.onTitleUpdate.call(s);return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_TITLE_UPDATED,"updateTitle")},focus:function(A){var y,x,w,z;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"focus")}else{if(s.options.position.type=="static"){return f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.CANNOT_FOCUS_STATIC,"focus")}}y=parseInt(s.elements.tooltip.css("z-index"));x=6000+f("div.qtip[qtip]").length-1;if(!s.status.focused&&y!==x){z=s.beforeFocus.call(s,A);if(z===false){return s}f("div.qtip[qtip]").not(s.elements.tooltip).each(function(){if(f(this).qtip("api").status.rendered===true){w=parseInt(f(this).css("z-index"));if(typeof w=="number"&&w>-1){f(this).css({zIndex:parseInt(f(this).css("z-index"))-1})}f(this).qtip("api").status.focused=false}});s.elements.tooltip.css({zIndex:x});s.status.focused=true;s.onFocus.call(s,A);f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_FOCUSED,"focus")}return s},disable:function(w){if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"disable")}if(w){if(!s.status.disabled){s.status.disabled=true;f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_DISABLED,"disable")}else{f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.TOOLTIP_ALREADY_DISABLED,"disable")}}else{if(s.status.disabled){s.status.disabled=false;f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_ENABLED,"disable")}else{f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.TOOLTIP_ALREADY_ENABLED,"disable")}}return s},destroy:function(){var w,x,y;x=s.beforeDestroy.call(s);if(x===false){return s}if(s.status.rendered){s.options.show.when.target.unbind("mousemove.qtip",s.updatePosition);s.options.show.when.target.unbind("mouseout.qtip",s.hide);s.options.show.when.target.unbind(s.options.show.when.event+".qtip");s.options.hide.when.target.unbind(s.options.hide.when.event+".qtip");s.elements.tooltip.unbind(s.options.hide.when.event+".qtip");s.elements.tooltip.unbind("mouseover.qtip",s.focus);s.elements.tooltip.remove()}else{s.options.show.when.target.unbind(s.options.show.when.event+".qtip-create")}if(typeof s.elements.target.data("qtip")=="object"){y=s.elements.target.data("qtip").interfaces;if(typeof y=="object"&&y.length>0){for(w=0;w<y.length-1;w++){if(y[w].id==s.id){y.splice(w,1)}}}}delete f.fn.qtip.interfaces[s.id];if(typeof y=="object"&&y.length>0){s.elements.target.data("qtip").current=y.length-1}else{s.elements.target.removeData("qtip")}s.onDestroy.call(s);f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_DESTROYED,"destroy");return s.elements.target},getPosition:function(){var w,x;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"getPosition")}w=(s.elements.tooltip.css("display")!=="none")?false:true;if(w){s.elements.tooltip.css({visiblity:"hidden"}).show()}x=s.elements.tooltip.offset();if(w){s.elements.tooltip.css({visiblity:"visible"}).hide()}return x},getDimensions:function(){var w,x;if(!s.status.rendered){return f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.TOOLTIP_NOT_RENDERED,"getDimensions")}w=(!s.elements.tooltip.is(":visible"))?true:false;if(w){s.elements.tooltip.css({visiblity:"hidden"}).show()}x={height:s.elements.tooltip.outerHeight(),width:s.elements.tooltip.outerWidth()};if(w){s.elements.tooltip.css({visiblity:"visible"}).hide()}return x}})}function p(){var s,w,u,t,v,y,x;s=this;s.beforeRender.call(s);s.status.rendered=true;s.elements.tooltip='<div qtip="'+s.id+'" class="qtip '+(s.options.style.classes.tooltip||s.options.style)+'"style="display:none; -moz-border-radius:0; -webkit-border-radius:0; border-radius:0;position:'+s.options.position.type+';">  <div class="qtip-wrapper" style="position:relative; overflow:hidden; text-align:left;">    <div class="qtip-contentWrapper" style="overflow:hidden;">       <div class="qtip-content '+s.options.style.classes.content+'"></div></div></div></div>';s.elements.tooltip=f(s.elements.tooltip);s.elements.tooltip.appendTo(s.options.position.container);s.elements.tooltip.data("qtip",{current:0,interfaces:[s]});s.elements.wrapper=s.elements.tooltip.children("div:first");s.elements.contentWrapper=s.elements.wrapper.children("div:first").css({background:s.options.style.background});s.elements.content=s.elements.contentWrapper.children("div:first").css(q(s.options.style));if(f.browser.msie){s.elements.wrapper.add(s.elements.content).css({zoom:1})}if(s.options.hide.when.event=="unfocus"){s.elements.tooltip.attr("unfocus",true)}if(typeof s.options.style.width.value=="number"){s.updateWidth()}if(f("<canvas>").get(0).getContext||f.browser.msie){if(s.options.style.border.radius>0){m.call(s)}else{s.elements.contentWrapper.css({border:s.options.style.border.width+"px solid "+s.options.style.border.color})}if(s.options.style.tip.corner!==false){e.call(s)}}else{s.elements.contentWrapper.css({border:s.options.style.border.width+"px solid "+s.options.style.border.color});s.options.style.border.radius=0;s.options.style.tip.corner=false;f.fn.qtip.log.error.call(s,2,f.fn.qtip.constants.CANVAS_VML_NOT_SUPPORTED,"render")}if((typeof s.options.content.text=="string"&&s.options.content.text.length>0)||(s.options.content.text.jquery&&s.options.content.text.length>0)){u=s.options.content.text}else{if(typeof s.elements.target.attr("title")=="string"&&s.elements.target.attr("title").length>0){u=s.elements.target.attr("title").replace("\\n","<br />");s.elements.target.attr("title","")}else{if(typeof s.elements.target.attr("alt")=="string"&&s.elements.target.attr("alt").length>0){u=s.elements.target.attr("alt").replace("\\n","<br />");s.elements.target.attr("alt","")}else{u=" ";f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.NO_VALID_CONTENT,"render")}}}if(s.options.content.title.text!==false){j.call(s)}s.updateContent(u);l.call(s);if(s.options.show.ready===true){s.show()}if(s.options.content.url!==false){t=s.options.content.url;v=s.options.content.data;y=s.options.content.method||"get";s.loadContent(t,v,y)}s.onRender.call(s);f.fn.qtip.log.error.call(s,1,f.fn.qtip.constants.EVENT_RENDERED,"render")}function m(){var F,z,t,B,x,E,u,G,D,y,w,C,A,s,v;F=this;F.elements.wrapper.find(".qtip-borderBottom, .qtip-borderTop").remove();t=F.options.style.border.width;B=F.options.style.border.radius;x=F.options.style.border.color||F.options.style.tip.color;E=g(B);u={};for(z in E){u[z]='<div rel="'+z+'" style="'+((z.search(/Left/)!==-1)?"left":"right")+":0; position:absolute; height:"+B+"px; width:"+B+'px; overflow:hidden; line-height:0.1px; font-size:1px">';if(f("<canvas>").get(0).getContext){u[z]+='<canvas height="'+B+'" width="'+B+'" style="vertical-align: top"></canvas>'}else{if(f.browser.msie){G=B*2+3;u[z]+='<v:arc stroked="false" fillcolor="'+x+'" startangle="'+E[z][0]+'" endangle="'+E[z][1]+'" style="width:'+G+"px; height:"+G+"px; margin-top:"+((z.search(/bottom/)!==-1)?-2:-1)+"px; margin-left:"+((z.search(/Right/)!==-1)?E[z][2]-3.5:-1)+'px; vertical-align:top; display:inline-block; behavior:url(#default#VML)"></v:arc>'}}u[z]+="</div>"}D=F.getDimensions().width-(Math.max(t,B)*2);y='<div class="qtip-betweenCorners" style="height:'+B+"px; width:"+D+"px; overflow:hidden; background-color:"+x+'; line-height:0.1px; font-size:1px;">';w='<div class="qtip-borderTop" dir="ltr" style="height:'+B+"px; margin-left:"+B+'px; line-height:0.1px; font-size:1px; padding:0;">'+u.topLeft+u.topRight+y;F.elements.wrapper.prepend(w);C='<div class="qtip-borderBottom" dir="ltr" style="height:'+B+"px; margin-left:"+B+'px; line-height:0.1px; font-size:1px; padding:0;">'+u.bottomLeft+u.bottomRight+y;F.elements.wrapper.append(C);if(f("<canvas>").get(0).getContext){F.elements.wrapper.find("canvas").each(function(){A=E[f(this).parent("[rel]:first").attr("rel")];r.call(F,f(this),A,B,x)})}else{if(f.browser.msie){F.elements.tooltip.append('<v:image style="behavior:url(#default#VML);"></v:image>')}}s=Math.max(B,(B+(t-B)));v=Math.max(t-B,0);F.elements.contentWrapper.css({border:"0px solid "+x,borderWidth:v+"px "+s+"px"})}function r(u,w,s,t){var v=u.get(0).getContext("2d");v.fillStyle=t;v.beginPath();v.arc(w[0],w[1],s,0,Math.PI*2,false);v.fill()}function e(v){var t,s,x,u,w;t=this;if(t.elements.tip!==null){t.elements.tip.remove()}s=t.options.style.tip.color||t.options.style.border.color;if(t.options.style.tip.corner===false){return}else{if(!v){v=t.options.style.tip.corner}}x=b(v,t.options.style.tip.size.width,t.options.style.tip.size.height);t.elements.tip='<div class="'+t.options.style.classes.tip+'" dir="ltr" rel="'+v+'" style="position:absolute; height:'+t.options.style.tip.size.height+"px; width:"+t.options.style.tip.size.width+'px; margin:0 auto; line-height:0.1px; font-size:1px;">';if(f("<canvas>").get(0).getContext){t.elements.tip+='<canvas height="'+t.options.style.tip.size.height+'" width="'+t.options.style.tip.size.width+'"></canvas>'}else{if(f.browser.msie){u=t.options.style.tip.size.width+","+t.options.style.tip.size.height;w="m"+x[0][0]+","+x[0][1];w+=" l"+x[1][0]+","+x[1][1];w+=" "+x[2][0]+","+x[2][1];w+=" xe";t.elements.tip+='<v:shape fillcolor="'+s+'" stroked="false" filled="true" path="'+w+'" coordsize="'+u+'" style="width:'+t.options.style.tip.size.width+"px; height:"+t.options.style.tip.size.height+"px; line-height:0.1px; display:inline-block; behavior:url(#default#VML); vertical-align:"+((v.search(/top/)!==-1)?"bottom":"top")+'"></v:shape>';t.elements.tip+='<v:image style="behavior:url(#default#VML);"></v:image>';t.elements.contentWrapper.css("position","relative")}}t.elements.tooltip.prepend(t.elements.tip+"</div>");t.elements.tip=t.elements.tooltip.find("."+t.options.style.classes.tip).eq(0);if(f("<canvas>").get(0).getContext){h.call(t,t.elements.tip.find("canvas:first"),x,s)}if(v.search(/top/)!==-1&&f.browser.msie&&parseInt(f.browser.version.charAt(0))===6){t.elements.tip.css({marginTop:-4})}n.call(t,v)}function h(t,v,s){var u=t.get(0).getContext("2d");u.fillStyle=s;u.beginPath();u.moveTo(v[0][0],v[0][1]);u.lineTo(v[1][0],v[1][1]);u.lineTo(v[2][0],v[2][1]);u.fill()}function n(u){var t,w,s,x,v;t=this;if(t.options.style.tip.corner===false||!t.elements.tip){return}if(!u){u=t.elements.tip.attr("rel")}w=positionAdjust=(f.browser.msie)?1:0;t.elements.tip.css(u.match(/left|right|top|bottom/)[0],0);if(u.search(/top|bottom/)!==-1){if(f.browser.msie){if(parseInt(f.browser.version.charAt(0))===6){positionAdjust=(u.search(/top/)!==-1)?-3:1}else{positionAdjust=(u.search(/top/)!==-1)?1:2}}if(u.search(/Middle/)!==-1){t.elements.tip.css({left:"50%",marginLeft:-(t.options.style.tip.size.width/2)})}else{if(u.search(/Left/)!==-1){t.elements.tip.css({left:t.options.style.border.radius-w})}else{if(u.search(/Right/)!==-1){t.elements.tip.css({right:t.options.style.border.radius+w})}}}if(u.search(/top/)!==-1){t.elements.tip.css({top:-positionAdjust})}else{t.elements.tip.css({bottom:positionAdjust})}}else{if(u.search(/left|right/)!==-1){if(f.browser.msie){positionAdjust=(parseInt(f.browser.version.charAt(0))===6)?1:((u.search(/left/)!==-1)?1:2)}if(u.search(/Middle/)!==-1){t.elements.tip.css({top:"50%",marginTop:-(t.options.style.tip.size.height/2)})}else{if(u.search(/Top/)!==-1){t.elements.tip.css({top:t.options.style.border.radius-w})}else{if(u.search(/Bottom/)!==-1){t.elements.tip.css({bottom:t.options.style.border.radius+w})}}}if(u.search(/left/)!==-1){t.elements.tip.css({left:-positionAdjust})}else{t.elements.tip.css({right:positionAdjust})}}}s="padding-"+u.match(/left|right|top|bottom/)[0];x=t.options.style.tip.size[(s.search(/left|right/)!==-1)?"width":"height"];t.elements.tooltip.css("padding",0);t.elements.tooltip.css(s,x);if(f.browser.msie&&parseInt(f.browser.version.charAt(0))==6){v=parseInt(t.elements.tip.css("margin-top"))||0;v+=parseInt(t.elements.content.css("margin-top"))||0;t.elements.tip.css({marginTop:v})}}function j(){var s=this;if(s.elements.title!==null){s.elements.title.remove()}s.elements.title=f('<div class="'+s.options.style.classes.title+'">').css(q(s.options.style.title,true)).css({zoom:(f.browser.msie)?1:0}).prependTo(s.elements.contentWrapper);if(s.options.content.title.text){s.updateTitle.call(s,s.options.content.title.text)}if(s.options.content.title.button!==false&&typeof s.options.content.title.button=="string"){s.elements.button=f('<a class="'+s.options.style.classes.button+'" style="float:right; position: relative"></a>').css(q(s.options.style.button,true)).html(s.options.content.title.button).prependTo(s.elements.title).click(function(t){if(!s.status.disabled){s.hide(t)}})}}function l(){var t,v,u,s;t=this;v=t.options.show.when.target;u=t.options.hide.when.target;if(t.options.hide.fixed){u=u.add(t.elements.tooltip)}if(t.options.hide.when.event=="inactive"){s=["click","dblclick","mousedown","mouseup","mousemove","mouseout","mouseover","mouseleave","mouseover"];function y(z){if(t.status.disabled===true){return}clearTimeout(t.timers.inactive);t.timers.inactive=setTimeout(function(){f(s).each(function(){u.unbind(this+".qtip-inactive");t.elements.content.unbind(this+".qtip-inactive")});t.hide(z)},t.options.hide.delay)}}else{if(t.options.hide.fixed===true){t.elements.tooltip.bind("mouseover.qtip",function(){if(t.status.disabled===true){return}clearTimeout(t.timers.hide)})}}function x(z){if(t.status.disabled===true){return}if(t.options.hide.when.event=="inactive"){f(s).each(function(){u.bind(this+".qtip-inactive",y);t.elements.content.bind(this+".qtip-inactive",y)});y()}clearTimeout(t.timers.show);clearTimeout(t.timers.hide);t.timers.show=setTimeout(function(){t.show(z)},t.options.show.delay)}function w(z){if(t.status.disabled===true){return}if(t.options.hide.fixed===true&&t.options.hide.when.event.search(/mouse(out|leave)/i)!==-1&&f(z.relatedTarget).parents("div.qtip[qtip]").length>0){z.stopPropagation();z.preventDefault();clearTimeout(t.timers.hide);return false}clearTimeout(t.timers.show);clearTimeout(t.timers.hide);t.elements.tooltip.stop(true,true);t.timers.hide=setTimeout(function(){t.hide(z)},t.options.hide.delay)}if((t.options.show.when.target.add(t.options.hide.when.target).length===1&&t.options.show.when.event==t.options.hide.when.event&&t.options.hide.when.event!=="inactive")||t.options.hide.when.event=="unfocus"){t.cache.toggle=0;v.bind(t.options.show.when.event+".qtip",function(z){if(t.cache.toggle==0){x(z)}else{w(z)}})}else{v.bind(t.options.show.when.event+".qtip",x);if(t.options.hide.when.event!=="inactive"){u.bind(t.options.hide.when.event+".qtip",w)}}if(t.options.position.type.search(/(fixed|absolute)/)!==-1){t.elements.tooltip.bind("mouseover.qtip",t.focus)}if(t.options.position.target==="mouse"&&t.options.position.type!=="static"){v.bind("mousemove.qtip",function(z){t.cache.mouse={x:z.pageX,y:z.pageY};if(t.status.disabled===false&&t.options.position.adjust.mouse===true&&t.options.position.type!=="static"&&t.elements.tooltip.css("display")!=="none"){t.updatePosition(z)}})}}function o(u,v,A){var z,s,x,y,t,w;z=this;if(A.corner=="center"){return v.position}s=f.extend({},u);y={x:false,y:false};t={left:(s.left<f.fn.qtip.cache.screen.scroll.left),right:(s.left+A.dimensions.width+2>=f.fn.qtip.cache.screen.width+f.fn.qtip.cache.screen.scroll.left),top:(s.top<f.fn.qtip.cache.screen.scroll.top),bottom:(s.top+A.dimensions.height+2>=f.fn.qtip.cache.screen.height+f.fn.qtip.cache.screen.scroll.top)};x={left:(t.left&&(A.corner.search(/right/i)!=-1||(A.corner.search(/right/i)==-1&&!t.right))),right:(t.right&&(A.corner.search(/left/i)!=-1||(A.corner.search(/left/i)==-1&&!t.left))),top:(t.top&&A.corner.search(/top/i)==-1),bottom:(t.bottom&&A.corner.search(/bottom/i)==-1)};if(x.left){if(z.options.position.target!=="mouse"){s.left=v.position.left+v.dimensions.width}else{s.left=z.cache.mouse.x}y.x="Left"}else{if(x.right){if(z.options.position.target!=="mouse"){s.left=v.position.left-A.dimensions.width}else{s.left=z.cache.mouse.x-A.dimensions.width}y.x="Right"}}if(x.top){if(z.options.position.target!=="mouse"){s.top=v.position.top+v.dimensions.height}else{s.top=z.cache.mouse.y}y.y="top"}else{if(x.bottom){if(z.options.position.target!=="mouse"){s.top=v.position.top-A.dimensions.height}else{s.top=z.cache.mouse.y-A.dimensions.height}y.y="bottom"}}if(s.left<0){s.left=u.left;y.x=false}if(s.top<0){s.top=u.top;y.y=false}if(z.options.style.tip.corner!==false){s.corner=new String(A.corner);if(y.x!==false){s.corner=s.corner.replace(/Left|Right|Middle/,y.x)}if(y.y!==false){s.corner=s.corner.replace(/top|bottom/,y.y)}if(s.corner!==z.elements.tip.attr("rel")){e.call(z,s.corner)}}return s}function q(u,t){var v,s;v=f.extend(true,{},u);for(s in v){if(t===true&&s.search(/(tip|classes)/i)!==-1){delete v[s]}else{if(!t&&s.search(/(width|border|tip|title|classes|user)/i)!==-1){delete v[s]}}}return v}function c(s){if(typeof s.tip!=="object"){s.tip={corner:s.tip}}if(typeof s.tip.size!=="object"){s.tip.size={width:s.tip.size,height:s.tip.size}}if(typeof s.border!=="object"){s.border={width:s.border}}if(typeof s.width!=="object"){s.width={value:s.width}}if(typeof s.width.max=="string"){s.width.max=parseInt(s.width.max.replace(/([0-9]+)/i,"$1"))}if(typeof s.width.min=="string"){s.width.min=parseInt(s.width.min.replace(/([0-9]+)/i,"$1"))}if(typeof s.tip.size.x=="number"){s.tip.size.width=s.tip.size.x;delete s.tip.size.x}if(typeof s.tip.size.y=="number"){s.tip.size.height=s.tip.size.y;delete s.tip.size.y}return s}function a(){var s,t,u,x,v,w;s=this;u=[true,{}];for(t=0;t<arguments.length;t++){u.push(arguments[t])}x=[f.extend.apply(f,u)];while(typeof x[0].name=="string"){x.unshift(c(f.fn.qtip.styles[x[0].name]))}x.unshift(true,{classes:{tooltip:"qtip-"+(arguments[0].name||"defaults")}},f.fn.qtip.styles.defaults);v=f.extend.apply(f,x);w=(f.browser.msie)?1:0;v.tip.size.width+=w;v.tip.size.height+=w;if(v.tip.size.width%2>0){v.tip.size.width+=1}if(v.tip.size.height%2>0){v.tip.size.height+=1}if(v.tip.corner===true){v.tip.corner=(s.options.position.corner.tooltip==="center")?false:s.options.position.corner.tooltip}return v}function b(v,u,t){var s={bottomRight:[[0,0],[u,t],[u,0]],bottomLeft:[[0,0],[u,0],[0,t]],topRight:[[0,t],[u,0],[u,t]],topLeft:[[0,0],[0,t],[u,t]],topMiddle:[[0,t],[u/2,0],[u,t]],bottomMiddle:[[0,0],[u,0],[u/2,t]],rightMiddle:[[0,0],[u,t/2],[0,t]],leftMiddle:[[u,0],[u,t],[0,t/2]]};s.leftTop=s.bottomRight;s.rightTop=s.bottomLeft;s.leftBottom=s.topRight;s.rightBottom=s.topLeft;return s[v]}function g(s){var t;if(f("<canvas>").get(0).getContext){t={topLeft:[s,s],topRight:[0,s],bottomLeft:[s,0],bottomRight:[0,0]}}else{if(f.browser.msie){t={topLeft:[-90,90,0],topRight:[-90,90,-s],bottomLeft:[90,270,0],bottomRight:[90,270,-s]}}}return t}function k(){var s,t,u;s=this;u=s.getDimensions();t='<iframe class="qtip-bgiframe" frameborder="0" tabindex="-1" src="javascript:false" style="display:block; position:absolute; z-index:-1; filter:alpha(opacity=\'0\'); border: 1px solid red; height:'+u.height+"px; width:"+u.width+'px" />';s.elements.bgiframe=s.elements.wrapper.prepend(t).children(".qtip-bgiframe:first")}f(document).ready(function(){f.fn.qtip.cache={screen:{scroll:{left:f(window).scrollLeft(),top:f(window).scrollTop()},width:f(window).width(),height:f(window).height()}};var s;f(window).bind("scroll",function(t){clearTimeout(s);s=setTimeout(function(){if(t.type==="scroll"){f.fn.qtip.cache.screen.scroll={left:f(window).scrollLeft(),top:f(window).scrollTop()}}else{f.fn.qtip.cache.screen.width=f(window).width();f.fn.qtip.cache.screen.height=f(window).height()}for(i=0;i<f.fn.qtip.interfaces.length;i++){var u=f.fn.qtip.interfaces[i];if(u.status.rendered===true&&(u.options.position.type!=="static"||u.options.position.adjust.scroll&&t.type==="scroll")){u.updatePosition(t,true)}}},100)});f(document).bind("mousedown.qtip",function(t){if(f(t.target).parents("div.qtip").length===0){f(".qtip[unfocus]").each(function(){var u=f(this).qtip("api");if(f(this).is(":visible")&&!u.status.disabled&&f(t.target).add(u.elements.target).length>1){u.hide(t)}})}})});f.fn.qtip.interfaces=[];f.fn.qtip.log={error:function(){return this}};f.fn.qtip.constants={};f.fn.qtip.defaults={content:{prerender:false,text:false,url:false,data:null,title:{text:false,button:false}},position:{target:false,corner:{target:"bottomRight",tooltip:"topLeft"},adjust:{x:0,y:0,mouse:true,screen:false,scroll:true},type:"absolute",container:false},show:{when:{target:false,event:"mouseover"},effect:{type:"fade",length:100},delay:140,solo:false,ready:false},hide:{when:{target:false,event:"mouseout"},effect:{type:"fade",length:100},delay:0,fixed:false},api:{beforeRender:function(){},onRender:function(){},beforePositionUpdate:function(){},onPositionUpdate:function(){},beforeShow:function(){},onShow:function(){},beforeHide:function(){},onHide:function(){},beforeContentUpdate:function(){},onContentUpdate:function(){},beforeContentLoad:function(){},onContentLoad:function(){},beforeTitleUpdate:function(){},onTitleUpdate:function(){},beforeDestroy:function(){},onDestroy:function(){},beforeFocus:function(){},onFocus:function(){}}};f.fn.qtip.styles={defaults:{background:"white",color:"#111",overflow:"hidden",textAlign:"left",width:{min:0,max:250},padding:"5px 9px",border:{width:1,radius:0,color:"#d3d3d3"},tip:{corner:false,color:false,size:{width:13,height:13},opacity:1},title:{background:"#e1e1e1",fontWeight:"bold",padding:"7px 12px"},button:{cursor:"pointer"},classes:{target:"",tip:"qtip-tip",title:"qtip-title",button:"qtip-button",content:"qtip-content",active:"qtip-active"}},cream:{border:{width:3,radius:0,color:"#F9E98E"},title:{background:"#F0DE7D",color:"#A27D35"},background:"#FBF7AA",color:"#A27D35",classes:{tooltip:"qtip-cream"}},light:{border:{width:3,radius:0,color:"#E2E2E2"},title:{background:"#f1f1f1",color:"#454545"},background:"white",color:"#454545",classes:{tooltip:"qtip-light"}},dark:{border:{width:3,radius:0,color:"#303030"},title:{background:"#404040",color:"#f3f3f3"},background:"#505050",color:"#f3f3f3",classes:{tooltip:"qtip-dark"}},red:{border:{width:3,radius:0,color:"#CE6F6F"},title:{background:"#f28279",color:"#9C2F2F"},background:"#F79992",color:"#9C2F2F",classes:{tooltip:"qtip-red"}},green:{border:{width:3,radius:0,color:"#A9DB66"},title:{background:"#b9db8c",color:"#58792E"},background:"#CDE6AC",color:"#58792E",classes:{tooltip:"qtip-green"}},blue:{border:{width:3,radius:0,color:"#ADD9ED"},title:{background:"#D0E9F5",color:"#5E99BD"},background:"#E5F6FE",color:"#4D9FBF",classes:{tooltip:"qtip-blue"}}}})(joms.jQuery);

(function($) {

$.fn.stretchToFit = function(windowResize) {

	(function stretchToFit(target)
	{
		target.css('width', '100%');

		// Google Chrome doesn't return correct outerWidth() else things would be nicer.
		// css('width', width()*2 - outerWidth(true));
		target.css('width', target.width() - parseInt(target.css('borderLeftWidth'))
		                                   - parseInt(target.css('borderRightWidth'))
		                                   - parseInt(target.css('padding-left'))
		                                   - parseInt(target.css('padding-right')));

	})(this);

	return this;

};

})(joms.jQuery);


(function($) {

$.fn.defaultValue = function(defaultText, defaultClass) {

	var target = this;

	function Focus()
	{
		if (target.val()==defaultText)
		{
			target.val('');
		}

		target.removeClass(defaultClass);
	}

	function Blur()
	{
		var _defaultText  = target.data('defaultText');
		var _defaultClass = target.data('defaultClass');

		var empty = target.val().length < 1 ||
		            target.val() == _defaultText ||
		            target.hasClass(_defaultClass);

		if (empty) target.val(defaultText);

		if (defaultClass != _defaultClass)
			target.removeClass('_defaultClass');

		target.toggleClass(defaultClass, empty);
	}

	target
		.focus(Focus)
		.blur(Blur);

	Blur();

	target.data('defaultText', defaultText);
	target.data('defaultClass', defaultClass);

	return target;

}

})(joms.jQuery);


(function($) {

$.fn.serializeJSON = function() {

	var params = {};

	$.each(this.serializeArray(), function()
	{
		params[this.name] = this.value;
	})	

	return params;
}

})(joms.jQuery);


/** tipsy **/
(function($) {
    $.fn.tipsy = function(options) {

        options = $.extend({}, $.fn.tipsy.defaults, options);
        
        return this.each(function() {
            
            var opts = $.fn.tipsy.elementOptions(this, options);
            
            $(this).hover(function() {

                $.data(this, 'cancel.tipsy', true);

                var tip = $.data(this, 'active.tipsy');
                if (!tip) {
                    tip = $('<div class="tipsy"><div class="tipsy-inner"/></div>');
                    tip.css({position: 'absolute', zIndex: 100000});
                    $.data(this, 'active.tipsy', tip);
                }

                if ($(this).attr('title') || typeof($(this).attr('original-title')) != 'string') {
                    $(this).attr('original-title', $(this).attr('title') || '').removeAttr('title');
                }

                var title;
                if (typeof opts.title == 'string') {
                    title = $(this).attr(opts.title == 'title' ? 'original-title' : opts.title);
                } else if (typeof opts.title == 'function') {
                    title = opts.title.call(this);
                }

                tip.find('.tipsy-inner')[opts.html ? 'html' : 'text'](title || opts.fallback);

                var pos = $.extend({}, $(this).offset(), {width: this.offsetWidth, height: this.offsetHeight});
                tip.get(0).className = 'tipsy'; // reset classname in case of dynamic gravity
                tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
                var actualWidth = tip[0].offsetWidth, actualHeight = tip[0].offsetHeight;
                var gravity = (typeof opts.gravity == 'function') ? opts.gravity.call(this) : opts.gravity;

                switch (gravity.charAt(0)) {
                    case 'n':
                        tip.css({top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-north');
                        break;
                    case 's':
                        tip.css({top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}).addClass('tipsy-south');
                        break;
                    case 'e':
                        tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}).addClass('tipsy-east');
                        break;
                    case 'w':
                        tip.css({top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}).addClass('tipsy-west');
                        break;
                }

                if (opts.fade) {
                    tip.css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: 0.8});
                } else {
                    tip.css({visibility: 'visible'});
                }

            }, function() {
                $.data(this, 'cancel.tipsy', false);
                var self = this;
                setTimeout(function() {
                    if ($.data(this, 'cancel.tipsy')) return;
                    var tip = $.data(self, 'active.tipsy');
                    if (opts.fade) {
                        tip.stop().fadeOut(function() {$(this).remove();});
                    } else {
                        tip.remove();
                    }
                }, 100);

            });
            
        });
        
    };
    
    // Overwrite this method to provide options on a per-element basis.
    // For example, you could store the gravity in a 'tipsy-gravity' attribute:
    // return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
    // (remember - do not modify 'options' in place!)
    $.fn.tipsy.elementOptions = function(ele, options) {
        return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
    };
    
    $.fn.tipsy.defaults = {
        fade: false,
        fallback: '',
        gravity: 'n',
        html: false,
        title: 'title'
    };
    
    $.fn.tipsy.autoNS = function() {
        return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 's' : 'n';
    };
    
    $.fn.tipsy.autoWE = function() {
        return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'e' : 'w';
    };
    
})(joms.jQuery);
