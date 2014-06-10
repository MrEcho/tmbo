<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Uploadfile extends MY_Controller {

	public function index() //html
	{
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        echo $this->twig->render("upload/upload_index.twig", $this->view);
	}

    public function do_upload(){

        $config['upload_path']          = APPPATH.'/../temp_uploads/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|mp3';
        //$config['max_size']             = 100;
        $config['min_width']            = 20;
        $config['min_height']           = 20;

        $this->load->library('upload', $config);
        $this->load->model("data/data_upload");

        if ( ! $this->upload->do_upload('rawfile'))
        {
            $this->debug($this->upload->display_errors());
        }
        else
        {
            $data = $this->upload->data();

            $this->load->model("logic/upload_m", "upload_m");

            $this->upload_m->submit($data);

        }
    }

    public function uploaddone($type,$file_id){

        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        $this->view['upload']['file_id'] = $file_id;
        $this->view['upload']['type'] = $type;

        $lastupload = $this->session->userdata('lastupload');

        $this->view['upload']['data'] = $lastupload;

        echo $this->twig->render("upload/upload_uploaddone.twig", $this->view);

    }

}