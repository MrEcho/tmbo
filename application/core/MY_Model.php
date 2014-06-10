<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/26/14
 * Time: 9:08 PM
 */

class MY_Model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

    function debug($input,$exit=false){
        echo '<pre>';
        print_r($input);
        echo '</pre>';
        if($exit) { exit; }
    }
} 