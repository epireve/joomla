<?php
/**
 * @category	Core
 * @package		JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Jom Social Component Controller
 */
class CommunityControllerTemplates extends CommunityController
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function publish()
	{
	    JRequest::checkToken() or jexit( 'Invalid Token' );
	    
	    $template   = JRequest::getVar( 'template' , 'POST' );
	    $model		= $this->getModel( 'Configuration' );
	    $model->updateTemplate( $template );
	    $mainframe  = JFactory::getApplication();
	    
	    $mainframe->redirect( JRoute::_('index.php?option=com_community&view=templates' , false ) , JText::sprintf( 'COM_COMMUNITY_TEMPLATE_CONFIGURATION_UPDATED' , $template ) );
	}
	
	public function ajaxChangeTemplate( $templateName )
	{
		$response	= new JAXResponse();
		
		if( $templateName == 'none' )
		{
			// Previously user might already selected a template, hide the files
			$response->addScriptCall( 'azcommunity.resetTemplateFiles();' );
			
			// Close all files if it is already editing
			$response->addScriptCall( 'azcommunity.resetTemplateForm();' );
		}
		else
		{
			$html	= '<div id="template-files">';
			$html	.= '<h3>' . JText::_('COM_COMMUNITY_SELECT_FILE') . '</h3>';
			

			$templatePath	= COMMUNITY_BASE_PATH . DS . 'templates' . DS . JString::strtolower( $templateName );
			
			$files			= array();
	
			if( $handle = @opendir($templatePath) )
			{
				while( false !== ( $file = readdir( $handle ) ) )
				{
					$filePath	= $templatePath . DS . $file;
					
					// Do not get '.' or '..' or '.svn' since we only want folders.
					if( $file != '.' && $file != '..' && $file != '.svn' && !(JString::stristr( $file , '.js')) && !is_dir($filePath) )
					{
						$files[]	= $file;
					}
				}
			}
			sort($files);

			$html	.= '<select name="file" onchange="azcommunity.editTemplate(\'' . $templateName . '\',this.value);">';
			$html	.= '<option value="none" selected="true">' . JText::_('COM_COMMUNITY_SELECT_FILE') . '</option>';
			for( $i = 0; $i < count( $files ); $i++ )
			{
				$html .= '<option value="' . $files[$i] . '">' . $files[$i] . '</option>';
			}
			$html	.= '</select>';
			
			$html	.= '</div>';
			$response->addAssign( 'templates-files-container' , 'innerHTML' , $html );
		}

		return $response->sendResponse();
	}
	
	/**
	 * Ajax method to load a template file
	 *
	 * @param	$templateName	The template name
	 * @param	$fileName	The file name
	 **/	 
	public function ajaxLoadTemplateFile( $templateName , $fileName , $override )
	{
		$response	= new JAXResponse();

		if( $fileName == 'none')
		{
			$response->addScriptCall( 'azcommunity.resetTemplateForm();' );
		}
		else
		{
			$filePath	= COMMUNITY_BASE_PATH . DS . 'templates' . DS . JString::strtolower( $templateName ) . DS . JString::strtolower( $fileName );
			
			if( $override )
				$filePath	= JPATH_ROOT . DS . 'templates' . DS . JString::strtolower( $templateName ) . DS . 'html' . DS . 'com_community' . DS . JString::strtolower( $fileName );
			
			jimport('joomla.filesystem.file');
			
			$contents	= JFile::read( $filePath );
	
			$response->addAssign( 'data' , 'value' , $contents );
			$response->addAssign( 'fileName' , 'value' , $fileName );
			$response->addAssign( 'templateName' , 'value' , $templateName );
			$response->addAssign( 'filePath' , 'innerHTML' , $filePath );
		}

		return $response->sendResponse();
	}
	
	public function ajaxSaveTemplateFile( $templateName , $fileName , $fileData , $override )
	{
		$response	= new JAXResponse();
		
		$filePath	= COMMUNITY_BASE_PATH . DS . 'templates' . DS . JString::strtolower( $templateName ) . DS . JString::strtolower( $fileName );

		if( $override )
			$filePath	= JPATH_ROOT . DS . 'templates' . DS . JString::strtolower( $templateName ) . DS . 'html' . DS . 'com_community' . DS . JString::strtolower( $fileName );
		
		jimport( 'joomla.filesystem.file' );
		
		if( JFile::write( $filePath , $fileData ) )
		{
			$response->addScriptCall('joms.jQuery("#status").html("' . JText::sprintf('%1$s saved successfully.' , $fileName ) . '");');
			$response->addScriptCall('joms.jQuery("#status").attr("class","info");');
		}
		else
		{
			$response->addScriptCall( 'alert' , JText::_('COM_COMMUNITY_TEMPLATES_FILE_SAVE_ERROR') );
		}

		return $response->sendResponse();
	}
	
	public function save()
	{
		$mainframe	=& JFactory::getApplication();
		$params		= JRequest::getVar('params', array(), 'post', 'array');
		$element	= JRequest::getString( 'id' );
		$override	= JRequest::getVar( 'override' );
        $task       = JRequest::getCmd( 'task' );     

		if( $override )
		{
			$xml	= JPATH_ROOT . DS . 'templates' . DS . $element . DS . 'html' . DS .'com_community' . DS . COMMUNITY_TEMPLATE_XML;
			$file	= JPATH_ROOT . DS . 'templates' . DS . $element . DS . 'html' . DS . 'com_community' . DS . 'params.ini';
			
			if( !JFile::exists( $xml ) )
			{
				$file	= JPATH_ROOT . DS . 'templates' . DS . $element . DS . 'params.ini';
			}
		}
		else
		{
			$file		= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'templates' . DS . $element . DS . 'params.ini';
		}

		jimport('joomla.filesystem.file');
		
		$registry	= new JRegistry();
		$registry->loadArray($params);
		$raw		= $registry->toString();
		
		if( !empty( $raw ) )
		{
			if( !JFile::write( $file , $raw ) )
			{
				$mainframe->redirect( 'index.php?option=com_community&view=templates&layout=edit&id=' . $element , JText::_('COM_COMMUNITY_TEMPLATES_PARAMETERS_SAVE_ERROR') , 'error' );
			}
		}
		
		switch($task){
            case 'apply';
                $link   = 'index.php?option=com_community&view=templates&layout=edit&override='.$override.'&id='.$element;
                break;
            case 'save';
            default:
                $link   = 'index.php?option=com_community&view=templates';
                break;
        }
		
		$mainframe->redirect( $link , JText::_('COM_COMMUNITY_TEMPLATES_PARAMETERS_SAVED') );
	} 
	
	public function apply()
	{
        $this->save();   
    }
	
	public function edit()
	{
		$id			= JRequest::getVar( 'cid' );
		$mainframe	=& JFactory::getApplication();
		
		$mainframe->redirect( 'index.php?option=com_community&view=templates&layout=edit&id=' . $id[0] );
	}
}