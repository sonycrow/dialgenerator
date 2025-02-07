<?php

include("utils.php");

error_reporting(0);

ob_start();
header("Content-type: image/jpeg");
//header("Content-type: text/plain");

// BBcode
$bbcode = !empty(trim($_REQUEST["bbcode"])) ? trim($_REQUEST["bbcode"]) : trim(base64_decode(trim($_REQUEST["token"])));

if (empty($bbcode)) {
    header("Content-type: text/plain");
    die("No BBCODE detected!");
}

// Propiedades
$props = getProperties($bbcode);

// Imagen
$img = imagecreatefromjpeg(dirname(__FILE__) . "/card/card_template2.jpg");
imagealphablending($img, true);
imagesavealpha($img, true);

// Rareza
$rareza = imagecreatefrompng(dirname(__FILE__) . "/card/common.png");
imagecopy($img, $rareza, 430, 26, 0, 0, 281,278);

// Nombre
addCenterText($img, "osbi", 20, -150, 60, strtoupper($props["name"]));
addCenterText($img, "oss", 12, -150, 90, implode(", ", $props["keywords"]));

// Número
addCenterText($img, "osr", 20, 280, 40, "#" . $props["code"]);

// Set
$set = strtolower(preg_replace("/[^A-Z]/iu", '', $props["set"]));
$filename = null;
if (file_exists(dirname(__FILE__) . "/set/{$set}.png")) {
    $filename = dirname(__FILE__) . "/set/{$set}.png";
    $icon = imagecreatefrompng($filename);
}
elseif (file_exists(dirname(__FILE__) . "/set/{$set}.gif")) {
    $filename = dirname(__FILE__) . "/set/{$set}.gif";
    $icon = imagecreatefromgif($filename);
}

if ($filename) {
    $back = imagecreatefrompng(dirname(__FILE__) . "/set/background.png");
    imagecopy($img, $back, 680, 46, 0, 0, 40, 40);
    imagecopy($img, $icon, 684, 50, 0, 0, 32, 32);
}

// Team
$team = strtolower(preg_replace("/[^A-Z]/iu", '', $props["team"]));
if (file_exists(dirname(__FILE__) . "/ta/{$team}.png")) {
    $icon = imagecreatefrompng(dirname(__FILE__) . "/ta/{$team}.png");
    imagecopyresized($img, $icon, 355, 176, 0, 0, 40, 40, 20, 20);
}

// Points
imagettftext($img, 20, 0, 143, 222, imagecolorallocate($img, 255, 255, 255), dirname(__FILE__) . "/font/oss.ttf", $props["points"]);

// Tabla de poderes
$height = 325;
foreach ($props["powers"] as $power) {
    switch ($power["type"]) {
        case "improved":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/improved.png");
            break;
        case "epic":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/epic.png");
            break;
        case "speed":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/{$props['movement']}.png");
            break;
        case "attack":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/{$props['attack']}.png");
            break;
        case "defense":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/{$props['defense']}.png");
            break;
        case "damage":
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/{$props['damage']}.png");
            break;
        default:
            $imgpower = imagecreatefrompng(dirname(__FILE__) . "/power/trait.png");
    }
    imagecopy($img, $imgpower, 15, $height - 20, 0, 0, 64, 64);

    $heightPower  = write_multiline_text($img, 16, imagecolorallocate($img, 0, 0, 0), "ose", strtoupper($power["name"]), 90, $height, 630);
    $heightPower += write_multiline_text($img, 14, imagecolorallocate($img, 0, 0, 0), "oss", $power["desc"], 90, $height + $heightPower, 630);

    if ($heightPower < 64) {
        $heightPower = 64;
    }

    $height += $heightPower + 20;
}

// Set filename
$imagename = "heroclix_card_" . $props["set"] . $props["code"] . ".jpg";
header('Content-Disposition: inline; filename=' . $imagename);
header('Connection: Keep-Alive');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

// Send Image to Browser
imagejpeg($img, null, 100);

// Clear Memory
imagedestroy($img);
imagedestroy($rareza);


function write_multiline_text($image, $font_size, $color, $font, $text, $start_x, $start_y, $max_width)
{
    //split the string
    //build new string word for word
    //check everytime you add a word if string still fits
    //otherwise, remove last word, post current string and start fresh on a new line
    $words = explode(" ", $text);
    $string = "";
    $tmp_string = "";
    $total_height = 0;

    for ($i = 0; $i < count($words); $i++) {
        $tmp_string .= $words[$i] . " ";

        //check size of string
        $dim = imagettfbbox($font_size, 0, dirname(__FILE__) . "/font/{$font}.ttf", $tmp_string);

        if ($dim[4] < $max_width) {
            $string = $tmp_string;
        } else {
            $i--;
            $tmp_string = "";
            imagettftext($image, $font_size, 0, $start_x, $start_y, $color, dirname(__FILE__) . "/font/{$font}.ttf", $string);

            $string = "";
            $start_y += $font_size + 10; //change this to adjust line-height. Additionally you could use the information from the "dim" array to automatically figure out how much you have to "move down"
            $total_height += $font_size + 10;
        }
    }

    imagettftext($image, $font_size, 0, $start_x, $start_y, $color, dirname(__FILE__) . "/font/{$font}.ttf", $string); //"draws" the rest of the string
    $total_height += $font_size + 10;

    return $total_height;
}
