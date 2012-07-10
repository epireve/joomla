<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

require_once(dirname(__FILE__) . '/JoomlaRokMenuNode.php');

if (!class_exists('RokMenuProviderJoomla16'))
{


    class RokMenuProviderJoomla16 extends AbstractRokMenuProvider
    {

        const ROOT_ID = 1;

        protected function getMenuItems()
        {
            //Cache this basd on access level
            $conf =& JFactory::getConfig();
            if ($conf->getValue('config.caching') && $this->args["module_cache"])
            {
                $user =& JFactory::getUser();
                $cache =& JFactory::getCache('mod_roknavmenu');
                $cache->setCaching(true);
                $args = array($this->args);
                $checksum = md5(implode(',', $this->args));
                $menuitems = $cache->get(array($this, 'getFullMenuItems'), $args, 'mod_roknavmenu-' . $user->get('aid', 0) . '-' . $checksum);
            }
            else
            {
                $menuitems = $this->getFullMenuItems($this->args);
            }

            $jmenu = JSite::getMenu();
            $active = $jmenu->getActive();


            if (is_object($active))
            {
                if (array_key_exists($active->id, $menuitems))
                {
                    $this->current_node = $active->id;
                }
            }

            $this->populateActiveBranch($menuitems);

            return $menuitems;
        }

        public function getFullMenuItems($args)
        {
            $menu = JSite::getMenu();
            // Get Menu Items
            $rows = $menu->getItems('menutype', $args['menutype']);

            $outputNodes = array();
            if (is_array($rows) && count($rows) > 0)
            {
                foreach ($rows as $item)
                {
                    //Create the new Node
                    $node = new JoomlaRokMenuNode();

                    $node->setId($item->id);
                    $node->setParent($item->parent_id);
                    $node->setTitle(addslashes(htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8')));
                    $node->setParams($item->params);
                    $node->setLink($item->link);

                    // Menu Link is a special type that is a link to another item
                    if ($item->type == 'alias' && $newItem = $menu->getItem($item->params->get('aliasoptions')))
                    {
                        $node->setAlias(true);
                        $node->setLink($newItem->link);
                    }

                    // Get the icon image associated with the item
                    $iParams = new JRegistry($item->params);
                    if ($args['menu_images'] && $iParams->get('menu_image') && $iParams->get('menu_image') != -1)
                    {
                        $node->setImage(JURI::base(true) . '/images/stories/' . $iParams->get('menu_image'));
                        if ($args['menu_images_link'])
                        {
                            $node->setLink(null);
                        }
                    }

                    switch ($item->type)
                    {
                        case 'separator':
                            $node->setType('separator');
                            break;
                        case 'url':
                            if ((strpos($node->getLink(), 'index.php?') === 0) && (strpos($node->getLink(), 'Itemid=') === false))
                            {
                                $node->setLink($node->getLink() . '&amp;Itemid=' . $node->getId());
                            }
                            $node->setType('menuitem');
                            break;
                        default :
                            $router = JSite::getRouter();
                            if ($node->isAlias() && $newItem)
                            {
                                $menu_id = $item->params->get('aliasoptions');
                            }
                            else
                            {
                                $menu_id = $node->getId();
                            }
                            $link = ($router->getMode() == JROUTER_MODE_SEF) ? 'index.php?Itemid=' . $menu_id : $node->getLink() . '&Itemid=' . $menu_id;
                            $node->setLink($link);
                            $node->setType('menuitem');
                            break;
                    }


                    if ($node->getLink() != null)
                    {
                        // set the target based on menu item options
                        switch ($item->browserNav)
                        {
                            case 1:
                                $node->setTarget('new');
                                break;
                            case 2:
                                $node->setLink(str_replace('index.php', 'index2.php', $node->getLink()));
                                $node->setTarget('newnotool');
                                break;
                            default:
                                //$node->setTarget('current');
                                break;
                        }


                        // Get the final URL
                        if ($item->home == 1)
                        { // Set Home Links to the Base
                            $node->setLink(JURI::base());
                        }

                        if ($item->type != 'separator' && $item->type != 'url')
                        {
                            $iSecure = $iParams->get('secure', 0);
                            if (array_key_exists('url_type', $args) && $args['url_type'] == 'full')
                            {
                                $url = JRoute::_($node->getLink(), true, $iSecure);
                                $base = (!preg_match("/^http/", $node->getLink())) ? rtrim(JURI::base(false) . '/') : '';
                                $routed = $base . $url;
                                $secure = RokNavMenuTree::_getSecureUrl($routed, $iSecure);
                                $node->setLink($secure);
                            } else
                            {
                                $node->setLink(JRoute::_($node->getLink(), true, $iSecure));
                            }
                        }
                        else if ($item->type == 'url')
                        {
                            $node->setLink(str_replace('&', '&amp;', $node->getLink()));
                        }
                    }

                    $node->addListItemClass("item" . $node->getId());
                    $node->setAccess($item->access);
                    $node->addSpanClass($node->getType());

                    $outputNodes[$node->getId()] = $node;
                }
                return $outputNodes;
            }
        }

        /**
         * @param  $nodeList
         * @return void
         */
        protected function populateActiveBranch($nodeList)
        {
            // setup children array to find parents and children
            $children = array();
            $list = array();
            foreach ($nodeList as $node)
            {

                $thisref = &$children[$node->getId()];
                $thisref['parent_id'] = $node->getParent();
                if ($node->getParent() == 1)
                {
                    $list[$node->getId()] = &$thisref;
                } else
                {
                    $children[$node->getParent()]['children'][] = $node->getId();
                }
            }
            // Find active branch
            if ($this->current_node != 1)
            {
                if (array_key_exists($this->current_node, $nodeList))
                {

                    $parent_id = $children[$this->current_node]['parent_id'];
                    while ($parent_id != 1)
                    {
                        $this->active_branch[$parent_id] = $nodeList[$parent_id];
                        $parent_id = $children[$parent_id]['parent_id'];
                    }
                    $this->active_branch = array_reverse($this->active_branch, true);
                    $this->active_branch[$this->current_node] = $nodeList[$this->current_node];
                }
            }
        }

        public function getMenuTree()
        {
            if (null == $this->menu)
            {
                $this->menu = new RokMenuNodeTree(self::ROOT_ID);
                $this->populateMenuTree();
            }
            return $this->menu;
        }

    }

}