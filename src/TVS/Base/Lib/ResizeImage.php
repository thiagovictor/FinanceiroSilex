<?php

namespace TVS\Base\Lib;

class ResizeImage {

    private $options = [
        'MAX_WIDTH' => 1500,
        'MAX_HEIGHT' => 1500,
        'DEFAULT_Q' => 100,
        'DEFAULT_S' => 0,
        'DEFAULT_ALIGN' => 'c',
        'DEFAULT_CC' => 'ffffff',
        'DEFAULT_WIDTH' => 40,
        'DEFAULT_HEIGHT' => 40,
        'DEFAULT_ZC' => 1,
        'LOCAL_IMAGE' => '',
        'LOCAL_NEW_IMAGE' => ''
    ];
    
    public function setAttribute($attribute, $value) {
        if(isset($this->options[$attribute])){
            $this->options[$attribute]=$value;
            return true;
        }
        return false;
    }
    
    function processImageAndWriteToCache() {
        $sData = getimagesize($this->options["LOCAL_IMAGE"]);
        $mimeType = $sData['mime'];

        if (!preg_match('/^image\/(?:jpg|jpeg)$/i', $mimeType)) {
            return false;
        }

        if (!function_exists('imagecreatetruecolor')) {
            return false;
            //var_dump('GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library');
        }

        $new_width = min($this->options["DEFAULT_WIDTH"], $this->options["MAX_WIDTH"]);
        $new_height = min($this->options["DEFAULT_HEIGHT"], $this->options["MAX_HEIGHT"]);

        $image = imagecreatefromjpeg($this->options["LOCAL_IMAGE"]);


        $width = imagesx($image);
        $height = imagesy($image);
        $origin_x = 0;
        $origin_y = 0;

        // generate new w/h if not provided
        if ($new_width && !$new_height) {
            $new_height = floor($height * ($new_width / $width));
        } else if ($new_height && !$new_width) {
            $new_width = floor($width * ($new_height / $height));
        }

        // scale down and add borders
        if ($this->options["DEFAULT_ZC"] == 3) {
            $final_height = $height * ($new_width / $width);
            if ($final_height > $new_height) {
                $new_width = $width * ($new_height / $height);
            } else {
                $new_height = $final_height;
            }
        }

        // create a new true color image
        $canvas = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($canvas, false);

        if (strlen($this->options["DEFAULT_CC"]) == 3) { //if is 3-char notation, edit string into 6-char notation
            $this->options["DEFAULT_CC"] = str_repeat(substr($this->options["DEFAULT_CC"], 0, 1), 2) . str_repeat(substr($this->options["DEFAULT_CC"], 1, 1), 2) . str_repeat(substr($this->options["DEFAULT_CC"], 2, 1), 2);
        } else if (strlen($this->options["DEFAULT_CC"]) != 6) {
            $this->options["DEFAULT_CC"] = DEFAULT_CC; // on error return default canvas color
        }

        $canvas_color_R = hexdec(substr($this->options["DEFAULT_CC"], 0, 2));
        $canvas_color_G = hexdec(substr($this->options["DEFAULT_CC"], 2, 2));
        $canvas_color_B = hexdec(substr($this->options["DEFAULT_CC"], 4, 2));

        $color = imagecolorallocatealpha($canvas, $canvas_color_R, $canvas_color_G, $canvas_color_B, 0);

        imagefill($canvas, 0, 0, $color);

        // scale down and add borders
        if ($this->options["DEFAULT_ZC"] == 2) {
            $final_height = $height * ($new_width / $width);
            if ($final_height > $new_height) {
                $origin_x_ = $new_width / 2;
                $new_width = $width * ($new_height / $height);
                $origin_x = round($origin_x_ - ($new_width / 2));
            } else {
                $origin_y_ = $new_height / 2;
                $new_height = $final_height;
                $origin_y = round($origin_y_ - ($new_height / 2));
            }
        }

        // Restore transparency blending
        imagesavealpha($canvas, true);

        if ($this->options["DEFAULT_ZC"] > 0) {
            $src_x = $src_y = 0;
            $src_w = $width;
            $src_h = $height;
            $cmp_x = $width / $new_width;
            $cmp_y = $height / $new_height;
            // calculate x or y coordinate and width or height of source
            if ($cmp_x > $cmp_y) {
                $src_w = round($width / $cmp_x * $cmp_y);
                $src_x = round(($width - ($width / $cmp_x * $cmp_y)) / 2);
            } else if ($cmp_y > $cmp_x) {
                $src_h = round($height / $cmp_y * $cmp_x);
                $src_y = round(($height - ($height / $cmp_y * $cmp_x)) / 2);
            }

            // positional cropping!
            if ($this->options["DEFAULT_ALIGN"]) {
                if (strpos($this->options["DEFAULT_ALIGN"], 't') !== false) {
                    $src_y = 0;
                }
                if (strpos($this->options["DEFAULT_ALIGN"], 'b') !== false) {
                    $src_y = $height - $src_h;
                }
                if (strpos($this->options["DEFAULT_ALIGN"], 'l') !== false) {
                    $src_x = 0;
                }
                if (strpos($this->options["DEFAULT_ALIGN"], 'r') !== false) {
                    $src_x = $width - $src_w;
                }
            }

            imagecopyresampled($canvas, $image, $origin_x, $origin_y, $src_x, $src_y, $new_width, $new_height, $src_w, $src_h);
        } else {

            // copy and resize part of an image with resampling
            imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        }

        // options["DEFAULT_S"] image
        if ($this->options["DEFAULT_S"] && function_exists('imageconvolution')) {
            $sharpenMatrix = array(
                array(-1, -1, -1),
                array(-1, 16, -1),
                array(-1, -1, -1),
            );
            $divisor = 8;
            $offset = 0;
            imageconvolution($canvas, $sharpenMatrix, $divisor, $offset);
        }

        imagejpeg($canvas, $this->options["LOCAL_NEW_IMAGE"], $this->options["DEFAULT_Q"]);
        imagedestroy($canvas);
        imagedestroy($image);
        return true;
    }

}
