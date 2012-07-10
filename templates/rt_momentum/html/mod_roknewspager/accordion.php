<?php 
/**
 * RokNewsPager Module
 *
 * @package     RocketTheme
 * @subpackage  roknewspager.tmpl
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="roknewspager-wrapper">
	<div class="roknewspager">
	<?php foreach ($list as $item) :  ?>
		<?php
		$title = $item->title;
		$tmp = explode(" ", $title);
		$tmp[0] = "<span>".$tmp[0]."</span>";
		
		$title = join(" ", $tmp);
		
		if (isset($item->thumb_size)) $size = " width='{$item->thumb_size['width']}' height='{$item->thumb_size['height']}'";
		else $size = "";
		
		?>
		<div class="roknewspager-li"><div class="roknewspager-li2">
			<h3 class="roknewspager-h3">
				<a href="<?php echo $item->link; ?>" class="roknewspager-title"><?php echo $title ?></a>
				<span class="roknewspager-toggle"></span>
			</h3>
	        <div class="roknewspager-div"><div class="roknewspager-content">
	            <?php if($show_thumbnails && $item->thumb): ?>
	                <?php if($thumbnail_link):?><a href="<?php echo $item->link; ?>"> <?php endif;?>
                    <img src="<?php echo $item->thumb;?>" alt="<?php echo $item->title; ?>"<?php echo $size; ?> />
                    <?php if($thumbnail_link):?></a> <?php endif;?>
	            <?php endif;?>
	            <?php if($show_title && $item->title):?><a href="<?php echo $item->link; ?>" class="roknewspager-title"><?php echo $item->title; ?></a><?php endif;?>
	            <?php if($show_preview_text && $item->introtext):?><div class="introtext"><?php echo $item->introtext; ?></div><?php endif;?>
	            <?php if($show_comment_count):?><div class="commentcount"><?php echo $item->comment_count; ?></div><?php endif;?>
				<?php if($show_author && $item->author):?><div class="author"><?php echo $item->author; ?></div><?php endif;?>
				<?php if($show_published_date && $item->published_date):?><div class="published-date"><?php echo JHTML::_('date', $item->published_date, JText::_('DATE_FORMAT_LC3')); ?></div><?php endif;?>
	            <?php if($show_ratings && $item->rating):?>
					<div class="article-rating">
						<div class="rating-bar">
							<div style="width:<?php echo $item->rating; ?>%"></div>
						</div>
					</div>
				<?php endif;?>
	            <?php if($show_readmore):?><a href="<?php echo $item->link; ?>" class="readon"><span><?php echo $readmore_text;?></span></a><?php endif;?>
	        </div></div>
		</div></div>
	<?php endforeach; ?>
	</div>
</div>
<?php
	$disabled = ($pages == 1) ? " style='display: none;'" : '';
?>
<?php if($show_paging):?>
<div class="roknewspager-pages" <?php echo $disabled; ?>>
	<div class="roknewspager-spinner"></div>
    <div class="roknewspager-pages2">
        <div class="roknewspager-prev"></div>
        <div class="roknewspager-next"></div>
        <ul class="roknewspager-numbers">
            <?php for($x=1;$x<=$pages && $x < ($params->get('maxpages',8)+1);$x++):?>
            <li <?php if($x==$curpage):?>class="active"<?php endif; ?>><?php echo $x; ?></li>
            <?php endfor;?>
        </ul>
    </div>
</div>
<?php endif;?>
<?php
	$autoupdate = ($params->get('autoupdate', false)) ? 1 : 0;
	$autoupdate_delay = $params->get('autoupdate_delay', 5000);
	$moduleType = ($params->get('module_ident','name')=='name') ? "module=" . $module_name : "moduleid=" . $module_id;

	$url = JRoute::_( 'index.php?option=com_rokmodule&tmpl=component&type=raw&'.$moduleType.'&offset=_OFFSET_', true );
?>
<script type="text/javascript">
	RokNewsPagerStorage.push({
		'url': '<?php echo $url; ?>',
		'autoupdate': <?php echo $autoupdate; ?>, 
		'delay': <?php echo $autoupdate_delay; ?>,
		'accordion': true
	});
</script>