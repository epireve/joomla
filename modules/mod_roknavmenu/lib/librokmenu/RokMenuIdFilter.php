<?php
/**
 * @version   1.6 August 1, 2011
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokMenuIdFilter extends RecursiveFilterIterator {
    protected $id;

    public function __construct(RecursiveIterator $recursiveIter, $id) {
        $this->id = $id;
        parent::__construct($recursiveIter);
    }
    public function accept() {
        return $this->hasChildren() || $this->current()->getId() == $this->id;
    }

    public function getChildren() {
        return new self($this->getInnerIterator()->getChildren(), $this->id);
    }
}