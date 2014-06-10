<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/26/14
 * Time: 11:35 PM
 */

class Discussions extends MY_Controller {

    public function index($direction='desc') //html
    {
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        $this->load->model("data/data_user");
        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/view_m");
        $this->load->model("logic/disc_m");
        $this->view['data']['posts'] = $this->disc_m->getDiscList($direction);

        echo $this->twig->render("discussions/disc_index.twig", $this->view);
    }

    public function image($fileid){
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        $this->load->model("data/data_user");
        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/view_m");
        $this->load->model("logic/disc_m");

        $this->view['data']['post'] = $this->disc_m->getImageData($fileid);
        $this->view['data']['posts'] = $this->disc_m->getComments($fileid);
        //$this->view['data']['hasvoted'] = $this->disc_m->hasVoted($this->view['data']['posts']);

        //$this->debug($this->view);

        echo $this->twig->render("discussions/disc_image.twig", $this->view);
    }

    public function topic($fileid){
        $this->ishtml = true;
        $this->loadTwig();

        $this->load->model("logic/htmlhead", "htmlhead");

        $this->view = $this->htmlhead->mainControler($this->view);

        $this->load->model("data/data_user");
        $this->load->model("data/data_view");
        $this->load->model("data/data_disc");

        $this->load->model("logic/view_m");
        $this->load->model("logic/disc_m");

        $this->view['data']['post'] = $this->disc_m->getTopicData($fileid);
        $this->view['data']['posts'] = $this->disc_m->getComments($fileid);

        //$this->debug($this->view);

        echo $this->twig->render("discussions/disc_topic.twig", $this->view);
    }

    public function post(){

        $this->load->model("data/data_disc");
        $this->load->model("logic/disc_m");
        $this->disc_m->processPost();

    }

} 