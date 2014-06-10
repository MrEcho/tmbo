<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/27/14
 * Time: 5:45 AM
 */

class data_disc extends MY_Model{


    public function saveComment($fileid, $comment, $vote, $offensive, $repost, $seconds){

        filter_var($comment, FILTER_SANITIZE_STRING);

        $userid = $this->session->userdata('userid');

        $time = date("Y-m-d H:i:s", $seconds);

        $sql = "INSERT INTO tmbo.offensive_comments (`userid`, `fileid`, `comment`, `vote`, `offensive`, `repost`, `timestamp`)
                VALUES ($userid, $fileid, ".$this->db->escape($comment).", '".$vote."', $offensive, $repost, '".$time."') ";
        $this->db->query($sql);

        return $this->db->insert_id();

    }

    public function getComments($fileid){

        $query = $this->db->query("SELECT * FROM tmbo.offensive_comments WHERE `fileid`=".$fileid." ");

        $out = array();

        foreach ($query->result_array() as $row)
        {
            $out[$row['id']] = $row;
        }

        return $out;
    }

    public function getDiscussions($ordertag, $direction){

        // this is going to be way more complex, must do ranges

        $query = $this->db->query("SELECT * FROM tmbo.offensive_uploads WHERE  `type`='topic' order by `".$ordertag."` ".$direction." LIMIT 50");

        $out = array();

        foreach ($query->result_array() as $row)
        {
            $out[$row['id']] = $row;
        }

        return $out;

    }
} 