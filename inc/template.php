<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

class LP_Template {
    
    public $id;
    public $name;
    public $image;

    function __construct($id, $name, $image) {
        $this->id = $id;
        $this->name = $name;
        $this->image = $image;
    }

    function get_template() {
        return array('id' => $this->id, 'name' => $this->name, 'image' => $this->image);
    }
}