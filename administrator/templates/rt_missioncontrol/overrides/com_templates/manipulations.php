<?php

if ($layout == 'edit') {

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}

	$pq = phpQuery::newDocument($buffer);

	pq('.width-40 + .width-60')->addClass('wrap-around');
	pq('div.pane-sliders > div[style="display:none;"]:last-child')->parent()->addClass('no-borders');
    

	$buffer = $pq->getDocument()->htmlOuter();
	$this->document->setBuffer($buffer, 'component');
} 