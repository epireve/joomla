// JavaScript Document
joms.jQuery(document).ready(function() {
	// Iterate through each drop-down box options and generate HTML on-the-fly
	
	// set base z-index to be 10000 , and it will be decreased as we go downwards to ensure all 
	// dropdowns are on top
	
	var baseZIndex = 10000;
	
	joms.jQuery('select.js_PrivacySelect').each(function() {
		var tmpHTML = "";
		var currValue;
		
		// get current value of pre-selected item from the dropdown
		joms.jQuery(this).find('option').each(function() {
			// console.log(joms.jQuery(this).attr('selected'));
			if(joms.jQuery(this).attr('selected')) {
				currValue = joms.jQuery(this).val();
			}
		});
		// alert(currValue);
		
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
	
	joms.jQuery('.js_PriContainer').each(function() {
		joms.jQuery(this).css('z-index', baseZIndex);
		baseZIndex -= 20;
		// console.log(baseZIndex);
	});
	
	joms.jQuery('.js_dropDownChild').live('click',function(e) {
		e.preventDefault();
		var selectedVal = joms.jQuery(this).attr('name');
		var selectedText = "";
		// console.log('clicked. value - ' + selectedVal);
		
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
		// console.log(currShowVal);
		dropDownObj.removeClass('js_dropSelect-' + currShowVal).addClass('js_dropSelect-' + selectedVal).attr('name', selectedVal).html('<strong>' + selectedText + '</strong><span></span>');
		// console.log(dropDownObj.attr('name') + ' - ' + selectedText);
		
		// hide box after selecting
		joms.jQuery(this).parent().parent().parent().parent().data('state',0).removeClass('js_Current').find('dd').hide();
	});
	
	// click trigger to open
	joms.jQuery('.js_dropDownMaster dt').live('click', function(e) {
		e.preventDefault();
		if (joms.jQuery(this).parent().data('state')) {
			joms.jQuery(this).parent().data('state',0).removeClass('js_Current').find('dd').hide();
		} else {
			joms.jQuery(this).parent().data('state',1).addClass('js_Current').find('dd').show();
		}	
	})
});

/*
HTML Template

<dl>
	<dt>CURRENT-SELECTION</dt>
	<dd>
		<ul>
			<li><a href="#nothing" name="VALUE">OPTION</a></li>
			<li><a href="#nothing" name="VALUE">OPTION</a></li>
			<li><a href="#nothing" name="VALUE">OPTION</a></li>
			<li><a href="#nothing" name="VALUE">OPTION</a></li>
		</ul>
	</dd>
</dl>
*/