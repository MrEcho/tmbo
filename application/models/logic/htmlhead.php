<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/16/14
 * Time: 1:48 AM
 */



class Htmlhead extends CI_Model{



    public function __construct()
    {
        parent::__construct();


    }

    public function mainControler($view){
        /** needs to be put into settings */
        $view['head']['title'] = "TMBO v2";

        $view['head']['js'][] = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js";

        $view['head']['css'][] = "//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css";
        $view['head']['js'][] = "/resources/js/jquery-ui-1.10.4.custom.min.js";

        $view['head']['css'][] = "/resources/css/style.css";
        $view['head']['js'][] = "/resources/js/main.js";

        return $view;
    }

}