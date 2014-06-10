<?php
/**
 * Created by PhpStorm.
 * User: mrecho
 * Date: 5/18/14
 * Time: 5:38 AM
 */



class data_upload extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

    public function saveUpload($filename, $type, $seconds, $tmbo, $nsfw, $metadata)
    {
        filter_var($filename, FILTER_SANITIZE_STRING);

        $userid = $this->session->userdata('userid');

        $time = date("Y-m-d H:i:s", $seconds);

        $sql = "INSERT INTO tmbo.offensive_uploads (`filename`, `type`, `metadata`,`userid`, `timestamp`, `tmbo`, `nsfw`)
                VALUES (".$this->db->escape($filename).", ".$this->db->escape($type).", ".$this->db->escape($metadata).", $userid, '".$time."', $tmbo, $nsfw) ";
        $this->db->query($sql);

        return $this->db->insert_id();

    }

} 