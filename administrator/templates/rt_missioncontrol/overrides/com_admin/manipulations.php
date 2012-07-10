<?php
if ($view == 'help') {
	// needed bits

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}

	$pq = phpQuery::newDocument($buffer);
	
	pq('ul.helpmenu')->wrapAll('<div class="mc-toolbar" id="toolbar" />');
	
	$buffer = $pq->getDocument()->htmlOuter();
	
	$this->document->setBuffer($buffer, 'component');
	
}