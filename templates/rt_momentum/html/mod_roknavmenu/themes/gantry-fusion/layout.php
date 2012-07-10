<?php
/**
 * @version   1.3 October 12, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

class GantryFusionLayout extends AbstractRokMenuLayout
{
    protected $theme_path;
    protected $params;

    private $isJsEnabled;
    private $isPillEnabled;
    private $activeid;

    public function __construct(&$args)
    {
        parent::__construct($args);
        global $gantry;
        $theme_rel_path = "/html/mod_roknavmenu/themes/gantry-fusion";
        $this->theme_path = $gantry->templatePath . $theme_rel_path;
        $this->args['theme_path'] = $this->theme_path;
        $this->args['theme_rel_path'] = $gantry->templateUrl. $theme_rel_path;
        $this->args['theme_url'] = $this->args['theme_rel_path'];
    }

    public function stageHeader()
    {
        global $gantry;

        JHtml::_('behavior.mootools');
        
        $enablejs = $this->args['enable_js'];
        $opacity = $this->args['opacity'];
        $effect = $this->args['effect'];
        $hidedelay = $this->args['hidedelay'];
        $menu_animation = $this->args['menu-animation'];
        $menu_duration = $this->args['menu-duration'];
        $pill = $this->args['pill-enabled'];
        $pill_animation = $this->args['pill-animation'];
        $pill_duration = $this->args['pill-duration'];
        $tweakInitial_x = $this->args['tweak-initial-x'];
        $tweakInitial_y = $this->args['tweak-initial-y'];
        $tweakSubsequent_x = $this->args['tweak-subsequent-x'];
        $tweakSubsequent_y = $this->args['tweak-subsequent-y'];
        $widthCompensation = $this->args['tweak-width'];
        $heightCompensation = $this->args['tweak-height'];
        $centeredOffset = $this->args['centered-offset'];


        if ($centeredOffset == "1") {
            $tweakInitial_y = 0;
            $tweakInitial_x = 20;
        }

        $this->activeid = $this->args['enable_current_id'] == 0 ? false : true;
        if ($enablejs != '1' && $enablejs != 1) $this->isJSEnabled = 'nojs';
        if ($pill != '1' && $pill != 1) $this->isPillEnabled = false;
        else $this->isPillEnabled = true;

        if ($effect == 'slidefade') $effect = "slide and fade";

        if ($gantry->browser->name == 'ie' && $effect == 'slide and fade') $effect = "slide";
		if ($gantry->browser->name == 'ie' && $gantry->browser->shortversion == '8') $tweakInitial_x -= 1;

        if ($enablejs) {
            $gantry->addScript($gantry->baseUrl . 'modules/mod_roknavmenu/themes/fusion/js/fusion.js');
            ob_start();
            ?>
            window.addEvent('domready', function() {
                new Fusion('ul.menutop', {
                    pill: <?php echo $pill; ?>,
                    effect: '<?php echo $effect; ?>',
                    opacity:  <?php echo $opacity; ?>,
                    hideDelay:  <?php echo $hidedelay; ?>,
                    centered:  <?php echo $centeredOffset; ?>,
                    tweakInitial: {'x': <?php echo $tweakInitial_x; ?>, 'y': <?php echo $tweakInitial_y; ?>},
                    tweakSubsequent: {'x':  <?php echo $tweakSubsequent_x; ?>, 'y':  <?php echo $tweakSubsequent_y; ?>},
                    tweakSizes: {'width': <?php echo $widthCompensation; ?>, 'height': <?php echo $heightCompensation; ?>},
                    menuFx: {duration:  <?php echo $menu_duration; ?>, transition: Fx.Transitions.<?php echo $menu_animation; ?>},
                    pillFx: {duration:  <?php echo $pill_duration; ?>, transition: Fx.Transitions.<?php echo $pill_animation; ?>}
                });
            });
            <?php
            $inline = ob_get_clean();
            $this->appendInlineScript($inline);    
        }
		$gantry->addStyle('fusionmenu.css');
    }

    protected function renderItem(JoomlaRokMenuNode &$item, RokMenuNodeTree &$menu)
    {

        global $gantry;

        $wrapper_css = '';
        $ul_css = '';
        $group_css = '';

        $item_params = new JParameter($item->getParams());

	    //get columns count for children
	    $columns = $item_params->get('fusion_columns',1);
	    //get custom image
	    $custom_image = $item_params->get('fusion_customimage');
        $custom_class = $item_params->get('fusion_customclass');

	    if ($custom_image && $custom_image != -1) $item->addLinkClass('image');
	    else $item->addLinkClass('bullet');
        if ($custom_class != '') $item->addListItemClass($custom_class);

        $dropdown_width = $item_params->get('fusion_dropdown_width');
        $column_widths = explode(",",$item_params->get('fusion_column_widths'));


        if (trim($columns)=='') $columns = 1;
        if (trim($dropdown_width)=='') $dropdown_width = 180;

        $wrapper_css = ' style="width:'.trim($dropdown_width).'px;"';

        $col_total = 0;$cols_left=$columns;
        if (trim($column_widths[0] != '')) {
            for ($i=0; $i < $columns; $i++) {
                if (isset($column_widths[$i])) {
                    $ul_css[] = ' style="width:'.trim($column_widths[$i]).'px;"';
                    $col_total += $column_widths[$i];
                    $cols_left--;
                } else {
                    $col_width = floor(intval((intval($dropdown_width) - $col_total) / $cols_left));
                    $ul_css[] = ' style="width:'.$col_width.'px;"';
                }
            }
        } else {
            for ($i=0; $i < $columns; $i++) {
                $col_width = floor(intval($dropdown_width)/$columns);
                $ul_css[] = ' style="width:'.$col_width.'px;"';
            }
        }

	    $grouping = $item_params->get('fusion_children_group');
        if ($grouping == 1) $item->addListItemClass('grouped-parent');

	    $child_type = $item_params->get('fusion_children_type');
        $child_type = $child_type == '' ? 'menuitems' : $child_type;
        $distribution = $item_params->get('fusion_distribution');
        $manual_distribution = explode(",",$item_params->get('fusion_manual_distribution'));

        $modules = array();
        if ($child_type == 'modules') {
            $modules_id = $item_params->get('fusion_modules');

            $ids = is_array($modules_id) ? $modules_id : array($modules_id);
            foreach ($ids as $id) {
                if ($module = $this->getModule ($id)) $modules[] = $module;
            }
            $group_css = ' type-module';

        } elseif ($child_type == 'modulepos') {
            $modules_pos = $item_params->get('fusion_module_positions');

            $positions = is_array($modules_pos) ? $modules_pos : array($modules_pos);
            foreach ($positions as $pos) {
                $mod = $this->getModules ($pos);
                $modules = array_merge ($modules, $mod);
            }
            $group_css = ' type-module';
        }

	    //not so elegant solution to add subtext
	    $item_subtext = $item_params->get('fusion_item_subtext','');
	    if ($item_subtext=='') $item_subtext = false;
	    else $item->addLinkClass('subtext');

       //sort out module children:
       if ($child_type!="menuitems") {
            $document	= &JFactory::getDocument();
            $renderer	= $document->loadRenderer('module');
            $params		= array('style'=>'fusion');

            $mod_contents = array();
            foreach ($modules as $mod)  {

                $mod_contents[] = $renderer->render($mod, $params);
            }
            $item->setChildren($mod_contents);

            $link_classes = explode(' ', $item->getLinkClasses());
            //replace orphan with daddy if needed
            if ($item->hasChildren() && in_array('orphan', $link_classes) ) {
                $link_classes[array_search ('orphan',$link_classes)] = 'daddy';
    		}
            $item->setLinkClasses($link_classes);
       }
        ?>
        <li <?php if($item->hasListItemClasses()) : ?>class="<?php echo $item->getListItemClasses()?>"<?php endif;?> <?php if($item->hasCssId() && $this->activeid):?>id="<?php echo $item->getCssId();?>"<?php endif;?>>
            <?php if ($item->getType() == 'menuitem') : ?>
            	<?php
					$urlCheck = JRoute::_($this->curPageURL($item->getLink()), true);
					if ($urlCheck) $activeToTop = ' active-to-top';
					else $activeToTop = '';
				?>
                <a <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses().$activeToTop;?>"<?php endif;?> <?php if($item->hasLink()):?>href="<?php echo $item->getLink();?>"<?php endif;?> <?php if($item->hasTarget()):?>target="<?php echo $item->getTarget();?>"<?php endif;?> <?php if ($item->hasAttribute('onclick')): ?>onclick="<?php echo $item->getAttribute('onclick'); ?>"<?php endif; ?><?php if ($item->hasLinkAttribs()): ?> <?php echo $item->getLinkAttribs(); ?><?php endif; ?>>
                    <span>
                    <?php if ($custom_image && $custom_image != -1) :?>
                        <img src="<?php echo $gantry->templateUrl."/images/icons/".$custom_image; ?>" alt="<?php echo $custom_image; ?>" />
                    <?php endif; ?>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    <?php if ($item->getParent() == RokNavMenu::TOP_LEVEL_PARENT_ID && $item->hasChildren()): ?>
                    <span class="daddyicon"></span>
                    <?php endif; ?>
                    </span>
                </a>
            <?php elseif($item->getType() == 'separator') : ?>
                <span <?php if($item->hasLinkClasses()):?>class="<?php echo $item->getLinkClasses();?> nolink"<?php endif;?>>
                    <span>
                        <?php if ($custom_image && $custom_image != -1) :?>
                            <img src="<?php echo $gantry->templateUrl."/images/icons/".$custom_image; ?>" alt="<?php echo $custom_image; ?>" />
                        <?php endif; ?>
                    <?php echo $item->getTitle();?>
                    <?php if (!empty($item_subtext)) :?>
                    <em><?php echo $item_subtext; ?></em>
                    <?php endif; ?>
                    <?php if ($item->getParent() == RokNavMenu::TOP_LEVEL_PARENT_ID && $item->hasChildren()): ?>
                    <span class="daddyicon"></span>
                    <?php endif; ?>
                    </span>
                </span>
            <?php endif; ?>

            <?php if ($item->hasChildren()): ?>
                <?php if ($grouping == 0 or $item->getLevel() == 0) :
                    if ($distribution=='inorder') {
                        $count = sizeof($item->getChildren());
                        $items_per_col = intval(ceil($count / $columns));
                        $children_cols = array_chunk($item->getChildren(),$items_per_col);
                    } elseif ($distribution=='manual') {
                    	$children_cols = $this->array_fill($item->getChildren(), $columns, $manual_distribution);
                    } else {
                        $children_cols = $this->array_chunkd($item->getChildren(),$columns);
                    }
                    $col_counter = 0;
                    ?>
                    <div class="fusion-submenu-wrapper level<?php echo intval($item->getLevel())+2; ?> primary-overlay-<?php global $gantry; echo $gantry->get('primary-overlay'); ?><?php if ($columns > 1) echo ' columns'.$columns; ?>"<?php echo $wrapper_css; ?>>
                        <?php foreach($children_cols as $col) : ?>

                        <ul class="level<?php echo intval($item->getLevel())+2; ?>"<?php echo $ul_css[$col_counter++]; ?>>
                            <?php foreach ($col as $child) : ?>
                                <?php if ($child_type=='menuitems'): ?>
                                    <?php $this->renderItem($child, $menu); ?>
                                <?php else: ?>
                                    <li>
                                        <div class="fusion-modules item">
                                        <?php echo ($child); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                        <?php endforeach;?>
                        <div class="drop-bot"></div>
                    </div>
                <?php else : ?>
                    <div class="fusion-grouped<?php echo $group_css; ?>">
                        <ol>
                            <?php foreach ($item->getChildren() as $child) : ?>
                                <?php if ($child_type=='menuitems'): ?>
                                    <?php $this->renderItem($child, $menu); ?>
                                <?php else: ?>
                                    <li>
                                        <div class="fusion-modules item">
                                        <?php echo ($child); ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </div>

                <?php endif; ?>
            <?php endif; ?>
        </li>
        <?php
    }

    function getModule ($id=0, $name='')
    {

        $modules	=& RokNavMenu::loadModules();
        $total		= count($modules);
        for ($i = 0; $i < $total; $i++)
        {
            // Match the name of the module
            if ($modules[$i]->id == $id || $modules[$i]->name == $name)
            {
                return $modules[$i];
            }
        }
        return null;
    }

    function getModules ($position)
    {
        $modules = JModuleHelper::getModules ($position);
        return $modules;
    }
    
    function array_fill(array $array, $columns, $manual_distro) {
    
    	$new_array = array();
    	
    	//$array = array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth");
    
//    	var_dump ($array);
//    	var_dump ($columns);
//    	var_dump ($manual_distro);
    	
    	array_unshift($array, null);
    	
    	
    	for ($i=0;$i<$columns;$i++) {
    		if (isset($manual_distro[$i])) {
    			$manual_count = $manual_distro[$i];
    			for ($c=0;$c<$manual_count;$c++) {
    				//echo "i:c " . $i . ":". $c;
    				$element = next($array);
    				if ($element) $new_array[$i][$c] = $element;
    			}
    		}
    	
    	
    	
    	}

    	
    	return $new_array;
    
    }

    function array_chunkd(array $array, $chunk)
    {
        if ($chunk === 0)
            return $array;

        // number of elements in an array
        $size = count($array);

        // average chunk size
        $chunk_size = $size / $chunk;

        // calculate how many not-even elements eg in array [3,2,2] that would be element "3"
        $real_chunk_size = floor($chunk_size);
        $diff = $chunk_size - $real_chunk_size;
        $not_even = $diff > 0 ? round($chunk * $diff) : 0;

        // initialise values for return
        $result = array();
        $current_chunk = 0;

        foreach ($array as $key => $element)
        {
            $count = isset($result[$current_chunk]) ? count($result[$current_chunk]) : 0;

            // move to a new chunk?
            if ($count == $real_chunk_size && $current_chunk >= $not_even || $count > $real_chunk_size && $current_chunk < $not_even)
                $current_chunk++;

            // save value
            $result[$current_chunk][$key] = $element;
        }

        return $result;
    }

    public function calculate_sizes (array $array)
    {
        return implode(', ', array_map('count', $array));
    }
    
    public function curPageURL($link) {
		$pageURL = 'http';
	 	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 	$pageURL .= "://";
	 	if ($_SERVER["SERVER_PORT"] != "80") {
	  		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 	} else {
	  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 	}
	
		$replace = str_replace('&', '&amp;', (preg_match("/^http/", $link) ? $pageURL : $_SERVER["REQUEST_URI"]));

		return $replace == $link || $replace == $link . 'index.php';
	}

    public function renderMenu(&$menu) {
        ob_start();
?>
<div class="rt-fusionmenu">
<?php if (!$this->isPillEnabled): ?>
<div class="nopill">
<?php endif; ?>
<div class="rt-menubar">
    <ul class="menutop level1 <?php echo $this->isJsEnabled; ?>" <?php if (array_key_exists('tag_id',$this->args)): ?>id="<?php echo $this->args['tag_id'];?>"<?php endif;?>>
        <?php foreach ($menu->getChildren() as $item) : ?>
        <?php $this->renderItem($item, $menu); ?>
        <?php endforeach; ?>
    </ul>
</div>
<div class="clear"></div>
<?php if (!$this->isPillEnabled): ?>
</div>
<?php endif; ?>
</div>
<?php
        return ob_get_clean();
    }
}