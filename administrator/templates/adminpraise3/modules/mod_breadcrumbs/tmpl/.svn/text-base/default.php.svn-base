<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div id="ap-crumbs">
	<ul class="breadcrumbs">
		<li class="first-crumb"><?php echo JText::_( 'HISTORY' );?></li>
		<?php
		for($i = 0; $i < count($crumbList) - 1; $i++)
		{
			$crumb=explode('!', $crumbList[$i]);
			echo "<li><a href='$crumb[0]'>$crumb[1]</a><span class='next-arrow'></span></li>";
		}
		echo "<li class='last-crumb'>".$lastCrumb[1]."</li>";
		?>
	</ul>
</div>