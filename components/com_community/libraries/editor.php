<?php

/**
 * @package	JomSocial
 * @subpackage	Library
 * @copyright	(C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.html.editor');

class CEditor extends JEditor
{
    var	$type	    =   null;
    var $script	    =	'';

    public function __construct( $editor = 'none' )
    {
	    $this->type = $editor;

	    if( !$this->isCommunityEditor() )
	    {
		    parent::__construct( $this->type );
	    }
	    else
	    {
		    $this->setEditor();
	    }
    }

    private function setEditor()
    {
	    $config =	CFactory::getConfig();

	    $editors		=   $config->get('editors');
	    $editorsInArray	=   explode( ',', $editors );
	    
	    $communityEditor	=   in_array( $this->type, $editorsInArray );
	    if( $communityEditor )
	    {
		    if( $this->type == 'jomsocial' )
		    {
			    $js	=   'assets/editors/ckeditor/ckeditor.js';
			    CAssets::attach($js, 'js');
		    }
	    }
    }

    private function loadEditor( $name, $text=null )
    {
	    $config =	CFactory::getConfig();

	    $editors		=   $config->get('editors');
	    $editorsInArray	=   explode( ',', $editors );
	    
	    $communityEditor	=   in_array( $this->type, $editorsInArray );
	    if( $communityEditor )
	    {
		    if( $this->type == 'jomsocial' )
		    {

			    $this->script   =   '<textarea id="' . $name . '" name="' . $name . '">' . $text . '</textarea>
							    <script type="text/javascript">
								    CKEDITOR.replace( "' . $name . '",
								    {
									    customConfig : "config.js"
								    });
							    </script>';
		    }
	    }
    }

    public function displayEditor( $name, $html, $width, $height, $col, $row, $buttons = true, $params = array())
    {
	    if( !$this->isCommunityEditor() )
	    {
		    $return =	$this->display( $name, $html, $width, $height, $col, $row, $buttons, $params );
		    return $return;
	    }

	    $this->loadEditor( $name, $html );

	    return $this->script;
    }

    public function saveText( $text )
    {
	    if( !$this->isCommunityEditor() )
	    {
		    $return	=   $this->save( $text );
		    return $return;
	    }

	    return;
    }

    private function isCommunityEditor()
    {
	    $config =	CFactory::getConfig();
	    $db	    =&	JFactory::getDBO();

	    // compile list of the joomlas' editors
	    $query = 'SELECT ' . $db->nameQuote('element')
			    . ' FROM ' . $db->nameQuote(PLUGIN_TABLE_NAME)
			    . ' WHERE ' . $db->nameQuote('folder') .' = ' . $db->Quote('editors')
			    . ' AND ' . $db->nameQuote(EXTENSION_ENABLE_COL_NAME) .' = ' . $db->Quote(1)
			    . ' ORDER BY ' . $db->nameQuote('ordering') .', ' . $db->nameQuote('name');
	    $db->setQuery( $query );
	    $editors = $db->loadResultArray();

	    $jEditor	=   in_array( $this->type, $editors );

	    // Return false if it is a Joomla's editor
	    return !$jEditor;
    }
}

?>
