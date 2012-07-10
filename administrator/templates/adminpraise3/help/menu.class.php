<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

class AdminPraise3Menu extends JObject {

	protected static $_instance;
	

	private function AdminPraise3Menu() {
		
	}
	
	public static function &getInstance() {
		if (AdminPraise3Menu::$_instance === null) :
			AdminPraise3Menu::$_instance = new AdminPraise3Menu();	
		endif;
		
		return AdminPraise3Menu::$_instance;	
	}
	
	function renderComponentMenu($active, $showChildren=false, $showParent=true) {
		$components = AdminPraise3MenuHelper::getComponents();
		$class = $active ? 'parent active' : 'parent';
		$html = '';
		if ($showParent) :
			$html .= '<li class="'.$class.'"><a href="index.php?ap_task=list_components" title="'.JText::_( 'COMPONENTS' ).'"><span class="component-name">'.JText::_( 'COMPONENTS' ).'</span><span class="subarrow"></span></a>';
		endif;
		$html .= '<ul class="component-list">';
		$html .= $this->renderComponentChildren($components, $showChildren);
		$html .= "</ul>";
		return $html;
	}
	
	function renderComponentChildren($components, $showChildren) {
		$stainless = AdminPraise3Tools::getInstance();
		$ap_task = $stainless->get('ap_task');
		$html = '';
		$k = 0;
		foreach ($components AS $i => $row) :
			$html .= '<li class="parent '.$row->option.'">';
			
            if ($row->admin_menu_link) :
            	$ignore_first = false;
			    $html .= '<a href="index.php?'.$row->admin_menu_link.'" class="parent-link" title="'.JText::_($row->name).'"><div class="component-image"></div><span class="component-label">'.JText::_($row->name).'</span></a>';
			else : 
				$ignore_first = true;
                $html .= '<a href="index.php?option='.$row->option.'" class="parent-link" title="'.JText::_($row->name).'"><div class="component-image"></div><span class="component-label">'.JText::_($row->name).'</span></a>';
			endif;
			      	        
			if(($showChildren) || ($ap_task == "list_components")) :
				if(count($row->children)) :
					$html .= "<ul class=\"child-list\">";
					$html .= "<li class=\"child first-child\"><span class=\"triangle\"></span><a href=\"index.php?option=$row->option\"><span class=\"component-child-image\"></span><span>".JText::_($row->name)."</span></a></li>";
      	        	foreach ($row->children AS $i2 => $child): 
						if($i2 == 0 && $ignore_first) :
							continue;
						endif;
						$html .= "<li class=\"child\">";

      	        		$html .= "<a href='index.php?$child->admin_menu_link' title=\"".JText::_($child->name)."\"><span class=\"component-child-image\"></span><span>".JText::_($child->name)."</span></a></li>";
      	        		$k = 1 - $k;
      	        	endforeach;
					$html .= "</ul>";
				endif;
			endif;
			$html .= "</li>";
			$k = 1 - $k;
		endforeach;
		return $html;
	}
	
	function renderComponentFooter($components) {
		$stainless = AdminPraise3Tools::getInstance();
		$ap_task = $stainless->get('ap_task');
		$html = '';
		$k = 0;
		foreach ($components AS $i => $row) :
			$html .= '<li class="parent '.$row->option.'">';
			
            if ($row->admin_menu_link) :
            	$ignore_first = false;
			    $html .= '<a href="index.php?'.$row->admin_menu_link.'" class="parent-link hasTip" title="'.JText::_($row->name).'::"><div class="component-image"></div><span class="component-label">'.JText::_($row->name).'</span></a>';
			else : 
				$ignore_first = true;
                $html .= '<a href="index.php?option='.$row->option.'" class="parent-link hasTip" title="'.JText::_($row->name).'::"><div class="component-image"></div><span class="component-label">'.JText::_($row->name).'</span></a>';
			endif;
			      	        
			$html .= "</li>";
			$k = 1 - $k;
		endforeach;
		return $html;
	}
	
	function renderMenusMenu($active) {
		$menus = AdminPraise3MenuHelper::getMenus();

		$links['parent'] = array('url' => 'index.php?option=com_menus', 'text' => 'MENUS');
		$links['children'] = array();
		
		for($i=0; $i<count($menus); $i++) :
			$menu = $menus[$i];
			$url = 'index.php?option=com_menus&task=view&menutype='.$menu->menutype;
			$newLink = array('url' => $url, 'text' => $menu->title);
			$newLink['children'] = array(array('url' => $url.'&task=newItem', 'text' => JText::_('NEW').' '.$menu->title.' '.JText::_('ITEM')));
			$links['children'][] = $newLink;
		endfor;

		return $this->renderDropdown($links, $active);
	}
	
	function renderCustomComponentMenu($type, $active) {
		$links = AdminPraise3MenuHelper::getCustomComponentLinks($type);
	
		if (empty($links))
			return null;
		
		return $this->renderDropdown($links, $active);
	}
	
	function renderMainMenu($type, $active) {
		$links = AdminPraise3MenuHelper::getMainMenuLinks($type);
	
		if (empty($links))
			return null;
		
		return $this->renderDropdown($links, $active);
	}
	
	function renderSubmenu($type) {
		$links = AdminPraise3MenuHelper::getSubMenuLinks($type);
		return $this->renderChildren($links);
	}
	
	function renderDropdown($links, $active) {
		$aclass = isset($links['parent']['a-class']) ? 'class="'.$links['parent']['a-class'].'"' : '';
		$class = isset($links['parent']['li-class']) ? $links['parent']['li-class'].' ' : ''; 
		$class .= $active ? 'parent active' : 'parent';
		$html = "<li class='$class'>";
		$html .= "<a href='".$links['parent']['url']."' ".$aclass."><span class=\"parent-name\">".JText::_($links['parent']['text'])."</span><span class=\"subarrow\"></span></a>";
		
		if (isset($links['children'])) :
			$html .= $this->renderChildren($links['children']);
		endif;
		
		$html .= "</li>";
		return $html;
	}
	
	function renderChildren($children, $level=0, $recursive=true) {
		$html = null;
		if (count($children)) :
			$class = $level == 0 ? 'class="submenu"' : '';
			$html .= "<ul ".$class.">";
			foreach($children as $link) :
				$liclass = isset($link['li-class']) ? 'class="'.$link['li-class'].'"' : '';
				$liaclass = isset($link['a-class']) ? 'class="'.$link['a-class'].'"' : '';
				if (isset($link['a-class']) && $link['a-class'] == "modal"){
				$lirel = 'rel="{handler: \'iframe\', size: {x: 900, y: 550}}"';
				}else{
				$lirel = '';
				}
				$html .= "<li '.$liclass.'><a href='".$link['url']."' ".$liaclass." ".$lirel.">".JText::_($link['text'])."</a>";
				if (isset($link['children']) && $recursive) :
					$level++;
					$html .= $this->renderChildren($link['children'], $level);
				endif;
				$html .= "</li>";
			endforeach;
			$html .= "</ul>";
		endif;
		return $html;
	}
	
}