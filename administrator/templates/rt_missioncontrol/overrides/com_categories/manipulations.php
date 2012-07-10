<?php
if ($layout == 'edit' or $layout == 'add') {

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}

	$js_init = "window.addEvent('domready', function(){
	 	toggler = document.id('mc-category-tabs')
	  	element = document.id('mc-category')
	  	if(element) {
	  		document.switcher = new JSwitcher(toggler, element);
	  	}
	});";

	// add the tabber switcher js
	$this->document->addScript($this->templateUrl.'/js/MC.Switcher.js');
	$this->addInlineScript($js_init);

	$pq = phpQuery::newDocument($buffer);

	$framework = '<div id="mc-category-key" class="adminform" />
				  <ul id="mc-category-tabs" class="mc-form-tabs">
				  	<li><a id="editor" class="active">Description</a></li>
					<li><a id="publishing">Publishing &amp; MetaData</a></li>
					<li><a id="advanced">Advanced &amp; Permissions</a></li>
				  </ul>
				  <div id="mc-category" class="adminform">
				   	<div id="page-editor" />
				   	<div id="page-publishing">
				   		<div id="mc-pubdata">
				   			<div class="mc-block">
				   				<h3>Publishing Options</h3>
				   			</div>
				   		</div>
				   		<div id="mc-metadata">
				   			<div class="mc-block">
				   				<h3>MetaData</h3>
				   			</div>
				   		</div>
				   	</div>
				   	<div id="page-advanced">
				   		<div id="mc-settings">
				   			<div class="mc-block">
				   				<h3>Advanced Options</h3>
				   			</div>
				   		</div>
				   		<div id="mc-permissions">
				   			<div class="mc-block">
				   				<h3>Category Permissions</h3>
				   			</div>
				   		</div>
				   	</div>
				  </div>';
	
	
	pq('form[name=adminForm')->prepend($framework);
	pq('#mc-category-key')->append(pq('form[name=adminForm] fieldset.adminform > ul.adminformlist'));
	pq('#page-editor')->append(pq('form[name=adminForm] .width-60.fltlft > fieldset.adminform'));
	pq('#mc-pubdata .mc-block')->append(pq('.width-40 .panel:eq(0) fieldset.panelform'));
	pq('#mc-settings .mc-block')->append(pq('.width-40 .panel:eq(1) fieldset.panelform'));
	pq('#mc-metadata .mc-block')->append(pq('.width-40 .panel:eq(2) fieldset.panelform'));
	pq('#mc-permissions .mc-block')->append(pq('.width-100 .pane-sliders'));
	pq('#jform_extension')->parent('li')->addClass('mc-hidden');
	
	// remove unused bits
	pq('#mc-category-key span.faux-label')->parent('li')->remove();
	pq('#page-editor legend')->remove();
	pq('#page-editor .adminform > label')->remove();
	pq('#mc-permissions .pane-sliders:eq(0)')->remove();
	pq('.rule-desc')->remove();
	pq('.width-60.fltlft')->remove();
	pq('.width-40.fltrt')->remove();
	pq('.width-100.fltft')->remove();
	
	$buffer = $pq->getDocument()->htmlOuter();
	$this->document->setBuffer($buffer, 'component');
	

} 

