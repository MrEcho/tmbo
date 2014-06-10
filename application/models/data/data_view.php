<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/19/14
 * Time: 3:43 AM
 */

class data_view extends CI_Model{

    // file:///Users/mrecho/temp/git_codeigniter/user_guide_src/build/html/database/results.html

    public function __construct()
    {
        parent::__construct();
    }

    public function getFilePost($file_id,$type){

        $query = $this->db->query("SELECT * FROM tmbo.offensive_uploads WHERE `id`='".$file_id."' AND `type`='".$type."' LIMIT 1");

        $row = $query->row_array();

        return $row;
    }

    public function getPrevPost($file_id,$type){

        $query = $this->db->query("SELECT * FROM tmbo.offensive_uploads WHERE `id`<'".$file_id."' AND `type`='".$type."' order by `id` desc LIMIT 1");

        $row = $query->row_array();

        return $row;
    }

    public function getNextPost($file_id,$type){

        $query = $this->db->query("SELECT * FROM tmbo.offensive_uploads WHERE `id`>'".$file_id."' AND `type`='".$type."' LIMIT 1");

        $row = $query->row_array();

        return $row;
    }

    public function imageList($limit=50,$order='desc'){
        $query = $this->db->query("SELECT * FROM tmbo.offensive_uploads WHERE `type`='image' AND `status`='normal' order by `id` ".$order." LIMIT ".$limit." ");

        $out = array();

        foreach ($query->result_array() as $row)
        {
            $out[$row['id']] = $row;
            $out[$row['id']]['metadata'] = json_decode($row['metadata']);
        }

        return $out;
    }


} 