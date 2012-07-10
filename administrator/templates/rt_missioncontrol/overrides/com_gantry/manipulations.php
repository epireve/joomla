<?php
if ($layout == 'edit') {

	if (!class_exists('phpQuery')) {
	    require_once($this->templatePath . "/lib/phpQuery.php");
	}

	$pq = phpQuery::newDocument($buffer);
	
	pq('form[name=adminForm] fieldset.adminform')->parents('form[name=adminForm])')->wrapInner('<div class="mc-form-frame" />');

	
	$buffer = $pq->getDocument()->htmlOuter();
	$this->document->setBuffer($buffer, 'component');

}