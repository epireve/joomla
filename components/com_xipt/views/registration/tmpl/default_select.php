<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>
	<select id="profiletypes" name="profiletypes" class="select required" >
                             
<?php
		
foreach ( $this->allProfileTypes as $pType ):

	$selected = '';
	// check if selected
	if ($this->selectedPT == $pType->id)
		$selected = 'checked="true"';

	// show as selectbox	
    $option		= $pType->name;
	$id			= $pType->id;
    
    $selected	= ( JString::trim($id) == $this->selectedPT ) ? ' selected="true"' : '';
	echo '<option value="' . $id . '"' 
				. $selected . ' '
				.  '>' 
				. $option . '</option>';
endforeach;
	
?>
</select>
<input type="submit" id="ptypesavebtn" name="save" value="<?php echo XiptText::_('NEXT');?>"/>
<?php
