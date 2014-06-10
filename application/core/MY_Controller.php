<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/15/14
 * Time: 10:38 PM
 */


    class MY_Controller extends CI_Controller{

        var $twig;
        var $view = array();
        var $ishtml = true;
        var $islogedin = false;

        function __construct()
        {
            parent::__construct();

            $this->output->enable_profiler(FALSE);

            $this->setupView();

            $this->checkLogin();

        }

        function checkLogin(){
            $this->islogedin = $this->session->userdata('islogedin');

            if( ($this->uri->segment(1) != "login" && $this->uri->segment(2) != "submit") && ($this->islogedin == false || $this->islogedin == null) ){
                redirect('/login');
            }
        }

        function setupView(){
            $this->view['head'] = array();
            $this->view['head']['css'] = array();
            $this->view['head']['js'] = array();
            $this->view['head']['title'] = "";
            $this->view['head']['raw'] = "";
        }

        function loadTwig(){
            if($this->ishtml){
                $loader = new Twig_Loader_Filesystem(VIEWPATH);
                $this->twig = new Twig_Environment($loader);
            }
        }

        function debug($input,$exit=false){
            echo '<pre>';
            print_r($input);
            echo '</pre>';
            if($exit){exit;};
        }

    }