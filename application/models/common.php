<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/26/14
 * Time: 9:13 PM
 */



class common extends MY_Model{


    public function checkBoxBolean($raw){

        $out = 0;

        $raw = trim($raw);
        if($raw == "on" || $raw == 1){
            $out = 1;
        } else {
            $out = 0;
        }

        return $out;
    }
} 