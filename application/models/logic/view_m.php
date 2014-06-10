<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/19/14
 * Time: 3:41 AM
 */

class view_m extends MY_Model{


    public function mainImageList($start="0",$end="50"){

        $uploads = $this->data_view->imageList();

        foreach($uploads as $k => $file){
            $comments = $this->data_disc->getComments($file['id']);

            $uploads[$k]['comments'] = $this->processCommentsIntoStats($comments);
        }

        //$this->debug($uploads);

        return $uploads;
    }

    public function imageView($file_id){

        $out = array();

        $filepost = $this->data_view->getFilePost($file_id,'image');

        $prevImage = $this->data_view->getPrevPost($file_id,'image');

        $nextImage = $this->data_view->getNextPost($file_id,'image');

        //echo "prevImage = $prevImage<br>";
        //echo "nextImage = $nextImage<br>";

        $out['prev'] = $prevImage;
        $out['prev']['metadata'] = json_decode($prevImage['metadata']);
        $out['prev']['hover'] = $this->hovertext($prevImage);

        $out['next'] = $nextImage;
        $out['next']['metadata'] = json_decode($nextImage['metadata']);
        $out['next']['hover'] = $this->hovertext($nextImage);

        $out['file'] = $this->sigleFileInfo($filepost);



        //$this->debug($out);

        return $out;
    }

    public function sigleFileInfo($filepost){

        $datestring = $filepost['timestamp'];
        $dateint = strtotime($datestring);
        $datepath = date("Y/m/d", $dateint);

        $filepost['datepath'] = $datepath;

        $filepost['metadata'] = json_decode($filepost['metadata']);

        $filepost['filename'] = str_replace(" ","_", $filepost['filename']);

        $comments = $this->data_disc->getComments($filepost['id']);
        $filepost['comments'] = $this->processCommentsIntoStats($comments);
        $filepost['hasvoted'] = $this->disc_m->hasVoted($comments);


        return $filepost;
    }

    public function processCommentsIntoStats($arrayofcomments){

        $text = 0;
        $tig = 0;
        $tib = 0;
        $offensive = 0;
        $repost = 0;

        foreach($arrayofcomments as $post){
            if(trim($post['comment']) != ""){ $text++; }
            if($post['offensive'] == 1){ $offensive++; }
            if($post['repost'] == 1){ $repost++; }

            if(strtolower($post['vote']) == "this is good"){
                $tig++;
            }
            if(strtolower($post['vote']) == "this is bad"){
                $tib++;
            }
        }

        $out = array();
        $out['text'] = $text;
        $out['tig'] = $tig;
        $out['tib'] = $tib;
        $out['offensive'] = $offensive;
        $out['repost'] = $repost;

        return $out;
    }

    public function hovertext($post){

        $out = "";

        if($post['nsfw'] == 1){
            $out .= "[nsfw] ";
        }
        if($post['tmbo'] == 1){
            $out .= "[tmbo] ";
        }

        $out .= $post['filename'];

        return $out;
    }
}