<?php
/**
 * @category	Library
 * @package		JomSocial
 * @subpackage	mobile
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die('Restricted access');

class CDocumentMobile extends JDocumentHTML
{
	// This constructor intended goal is to look through
	// what has been previously assigned to the old document
	// and selectively bring the required ones back.
	public function __construct($document)
	{
		foreach ($document->_custom as $customTag)
		{
			// Restore azrul system plugin
			if(strstr($customTag, 'plugins/system/pc_includes'))
			{
				$this->addCustomTag($customTag);
			}
		}

		$this->setMetaData('viewport', 'width=device-width, initial-scale=1, user-scalable=no');
		parent::__construct();
	}

	public function render()
	{
		$template = new CTemplateHelper();
		$file = $template->getTemplateFile('mobile.document');

		ob_start();
			require($file);
			$data = ob_get_contents();
		ob_end_clean();

		$data = $this->_parseTemplate($data);
		return $data;
	}
}

class CDocumentMobileAjax extends CDocumentMobile
{
	public function render()
	{
		// When dispatch() is called from JApplication,
		// the rendered content is immediately stored
		// in the component buffer, so instead of rerendering
		// our component, we'll extract it out from the buffer.
		$this->content = $this->_buffer['component'][''];
		unset($this->_buffer);

		// and send the document back within jax response.
		$objResponse = new JAXResponse();
		$objResponse->addScriptCall('__callback', $this);

		return $objResponse->sendResponse();
	}
}

class CMobile
{
	public function showSwitcher()
	{
		CFactory::load('libraries', 'template');

		$uri   = JFactory::getURI();
			// Will see if there's a nicer solution to this
			$query = $uri->getQuery(true);
			unset($query['screen']);
			$query = $uri->buildQuery($query);
			$uri->setQuery($query);
		$uri = $uri->toString();

		// Build links
		$link = array(
			'mobile'  => $uri . '&screen=mobile',
			'desktop' => $uri . '&screen=desktop'
		);

		$tmpl = new CTemplate();
		$tmpl->set('link'    , $link);
		$tmpl->set('viewtype', JRequest::setVar('screen'));

		echo $tmpl->fetch('mobile.switcher');

	}
}

?>