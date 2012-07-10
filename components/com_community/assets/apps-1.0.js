// JavaScript Document

// Show about box
function cAppsAbout(appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxShowAbout', '"+appName+"');";
	cWindowShow(ajaxCall, 'About', 450, 200);
}

// Show privacy box
function cAppsPrivacy(appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxShowPrivacy', '"+appName+"');";
	cWindowShow(ajaxCall, 'Privacy Setting', 450, 300);
}

function cAppPrivacySave(){
	var value   = joms.jQuery('input[name=privacy]:checked').val();
	var appName = joms.jQuery('input[name=appname]').val();
	jax.call('community', 'apps,ajaxSavePrivacy', appName, value);
}

var appsremoveTitle	='';
// Show privacy box
function cAppsRemove(appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxRemove', '"+appName+"');";
	cWindowShow(ajaxCall, appsremoveTitle , 450, 100);	
}

// Show privacy box
function cAppsAdd(appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxAdd', '"+appName+"');";
	cWindowShow(ajaxCall, 'Add', 450, 100);
}

// Show privacy box
function cAppsPublishToggle(appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxPublishToggle', '"+appName+"');";
	cWindowShow(ajaxCall, 'Privacy Setting', 450, 200);
}

// Load param box
function cAppSetting(id, appName){
	var ajaxCall = "jax.call('community', 'apps,ajaxShowSettings', '"+id+"', '"+appName+"');";
	cWindowShow(ajaxCall, '', 450, 300);
}

// Save params
function cAppSaveSetting(){
	jax.call('community', 'apps,ajaxSaveSettings', jax.getFormValues('appSetting'));
}
