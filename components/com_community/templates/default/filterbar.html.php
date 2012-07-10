<?php
/**
 * @category Template
 * @package	JomSocial
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();
?>
<div id="cFilterBar" class="cFilterBar">
<div class="cFilterBar_inner">
<?php
	if( $sortItems )
	{
?>
	<div id="cFilterType_Sort" class="filterGroup">
        <span class="filterName"><?php echo JText::_('COM_COMMUNITY_SORT_BY'); ?>:</span>
        <ul class="filterOptions">
        <?php
        foreach( $sortItems as $key => $option )
        {
            $queries['sort'] = $key;
            $link = 'index.php?'. $uri->buildQuery($queries);
            $link = CRoute::_($link);
        ?>
        <?php if($key==JString::trim($selectedSort)):?>
            <li class="filterOption active"><?php echo $option; ?></li>
		<?php else: ?>
        	<li class="filterOption"><a href="<?php echo $link; ?>"><?php echo $option; ?></a></li>
		<?php endif ?>
        <?php
        }
        ?>
        </ul>
    </div>
<?php
        $queries['sort'] = $selectedSort;
    }
?>

<?php
    if( $filterItems )
    {
        
?>
	<div id="cFilterType_Filter" class="filterGroup">
        <span class="filterName"><?php echo JText::_('COM_COMMUNITY_FILTER_SHOW'); ?></span>
        <ul class="filterOptions">
        <?php
        foreach( $filterItems as $key => $option )
        {
            $queries['filter'] 		= $key;
            
            // We need to reset the pagination limitstart so the pagination will not affect the filter
            unset($queries['limitstart']);
            $link = 'index.php?'. $uri->buildQuery($queries);

            $link = CRoute::_($link);
        ?>
        <?php if($key==JString::trim($selectedFilter)):?>
            <li class="filterOption active"><?php echo $option; ?></li>
		<?php else: ?>
        	<li class="filterOption"><a href="<?php echo $link; ?>"><?php echo $option; ?></a></li>
		<?php endif ?>
        <?php
        }
        ?>
        </ul>
	</div>
<?php
    }
?>
</div>
</div>