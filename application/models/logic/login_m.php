<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/16/14
 * Time: 3:04 AM
 */

class Login_m  extends CI_Model{



    public function __construct()
    {
        parent::__construct();
    }

    public function httpAuth($name, $password){
        $isvalid = true;

        $this->session->set_userdata('islogedin', true);
        $this->session->set_userdata('userid', 1);

        //TODO everything!!!!

        return $isvalid;
    }
} 