<?php header("Content-type: text/css"); 
//this sets up the colors for the core missioncontrol template
require('../../css/color-vars.php');
?>

#toolbar .button {box-shadow: none; background-image: none; background-color:transparent; border-radius:0; border:none; border-radius:4px; -webkit-border-radius:4px; text-shadow:none;line-height:inherit;}
#toolbar .button a, #toolbar .button a:hover {padding:4px 10px;color:<?php echo $tab_text_color; ?>;}
body div#toolbar li {padding:0;margin:0 0 0 2px;}
body div#toolbar span {margin: 0;opacity: 1;}
body #mc-status .mc-dropdown, body #toolbar .mc-dropdown {margin: 25px 0 0;}

body #toolbar ul.mc-dropdown .button {border-radius:0;}
body #toolbar .button:active {padding:0;}
body #toolbar ul.mc-dropdown .button:active {padding:3px 0;}
body #toolbar .mc-dropdown, body #toolbar .mc-dropdown li:last-child {border-bottom-left-radius: 4px;border-bottom-right-radius: 4px;}
body #toolbar #toolbar-upload, body #toolbar #toolbar-upload a {background:<?php echo $active_bg_color;?> !important;color:<?php echo $active_text_color;?> !important;}
body #header {border-radius:6px;-moz-border-radius:6px;-webkit-border-radius:6px;border: 1px solid #eaeaea;}
body #header .query, body #header .total-count {margin-top:10px;}

body #file-settings h1, body .gallery-block h1 {font-size: 24px;}
body .gallery-block .indicator-published {background-position: 0 84%;}

body #file-edit .edit-block {margin-top: 25px;}
body #file-edit .image-status {margin-top:15px;}
body #file-edit .edit-block h1 {font-family: Helvetica;font-size:17px;letter-spacing: 0;}