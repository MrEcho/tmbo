<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/18/14
 * Time: 10:52 AM
 */

class Viewpost extends MY_Controller {

    public function index() //html
    {
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        echo $this->twig->render("view/view_index.twig", $this->view);
    }

    public function image($file_id){ //needs to be set in routes.php

        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        $this->load->model("data/data_user");
        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/disc_m");
        $this->load->model("logic/view_m");

        $this->view['sessionid'] = $this->session->userdata('session_id');
        $this->view['data'] = $this->view_m->imageView($file_id);

        //$this->debug($this->view);

        echo $this->twig->render("view/view_image.twig", $this->view);
    }

    public function voteTIG(){//ajax
        $session = $this->input->post_get('session', TRUE);
        $id = $this->input->post_get('id', TRUE);

        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/view_m");
        $this->load->model("logic/disc_m");

        $stats = $this->disc_m->voteTIG($id,$session);

        echo json_encode($stats);

    }

    public function voteTIB(){//ajax
        $session = $this->input->post_get('session', TRUE);
        $id = $this->input->post_get('id', TRUE);

        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/view_m");
        $this->load->model("logic/disc_m");

        $stats = $this->disc_m->voteTIB($id,$session);

        echo json_encode($stats);
    }
}