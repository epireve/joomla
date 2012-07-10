<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Disallow direct access to this file
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$css  		= JURI::root() . 'components/com_xipt/assets/style.css';
$document   = JFactory::getDocument();
$document->addStyleSheet($css);
?>
<form action="<?php echo XiptRoute::_( 'index.php?option=com_xipt&view=registration&reset=true',false ); ?>" method="post" name="ptypeForm">
 
	<div class="registerProfileType">
	<h3 id="Title"><?php
	echo XiptText::_ ( 'CHOOSE_PROFILE_TYPE' );
	?></h3>
<?php
if(XiptFactory::getSettings('jspt_show_radio'))
	echo $this->loadTemplate('click');
else
	echo $this->loadTemplate('select');
?>
</div>
<div class="clr" title="Next"></div>
<input type="hidden" name="view" value="registration" /> 
<input type="hidden" name="task" value="" /> 
<input type="hidden" name="option" value="com_xipt" /> 
<input type="hidden" name="boxchecked" value="0" />
<?php  echo JHTML::_ ( 'form.token' ); ?>
</form>
<?php 