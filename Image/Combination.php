<?php

class Image_Combination {

    private $canvas_width;
    private $canvas_height;
    private $image_type;
    private $image_path;
    private $image_save_path;
    private $image_resources = array();

    public function __construct ($canvas_width, $canvas_height) {
        $this->canvas_width  = $canvas_width;
        $this->canvas_height = $canvas_height;
    }

    public function setImageType ($image_type) {
        $this->image_type = $image_type;
    }

    public function setImagePath ($image_path) {
        $this->image_path = $image_path;
    }

    public function setImageSavePath ($iamge_path) {
        $this->image_save_path = $image_path;
    }

    public function push ($image) {

        $directory = $this->_directory($this->image_path);

        $this->image_resources[] = array(
                'file' => $directory . $image['file'],
                'x'    => $image['x'] ? $image['x'] : 0,
                'y'    => $image['y'] ? $image['y'] : 0
                );
    }

    public function save ($save_image) {

        $canvas    = $this->_generate();
        $directory = $this->_directory($this->image_save_path);

        switch ($this->image_type) {
            case 'png':
                imagepng($canvas, $directory.$save_image);
                break;
            case 'jpg':
            case 'jpeg':
                imagejpeg($canvas, $directory.$save_image);
                break;
            case 'gif':
                imagegif($canvas, $directory.$save_image);
                break;
        }
    }

    public function output () {

        $canvas = $this->_generate();

        switch ($this->image_type) {
            case 'png':
                header("Content-type: image/png");
                imagepng($canvas);
                break;
            case 'jpg':
            case 'jpeg':
                header("Content-type: image/jpeg");
                imagejpeg($canvas);
                break;
            case 'gif':
                header("Content-type: image/gif");
                imagegif($canvas);
                break;
        }
    }

    public function _directory ($image_path) {
        if ($image_path !== null && substr($image_path, -1) !== '/') {
            $image_path .= '/';
        }
        return $image_path;
    }

    public function _generate () {

        $canvas = imagecreatetruecolor($this->canvas_width, $this->canvas_height);
        $alpha  = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $alpha);

        foreach ($this->image_resources as $resource) {
            $image_info = getimagesize($resource['file']);

            switch ($this->image_type) {
                case 'png':
                    $image  = imagecreatefrompng($resource['file']);
                    break;
                case 'jpg':
                case 'jpeg':
                    $image  = imagecreatefromjpeg($resource['file']);
                    break;
                case 'gif':
                    $image  = imagecreatefromgif($resource['file']);
                    break;
            }

            imagecopy($canvas, $image, $resource['x'], $resource['y'], 0, 0, $image_info[0], $image_info[1]); 
        }

        return $canvas;
    }
}
