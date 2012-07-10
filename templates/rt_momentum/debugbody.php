<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();


//        'mb12' => array(''),
//
//        'mb6-sa6' => array ('',''),
//        'mb8-sa4' => array ('',''),
//        'mb9-sa3' => array ('',''),
//
//        'sa6-mb6' => array ('rt-pull-6','rt-push-6'),
//        'sa4-mb8' => array ('rt-pull-4','rt-push-4'),
//        'sa3-mb9' => array ('rt-pull-3','rt-push-3'),
//
//        'mb4-sa4-sb4' => array('','',''),
//        'mb6-sa3-sb3' => array('','',''),
//        'mb8-sa2-sb2' => array('','',''),
//
//        'sa4-mb4-sb4' => array('rt-push-4','rt-pull-4',''),
//        'sa3-mb6-sb3' => array('rt-push-3','rt-pull-6',''),
//        'sa2-mb8-sb2' => array('rt-push-2','rt-pull-8',''),
//
//        'sa4-sb4-mb4' => array('rt-push-8','rt-pull-4','rt-pull-4'),
//        'sa3-sb3-mb6' => array('rt-push-6','rt-pull-6','rt-pull-6'),
//        'sa2-sb2-mb8' => array('rt-push-4','rt-pull-8','rt-pull-8'),
//
//        'mb3-sa3-sb3-sc3' => array('','',''),
//        'mb4-sa2-sb3-sc3' => array('','',''),
//        'mb4-sa3-sb2-sc3' => array('','',''),
//        'mb4-sa3-sb3-sc2' => array('','',''),
//        'mb6-sa2-sb2-sc2' => array('','',''),
//
//        'sa3-mb3-sb3-sc3' => array('rt-push-3','rt-push-3','',''),
//        'sa3-mb4-sb2-sc3' => array('rt-push-3','rt-pull-4','',''),
//        'sa2-mb4-sb3-sc3' => array('rt-push-2','rt-pull-4','',''),
//        'sa3-mb4-sb3-sc2' => array('rt-push-3','rt-pull-4','',''),
//        'sa2-mb6-sb2-sc2' => array('rt-push-2','rt-pull-6','',''),
//
//        'sa3-sb3-mb3-sc3' => array('rt-push-6','rt-pull-3','rt-pull-3',''),
//        'sa3-sb2-mb4-sc3' => array('rt-push-5','rt-pull-4','rt-pull-4',''),
//        'sa2-sb4-mb4-sc3' => array('rt-push-6','rt-pull-4','rt-pull-4',''),
//        'sa3-sb4-mb4-sc2' => array('rt-push-7','rt-pull-4','rt-pull-4',''),
//        'sa2-sb2-mb6-sc2' => array('rt-push-4','rt-pull-6','rt-pull-6',''),
//
//        'sa3-sb3-sc3-mb3' => array('rt-push-9','rt-pull-3','rt-pull-3','rt-pull-3'),
//        'sa3-sb3-sc2-mb4' => array('rt-push-8','rt-pull-4','rt-pull-4','rt-pull-4'),
//        'sa3-sb2-sc3-mb4' => array('rt-push-8','rt-pull-4','rt-pull-4','rt-pull-4'),
//        'sa2-sb3-sc3-mb4' => array('rt-push-8','rt-pull-4','rt-pull-4','rt-pull-4'),
//        'sa2-sb2-sc2-mb6' => array('rt-push-6','rt-pull-6','rt-pull-6','rt-pull-6')

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<jdoc:include type="head" />
	<?php $gantry->addStyles(array('template.css','joomla.css')); ?>
</head>
	<body id="debug">
		<div class="rt-container">

		    <?php echo $gantry->debugMainbody('debugmainbody','sidebar','standard'); ?>

		</div>
	</body>
</html>
<?php
$gantry->finalize();
?>