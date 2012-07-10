function adminPraiseCheckFrame() {
	if(typeof parent.frames[0] != 'undefined') {
		window.parent.document.getElementById('sbox-window').close();
		systemMessage = document.getElementById('system-message');
		refElement = window.parent.document.getElementById('hiddenDiv');

		clonedMessage = systemMessage.cloneNode(true);
			
		refElement.appendChild(clonedMessage);
		setTimeout('adminPraiseRemoveMessage()', 2000);
	}
}
function adminPraiseRemoveMessage() {
	refElement = window.parent.document.getElementById('system-message')
	refElement.parentNode.removeChild(refElement);
}

function adminPraiseAjax(params) {
	if(MooTools.version > "1.12") {
		var adminPraiseRequest = new Request({
			url: params.url,
			method: params.method,
			onComplete: params.onComplete
		});

		adminPraiseRequest.send();

	} else {
		adminPraiseRequest = new Ajax(params.url, {
			method: params.method,
			onComplete: params.onComplete
		});
		adminPraiseRequest.request();
	}
}
function adminPraiseUnpublishModule(id) {
	moduleId = id;
	params = new Object();
	params.url = adminPraiseLiveSite + 'administrator/templates/adminpraise3/lib/apAjax.php?action=unpublishModule&id='+id ;
	params.method = 'get';
	params.onComplete = removeModules;

	adminPraiseAjax(params);
}

function removeModules(responseText) {
	if(responseText != '') {
		var systemMessage = adminPraiseCreateSystemMessage('Error', "Couldn't delete the module.");
		adminPraiseOutputSystemMessage(systemMessage)
		setTimeout('adminPraiseRemoveMessage()', 2000);
	} else {
		var module = document.getElementById('module-'+moduleId);
		var parent = module.parentNode;
		parent.removeChild(module);
		var systemMessage = adminPraiseCreateSystemMessage('Message', 'The module was deleted.');
		adminPraiseOutputSystemMessage(systemMessage);
		setTimeout('adminPraiseRemoveMessage()', 2000);
	}
}

function adminPraiseCreateSystemMessage(type, message) {
	var error = '<dl id="system-message">' +
					'<dt class="message">' + type + '</dt>' +
					'<dd class="message message fade">' +
						'<ul>' +
							'<li>' + message + '</li>' +
						'</ul>' +
					'</dd>' +
					'</dl>';
	
	return error;
}

function adminPraiseOutputSystemMessage(message) {
	document.getElementById('hiddenDiv').innerHTML = message;	
}