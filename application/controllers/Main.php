<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function index() //html
	{
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);


        $this->load->model("data/data_user");
        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");
        $this->load->model("logic/view_m");

        $this->view['data'] = array();
        $this->view['data']['filelist'] = $this->view_m->mainImageList();

        //$this->debug($this->view);

        echo $this->twig->render("main/main_index.twig", $this->view);
	}
}