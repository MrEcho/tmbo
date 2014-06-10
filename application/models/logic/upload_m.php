<?php
    /**
     * Created by PhpStorm.
     * User: mrecho
     * Date: 5/16/14
     * Time: 10:50 PM
     */
    //use getID3;

    class upload_m extends MY_Model
    {

        var $filedata;

        public function submit($data)
        {
            $this->session->set_userdata('lastupload', $data);

            $this->filedata = $data;

            $metadata = array();

            $this->filedata['time'] = time();


            $nsfw = $this->input->post_get('nsfw', TRUE);
            $tmbo = $this->input->post_get('tmbo', TRUE);

            $this->filedata['tmbo_flag'] = $this->common->checkBoxBolean($tmbo);
            $this->filedata['nsfw_flag'] = $this->common->checkBoxBolean($nsfw);

            // https://thismight.be/offensive/uploads/2014/05/16/image/374398_coooool.gif
            // https://thismight.be/offensive/pages/pic.php?id=374398

            // https://thismight.be/offensive/uploads/2014/05/14/audio/374166_Me%20First%20And%20The%20Gimme%20Gimmes%20-%20Straight%20Up.mp3
            // https://thismight.be/offensive/pages/pic.php?id=374166

            // https://stackoverflow.com/questions/13646028/how-to-remove-exif-from-a-jpg-without-losing-image-quality

            // `type` enum('image','topic','audio','video','avatar')
            $this->filedata['save_type'] = "image";

            if ($data['is_image']) {
                $this->processImage($this->filedata);
                $this->filedata['save_type'] = "image";

                $metadata = $this->filedata['exif'];
            }

            if ($data['file_ext'] == ".mp3") {
                $this->processMP3($this->filedata);
                $this->filedata['save_type'] = "audio";

                $metadata = $this->filedata['id3'];
            }



            //size and dimensions check

            // if everything ok...

            $metadata['file_type']    = $this->filedata['file_type'];
            $metadata['file_ext']     = $this->filedata['file_ext'];
            $metadata['file_size']    = $this->filedata['file_size'];
            $metadata['image_width']  = $this->filedata['image_width'];
            $metadata['image_height'] = $this->filedata['image_height'];

            $json_metadata = json_encode($metadata);
            $filename      = trim($this->filedata['client_name']);
            $file_id       = $this->data_upload->saveUpload($filename, $this->filedata['save_type'], $this->filedata['time'], $this->filedata['tmbo_flag'], $this->filedata['nsfw_flag'],  $json_metadata);

            if (is_numeric($file_id)) {
                $this->moveFilestoUploads($file_id, $this->filedata);
            }

            //$this->debug($this->filedata);

            $this->session->set_userdata('lastupload', $this->filedata);

            redirect("/uploadfile/uploaddone/" . $this->filedata['save_type'] . "/$file_id");
        }

        public function processImage($data)
        {

            // wow http://www.sno.phy.queensu.ca/~phil/exiftool/index.html

            if ($data['file_type'] == "image/jpeg") {
                $exif = exif_read_data($data['full_path'], 'ANY_TAG');

                $data['exif'] = array();

                $aperture                 = $this->exifAperture($exif);
                $data['exif']['aperture'] = $aperture;

                //$ShutterSpeedValue = $this->exifShutterSpeed($exif);
                //$data['exif']['shutterspeed'] = $ShutterSpeedValue;

                $data['exif']['exposure'] = trim($exif['ExposureTime']);
                $data['exif']['iso']      = trim($exif['ISOSpeedRatings']);

                $FocalLength                 = $this->exifFocalLength($exif);
                $data['exif']['focallength'] = $FocalLength;

                $data['exif']['make']  = trim($exif['Make']);
                $data['exif']['model'] = trim($exif['Model']);

                $data['exif']['image_width']  = $data['image_width'];
                $data['exif']['image_height'] = $data['image_height'];

                $Lens                 = $this->exifLens($exif);
                $data['exif']['lens'] = $Lens;

                $this->removeEXIF($data);

                $this->filedata = $data;
            }

            if ($data['file_type'] == "image/gif") {

                // im->optimizeImageLayers();
                // http://www.php.net/manual/en/imagick.optimizeimagelayers.php

                $data['exif']   = array();
                $this->filedata = $data;
            }

            if ($data['file_type'] == "image/png") {
                $data['exif']   = array();
                $this->filedata = $data;
            }

            $this->createThumbnail($data);

        }

        public function processMP3($data)
        {

            // https://github.com/JamesHeinrich/getID3/

            if ($data['file_type'] == "audio/mpeg") {

                $data['id3'] = array();

                $getID3 = new \getID3();
                $getID3->include_module('audio.mp3');

                $ThisFileInfo = $getID3->analyze($data['full_path']);

                getid3_lib::CopyTagsToComments($ThisFileInfo);


                $rawdata = @$ThisFileInfo['id3v2']['APIC']['0']['data'];

                if ($rawdata != null) {
                    $rawdata = @$ThisFileInfo['comments']['picture']['0']['data'];
                }

                $tempfile = "thumbnail.jpg";

                if ($rawdata != null) {
                    $handle = fopen($data['file_path'] . $data['raw_name'] . ".$tempfile", "c");
                    fwrite($handle, $rawdata);
                    fclose($handle);

                    $data['thumbnail'] = $data['file_path'] . $data['raw_name'] . ".$tempfile";
                } else {

                }

                $id3v2 = $this->compressid3v2($ThisFileInfo['tags_html']['id3v2']);

                $data['id3'] = array_merge($data['id3'], $id3v2);

                //echo '<pre>';
                //print_r($ThisFileInfo);
                //echo '</pre>';

                $this->filedata = $data;
            }

        }

        public function moveFilestoUploads($file_id, $data)
        {

            $temp_path      = $data['full_path'];
            $temp_thumbnail = @$data['thumbnail'];
            $file_name      = trim($data['client_name']);
            $type           = $data['save_type'];

            $file_name = str_replace(" ","_", $file_name);

            // https://thismight.be/offensive/uploads/2014/05/18/image/374492_supersonic%20electronic%20%20art%20-%20James%20Jirat%20Patradoon.gif
            // https://thismight.be/offensive/uploads/2014/05/18/image/thumbs/th374492.gif

            // mp3 https://thismight.be/offensive/uploads/2014/04/28/audio/thumbs/th373493

            $now_sec = $data['time'];

            $day_string = date("Y/m/d/", $now_sec);

            $save_path = UPLOADS_DIR . "/$day_string/$type/";

            $save_filename = $file_id . "_" . $file_name;//TODO xxxx

            $save_path_th = $save_path . "/thumbs/";

            if ($type == "image") {
                $save_filename_th = "th$file_id" . $data['file_ext'];
            } else {
                if ($type == "audio") {
                    $save_filename_th = "th$file_id";
                }
            }

            $mkdir   = @mkdir($save_path, 0777, true); //could already be there
            $mkdirth = @mkdir($save_path_th, 0777, true);

            if (file_exists($temp_path)) {
                rename($temp_path, "$save_path/$save_filename");
                @rename($temp_thumbnail, "$save_path_th/$save_filename_th");
            }

        }


        public function removeEXIF($data){

            try{
                // Needs more testing, colors where a bit off on my mac
                // colorspace issue?  $image->setImageColorSpace(Imagick::COLORSPACE_RGB);
                /*
                $img = new Imagick($data['full_path']);
                $profiles = $img->getImageProfiles("*", true);
                $img->setCompression(imagick::COMPRESSION_JPEG);
                $img->setCompressionQuality(100);
                $img->stripImage();

                if(!empty($profiles))
                    $img->profileImage("icc", $profiles['icc']);

                $img->writeImage($data['full_path']);

                $img->destroy();
                */
            }catch (Exception $e) {
                echo $e->getMessage();
            }
        }


        public function createThumbnail($data)
        {

            $maxWidth  = 200;
            $maxHeight = 100;

            if ($data['file_type'] == "image/gif") {
                $maxWidth  = 100;
                $maxHeight = 50;

            }

            $file = $data['raw_name'] . ".thumbnail" . $data['file_ext'];

            $fitbyWidth = (($maxWidth / $data['image_width']) < ($maxHeight / $data['image_height'])) ? true : false;

            try {
                $thumb = new Imagick($data['full_path']);

                if ($data['file_type'] == "image/gif") {
                    /*** Loop through the frames ***/
                    $count = 0;
                    foreach ($thumb as $frame) {

                        // Limits to only the first 10 frames of the gif
                        if($count >= 10){ $frame->thumbnailImage(1, 1, false); }
                        else {
                            if ($fitbyWidth) {
                                $frame->thumbnailImage($maxWidth, 0, false);
                            } else {
                             $frame->thumbnailImage(0, $maxHeight, false);
                            }
                        }
                        $count++;
                    }

                    $thumb->writeImages($data['file_path'] . $file, true);

                } else {
                    if ($data['file_type'] == "image/jpeg" || $data['file_type'] == "image/png") {

                        $thumb->stripImage();


                        if ($fitbyWidth) {
                            $thumb->thumbnailImage($maxWidth, 0, false);
                        } else {
                            $thumb->thumbnailImage(0, $maxHeight, false);
                        }

                    }

                    $thumb->writeImage($data['file_path'] . $file);
                }

                $thumb->destroy();

            } catch (Exception $e) {
                echo $e->getMessage();
            }

            $this->filedata['thumbnail'] = $data['file_path'] . $file;

        }

        public function exifAperture($exif)
        {
            $FNumber = @$exif['FNumber'];

            $value = null;
            if (!is_null($FNumber)) {
                list($max, $at) = explode('/', $FNumber);

                $value = $max / $at;

                $value = round($value, 1, PHP_ROUND_HALF_DOWN);
            }

            return $value;
        }

        public function exifShutterSpeed($exif)
        {
            $ShutterSpeedValue = @$exif['ShutterSpeedValue'];

            $value = null;
            if (!is_null($ShutterSpeedValue)) {
                list($max, $at) = explode('/', $ShutterSpeedValue);

                $value = $max / $at;

                $value = round($value, 1, PHP_ROUND_HALF_DOWN);
            }

            return $value;
        }

        public function exifFocalLength($exif)
        {
            $FocalLength = @$exif['FocalLength'];

            $value = null;
            if (!is_null($FocalLength)) {
                list($max, $at) = explode('/', $FocalLength);

                $value = $max / $at;

                $value = round($value, 2, PHP_ROUND_HALF_DOWN);
            }

            return $value;
        }

        public function exifLens($exif)
        {
            // http://www.exiv2.org/tags-nikon.html
            // http://www.rottmerhusen.com/objektives/lensid/nikkor.html
            // http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/Canon.html

            $lens = "";

            if (@$exif['Make'] == "Canon") {
                $lens = trim($exif['UndefinedTag:0xA434']);
            }
            if (@$exif['Make'] == "Apple") {
                $lens = trim($exif['UndefinedTag:0xA434']);
            }

            return $lens;
        }

        public function compressid3v2($array)
        {

            $out = array();

            foreach ($array as $k => $v) {
                $out[$k] = $v[0];
            }

            return $out;
        }
    }

    /**
     *
     * [file_name] => mp_2.30am_.jpg
     * [file_type] => image/jpeg
     * [file_path] => /Users/mrecho/Sites/tmbo/temp_uploads/
     * [full_path] => /Users/mrecho/Sites/tmbo/temp_uploads/mp_2.30am_.jpg
     * [raw_name] => mp_2.30am_
     * [orig_name] => mp_2.30am_.jpg
     * [client_name] => [mp] 2.30am_.jpg
     * [file_ext] => .jpg
     * [file_size] => 474.75
     * [is_image] => 1
     * [image_width] => 1200
     * [image_height] => 519
     * [image_type] => jpeg
     * [image_size_str] => width="1200" height="519"

     */