<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');
?>
<script language="javascript" type="text/javascript">

	function checkForm()
	{
		var myvalue= document.getElementById('acl');
		
		if( myvalue.value == 0)
		{
			alert( "<?php echo XiptText::_( 'PLEASE_SELECT_A_ACL_FROM_LIST'); ?>" );
			return false;
		}
		return true;
	}

</script>

<script type="text/javascript" src="<?php echo JURI::root().'components/com_xipt/assets/js/jquery1.4.2.js';?>" ></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	
	$("select#acl").change(function(){
			var optionvalue = $("select option:selected").val();
						
			$('div#xiptOptionHelper').css("display", "block");			
			$('div#xiptOptionHelper').children('div').css("display", "none");
			$('div#'+optionvalue).css("display", "block");
	});
});
	
</script>

<div style="background-color: #F9F9F9; border: 1px solid #D5D5D5; margin-bottom: 10px; padding: 5px;font-weight: bold;">
	<?php echo XiptText::_('SELECT_ACL_TO_USE');?>
</div>

<div id="error-notice" style="color: red; font-weight:700;"></div>
<div style="clear: both;"></div>

<form action="<?php echo JURI::base();?>index.php?option=com_xipt" method="post" name="adminForm" id="adminForm">
<div style="margin-left:5%; width:40% float:left;">
	<div>
	<select id="acl" name="acl" size="15">				
	<?php
		if(!empty($this->groups))
		foreach($this->groups as $acl) : ?>
			<option disabled="disabled"></option>
		    <option value="<?php echo $acl['name'];?>" disabled="disabled"><?php echo $acl['title'];?></option>
		    
			<?php foreach($this->rules[$acl['name']] as $rule) : ?>
		    		<option value="<?php echo $rule['name'];?>" ><?php echo $rule['title'];?></option>
			<?php endforeach; ?> 
	<?php endforeach; ?>
	</select>
	</div>
	<div style="margin-top:10px; margin-left:160px;";>				
	<input type="submit" name="aclnext" value="<?php echo XiptText::_('NEXT');?>" onClick="return checkForm();"/>
	</div>				
</div>

<div id="xiptOptionHelper" style= "background-color:#F9F9F9; border:1px solid #efefef; width:40%;  
									padding:5px; display:none; float:right; margin-top:-225px; margin-right:300px;">
	<?php foreach($this->groups as $acl) :
			foreach($this->rules[$acl['name']] as $rule) : ?>
				<div  id= <?php echo $rule['name']; ?>  style= "display:<?php echo "none";?>">
				<h3 > <?php echo $rule['title']; ?> </h3>
				<?php echo $rule['description']; ?>
				</div> 
			<?php endforeach; ?>
	<?php endforeach; ?>				
</div>

<div class='clr'></div>



	<input type="hidden" name="option" value="com_xipt" />
	<input type="hidden" name="view" value="<?php echo JRequest::getCmd( 'view' , 'aclrules' );?>" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="edit" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
