<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokMenuNotOnActiveTreeFilter extends RecursiveFilterIterator {
    protected $active_tree = array();
    protected $level;

    public function __construct(RecursiveIterator $recursiveIter, $active_tree, $end) {
        $this->level = $end;
        $this->active_tree = $active_tree;
        parent::__construct($recursiveIter);
    }
    public function accept() {

        if (!array_key_exists($this->current()->getId(),$this->active_tree) && $this->current()->getParent() == end(array_keys($this->active_tree))){
            $this->active_tree[$this->current()->getId()] = $this->current();
        }
        if (array_key_exists($this->current()->getId(),$this->active_tree) && $this->current()->getLevel() > $this->level+1){
            return true;
        }
        else if (!array_key_exists($this->current()->getId(),$this->active_tree) && $this->current()->getLevel() > $this->level){
            return true;
        }
        else if ($this->hasChildren()){
            return true;
        }
        return false;
    }

    public function getChildren() {
        return new self($this->getInnerIterator()->getChildren(), $this->active_tree, $this->level);
    }
}
