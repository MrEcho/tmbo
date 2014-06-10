<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function index() //html
	{
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        echo $this->twig->render("login/login_index.twig", $this->view);
	}

    public function submit()
    {
        $nickname = $this->input->post_get('nickname', TRUE);
        $password = $this->input->post_get('password', TRUE);

        $this->load->model("logic/login_m");

        $this->islogedin = $this->login_m->httpAuth($nickname, $password);

        if($this->islogedin){
            redirect('/main');
        } else {
            redirect('/login');
        }

    }
}