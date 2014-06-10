<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/26/14
 * Time: 11:43 PM
 */

class disc_m extends MY_Model{


    public function getComments($fileid){

        $list = $this->data_disc->getComments($fileid);

        $out = array();
        foreach($list as $k => $v){
            $out[$k] = $v;
            $out[$k]['tig'] = $this->stringVoteG($v['vote']);
            $out[$k]['tib'] = $this->stringVoteB($v['vote']);
        }

        //$this->debug($out);
        return $out;
    }

    private function stringVoteG($vote){
        if(strtolower($vote) == "this is good"){ return 1; }
        else { return 0; }
    }

    private function stringVoteB($vote){
        if(strtolower($vote) == "this is bad"){ return 1; }
        else { return 0; }
    }

    public function getDiscList($direction){

        $out = array();

        $ordertag = "id";

        $list = $this->data_disc->getDiscussions($ordertag, $direction);

        foreach($list as $k => $file){
            $comments = $this->data_disc->getComments($file['id']);

            $out[$k] = $file;
            $out[$k]['comments'] = $this->view_m->processCommentsIntoStats($comments);
            $out[$k]['oldist'] = $this->getOldistComment($comments);
        }

        //$this->debug($out);

        return $out;
    }

    /**
     * This data is from disc_m.getComments
     */
    public function hasVoted($comments){
        $userid = $this->session->userdata('userid');

        $voted = 0;

        foreach($comments as $k => $v){
            if($v['userid'] == $userid){ $voted = 1; }
        }

        return $voted;
    }

    public function getImageData($file_id){
        $filepost = $this->data_view->getFilePost($file_id,'image');

        $post = $this->view_m->sigleFileInfo($filepost);

        //$this->debug($post);

        return $post;
    }

    public function getTopicData($file_id){
        $filepost = $this->data_view->getFilePost($file_id,'topic');

        $post = $this->view_m->sigleFileInfo($filepost);

        //$this->debug($post);

        return $post;
    }

    public function processPost(){

        $fileid =    $this->input->post_get('fileid', TRUE);
        $type =      $this->input->post_get('type', TRUE);
        $comment =   $this->input->post_get('comment', TRUE);
        $vote =      $this->input->post_get('vote', TRUE);
        $offensive = $this->input->post_get('offensive', TRUE);
        $repost =    $this->input->post_get('repost', TRUE);
        $subscribe = $this->input->post_get('subscribe', TRUE);

        $time = time();

        if($offensive == "omg"){
            $offensive = 1;
        } else {
            $offensive = 0;
        }

        if($repost == "police"){
            $repost = 1;
        } else {
            $repost = 0;
        }

        $saved_id = $this->data_disc->saveComment($fileid, $comment, $vote, $offensive, $repost, $time);

        //TODO subscribe

        redirect("/discussions/$type/$fileid#$saved_id");

    }

    public function voteTIG($id,$session){

        $stats = array();
        $this_session = $this->session->userdata('session_id');

        //echo "$id,$session,$this_session";

        if($session == $this_session){

            $time = time();

            $comments = $this->data_disc->getComments($id);
            $stats = $this->view_m->processCommentsIntoStats($comments);

            $hasvoted = $this->hasVoted($comments);

            $vote = "this is good";

            if($hasvoted == 0){//TODO flag
                $this->data_disc->saveComment($id, '', $vote, 0, 0, $time);

                $stats['tig'] = $stats['tig'] + 1;
            }

        }//if session

        return $stats;
    }

    public function voteTIB($id,$session){

        $stats = array();
        $this_session = $this->session->userdata('session_id');

        //echo "$id,$session,$this_session";

        if($session == $this_session){

            $time = time();

            $comments = $this->data_disc->getComments($id);
            $stats = $this->view_m->processCommentsIntoStats($comments);

            $hasvoted = $this->hasVoted($comments);

            $vote = "this is bad";

            if($hasvoted == 0){//TODO flag
                $this->data_disc->saveComment($id, '', $vote, 0, 0, $time);

                $stats['tib'] = $stats['tib'] + 1;
            }

        }//if session

        return $stats;
    }

    public function getOldistComment($comments){

        $comm = array_pop($comments);

        return $comm;
    }

} 