/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var getCookies = function(){
	var pairs = document.cookie.split(";");
	var cookies = {};
	for (var i=0; i<pairs.length; i++){
		var pair = pairs[i].split("=");
		cookies[pair[0]] = unescape(pair[1]);
	}
	return cookies;
}

function isNumber(n) {
	return !isNaN(parseFloat(n)) && isFinite(n);
}

function createCookie(name,value,days,baseDomain) {
	baseDomain = (baseDomain == undefined) ? '' : '; domain='+baseDomain;
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	
	document.cookie = name+"="+value+expires+"; path=/"+baseDomain;
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	var baseDomain = '';
	var myCookies = getCookies();
		
	for (cook in myCookies)
	{
		if (cook.match(/base_domain_/) )
		{
			baseDomain = myCookies[cook];
			break;
		}
	}
	createCookie(name,"",-1, baseDomain);
}

function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.href);
  if(results == null)
	return "";
  else
	return decodeURIComponent(results[1].replace(/\+/g, " "));
}