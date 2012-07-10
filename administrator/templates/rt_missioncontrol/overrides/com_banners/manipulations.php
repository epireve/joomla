<?php

if ($layout == 'edit' or $layout == 'add') {

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}
	$pq = phpQuery::newDocument($buffer);
		
	pq('#image > [id*="width"]')->wrapAll('<div class="width-row" />');
	pq('#image > [id*="height"]')->wrapAll('<div class="height-row" />');

	$buffer = $pq->getDocument()->htmlOuter();
	$this->document->setBuffer($buffer, 'component');
} 
