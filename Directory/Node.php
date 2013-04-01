<?php

namespace Raindrop\PageBundle\Directory;

class Node {

    const ROOT = '__ROOT__';

    protected $path;

    protected $name;

    protected $title;

    protected $parent;

    protected $page_id;

    protected $children = array();

    public function __construct($name, $parent = self::ROOT) {
        $this->name = $name;
        $this->parent = $parent;
        $this->initPath();
    }

    public function initPath() {

        $base = DIRECTORY_SEPARATOR;
        if ($this->parent instanceof Node && $this->parent->getName() != self::ROOT) {
            $base = $this->parent->getPath() . DIRECTORY_SEPARATOR;
        }

        $suffix = $this->name != self::ROOT ? $this->getName() : '';

        $this->setPath($base . $suffix);
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;

        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setPageId($page_id) {
        $this->page_id = $page_id;
    }

    public function getPageId() {
        return $this->page_id;
    }

    public function getParent() {
        return $this->parent;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function hasParent() {
        return $this->parent instanceof Node;
    }

    public function getParentPath() {
        if ($this->hasParent()) {
            return $this->getParent()->getPath();
        }

        return null;
    }

    public function getChildren() {
        return $this->children;
    }

    public function hasChildren() {
        return !empty($this->children);
    }

    public function hasChild($name) {
        return isset($this->children[$name]);
    }

    public function getChild($name) {
        return $this->children[$name];
    }

    public function addChild($node) {
        $this->children[$node->getName()] = $node;

        return $this;
    }

    public function toArray() {

        $return = array(
            'name' => $this->getName(),
            'path' => $this->getPath(),
            'page_id' => $this->getPageId(),
            'title' => $this->getTitle(),
            'parent' => $this->getParentPath(),
            'children' => array()
        );

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                $return ['children'][$child->getName()] = $child->toArray();
            }
        }

        return $return;
    }

    public function dumpGraph($indent = 0) {

        $string = '';

        if ($indent > 0) {
            if ($indent > 1) {
                for ($i = 0; $i < $indent - 1; $i++) {
                    $string .= '   ';
                }
            }

            $string .= ' + ';
        }

        var_dump($string . $this->getName());

        if ($this->hasChildren()) {
            foreach ($this->getChildren() as $child) {
                $child->dumpGraph($indent+1);
            }
        }
    }
}