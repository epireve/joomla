<?php
/**
 * @package   Template Overrides - RocketTheme
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Gantry Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.DS.'helpers');


?>
<div class="rt-blog <?php echo $this->pageclass_sfx;?>">
	
	<?php /** Begin Page Title **/ if ( $this->params->get('show_page_heading')!=0) : ?>
	<h1 class="rt-pagetitle">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php /** End Page Title **/ endif; ?>
	<div class="blog-wrapper">
	<?php $leadingcount=0 ; ?>
	<?php /** Begin Leading Articles **/ if (!empty($this->lead_items)) : ?>
	<div class="rt-leading-articles">
		<?php foreach ($this->lead_items as &$item) : ?>
			<div class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
				<?php
					$this->item = &$item;
					echo $this->loadTemplate('item');
				?>
			</div>
			<?php
				$leadingcount++;
			?>
		<?php endforeach; ?>
	</div>
	<?php /** End Leading Articles **/ endif; ?>

	<?php
		$introcount=(count($this->intro_items));
		$counter=0;
	?>
	<?php /** Begin Articles **/ if (!empty($this->intro_items)) : ?>
	<div class="rt-teaser-articles">
		<?php foreach ($this->intro_items as $key => &$item) : ?>
		<?php
			$key= ($key-$leadingcount)+1;
			$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
			$row = $counter / $this->columns ;

			if ($rowcount==1) : ?>
		<div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
		<?php endif; ?>
		<div class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished"' : null; ?>">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</div>
		<?php $counter++; ?>
		<?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
			<span class="row-separator"></span>
			</div>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php /** End Articles **/ endif; ?>
	</div>
	<div class="clear"></div>
	<?php /** Begin Article Links **/ if (!empty($this->link_items)) : ?>
	<div class="rt-article-links">
		<?php echo $this->loadTemplate('links'); ?>
	</div>
	<?php /** End Article Links **/ endif; ?>

	<?php /** Begin Pagination **/ if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
	<div class="rt-pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="rt-results">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php  endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php /** End Pagination **/ endif; ?>

</div>