<?php
/**
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

function cShowCarousel($id, $total, $jaxCall){
	static $carouselCustomTag = null;
	
	if(!$carouselCustomTag)
	{
		CAssets::attach('templates/default/carousel.css', 'css');
		CAssets::attach('assets/carousel-1.0.js', 'js');
		
		$carouselCustomTag = true;
	}
	
	ob_start();
?>
<div class="carousel-container" id="<?php echo $id;?>">
	<a class="carousel-prev" href="javascript:void(0)" onclick="this.blur();cCarouselPrev('<?php echo $id;?>', '<?php echo $jaxCall;?>');joms.jQuery(this).trigger('onblur');">« Prev</a>
	<a class="carousel-next" href="javascript:void(0)" onclick="this.blur();cCarouselNext('<?php echo $id;?>', '<?php echo $jaxCall;?>');joms.jQuery(this).trigger('onblur');">Next »</a>
	<div class="carousel-content">
		<div class="carousel-content-wrap" style="display: block;">
			<div class="carousel-content-clip">
				<ul class="carousel-list" style="width: 1600px; left: 0pt;margin:0px">
					<?php for($i=0; $i<$total; $i++) { ?>
					<li class="carousel-item" id="<?php echo $id;?>-item-<?php echo $i; ?>"><div class="ajax-wait">&nbsp;</div></li>
					<?php } ?>					
				</ul>
			</div>
		</div>
	</div>
</div>
<script type='text/javascript'>
cCarouselInit('<?php echo $id; ?>', '<?php echo $jaxCall; ?>');
</script>
	<?php
	$content	= ob_get_contents();
	ob_end_clean();
	return $content;
}

?>
