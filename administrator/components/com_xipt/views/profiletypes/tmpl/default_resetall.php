<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
if(!defined('_JEXEC')) die('Restricted access');
?>



<div>
<h3 style="width:100%; background:#7ac047;text-align:center;color:RED;padding:5px;font-weight:bold;">
<?php 
//show user reseted
//can show 'reseting next 500 user'
echo "Reset Page : DO NOT CLOSE THIS WINDOW WHILE RESETTING USERS";
?>
</h3>
<?php  
echo "<br />Total Users ".$this->total;
echo "<br />Profile-type id ".$this->id;
echo "<br />Syncing-Up ". ($this->start-1)*$this->limit ." To ". $this->start*$this->limit ." ";
$remain = $this->total - ($this->start*$this->limit); 
if($remain <= 0)
	echo "<br />Remaining 0 Users";
else
	echo "<br />Remaining " .$remain . " Users";

?>
<script>
window.onload = function() {
	  setTimeout("xiredirect()", 3000);
}

function xiredirect(){
	window.location = "<?php echo XiPTRoute::_("index.php?option=com_xipt&view=profiletypes&task=resetall&start=$this->start&id=$this->id");?>"
		
}

</script>
</div>