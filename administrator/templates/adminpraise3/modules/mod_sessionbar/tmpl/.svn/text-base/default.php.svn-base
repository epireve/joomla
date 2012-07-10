<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$db     = &JFactory::getDBO();
$user   = &JFactory::getUser();
$config = &JFactory::getConfig();

$lifetime = (int) $config->getValue('lifetime');
$lifetime = $lifetime * 60;

$query = "SELECT time FROM #__session WHERE userid = '".$user->get('id')."' and client_id = '1' LIMIT 1";
       $db->setQuery($query);
       $session_time = (int) $db->loadResult();

$css = "
.sess_bar_outer
{
   width:100px;
   height:12px;
   padding:1px;
   border:1px solid #ccc;
}
.sess_bar
{
   height:12px;
}
";
$html = "
<div class='sess_bar_outer'>
   <div class='sess_bar' style='width:100%;background-color:#ccc' id='sess_bar' title='Your session expires in x minutes'></div>
</div>
";

$js = "
function set_session_bar()
{
   var d = new Date();
   var t = d.getTime();
   var t = Math.round(t / 1000);
   var sess_time = $session_time;
   var lifetime  = $lifetime;
   var remaining = (sess_time + lifetime) - t;
   var new_w = Math.round((remaining/lifetime)*100);
   var min_remaining = Math.round(remaining/60);

   if(new_w < 0) {
      new_w = 0;
   }

   if(new_w > 50) {
      document.getElementById('sess_bar').style.backgroundColor = '#006600';
   }
   if(new_w < 50 && new_w > 20) {
      document.getElementById('sess_bar').style.backgroundColor = '#CC6600';
   }
   if(new_w < 20 && new_w > 0) {
      document.getElementById('sess_bar').style.backgroundColor = '#660000';
   }
   document.getElementById('sess_bar').title = 'Your session expires in '+min_remaining+' minutes';
   document.getElementById('sess_bar').style.width = new_w+'%';
}
setInterval('set_session_bar()', 1000);
";

echo "<style>".$css."</style>";
echo $html;
echo "<script type='text/javascript'>".$js."</script>";
?>