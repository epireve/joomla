function submitbutton(pressbutton) {
	addOverlay();
    submitform(pressbutton);
}
function addOverlay() {
	var overlay = document.createElement('div');
	overlay.setAttribute('id', 'overlay');
	overlay.setAttribute('class', 'overlay');
	document.body.appendChild(overlay);
}