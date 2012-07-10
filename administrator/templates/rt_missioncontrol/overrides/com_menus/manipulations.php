<?php
if ($view == 'item' && ($layout == 'edit' or $layout == 'add')) {

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}

	$js_init = "window.addEvent('domready', function(){
	 	toggler = document.id('mc-menu-tabs')
	  	element = document.id('mc-menu')
	  	if(element) {
	  		document.switcher = new JSwitcher(toggler, element);
	  	}
	});";

	// add the tabber switcher js
	$this->document->addScript($this->templateUrl.'/js/MC.Switcher.js');
	$this->addInlineScript($js_init);

	$pq = phpQuery::newDocument($buffer);

	$framework = '<div id="mc-menu-key" class="adminform">
					<ul class="adminformlist" />
				  </div>
				  <ul id="mc-menu-tabs" class="mc-form-tabs">
				  	<li><a id="options" class="active">Menu Options</a></li>
					<li><a id="modules">Module Assignments &amp; MetaData</a></li>

				  </ul>
				  <div id="mc-menu" class="adminform">
				   	<div id="page-options">
				   		<div id="mc-details">
				   			<div class="mc-block">
				   				<h3>Menu Details</h3>
				   			</div>
				   		</div>
				   		<div id="mc-options">
				   			<div class="mc-block" />   		
				   		</div>
				   	</div>
				   	<div id="page-modules">
				   		<div id="mc-assignments">
				   			<div class="mc-block" />
				   		</div>
				   		<div id="mc-metadata">
				   			<div class="mc-block" />
				   		</div>
				   	</div>
				  </div>';
	
	
	pq('form[name=adminForm')->prepend($framework);
	pq('#mc-menu-key ul.adminformlist')->append(pq('.width-60 fieldset.adminform .adminformlist > li:lt(4)'));
	pq('#mc-details .mc-block')->append(pq('.width-60 fieldset.adminform ul.adminformlist'));
	pq('#mc-menu')->append(pq('form#item-form input[type="hidden"]'));
	pq('#mc-assignments .mc-block')->append(pq('.width-40 .panel:last'))->removeClass('panel');
	pq('#mc-metadata .mc-block')->append(pq('.width-40 .panel:last'))->removeClass('panel');
	pq('#mc-options .mc-block')->append(pq('.width-40 .panel'))->removeClass('panel');
	
	pq('.width-60.fltlft')->remove();
	pq('.width-40.fltrt')->remove();
    pq('#jform_type-lbl')->next()->addClass('mc-menu-type');
    pq('.mc-menu-type')->next()->attr('rel',"{handler:'clone', target:'menu-types', size: {x: 550, y: 550}}");
    pq('#jform_link')->attr('size',40);

	
	$buffer = $pq->getDocument()->htmlOuter();
	
	$this->document->setBuffer($buffer, 'component');
} 

