<?php
function addCenterText($img, $font, $size, $offsetx, $y, $text) {
    $box = imagettfbbox($size, 0, dirname(__FILE__) . "/font/{$font}.ttf", $text);

    $text_width = abs($box[2]) - abs($box[0]);
    $image_width = imagesx($img);
    $x = ($image_width - $text_width) / 2;

    // $image_height = imagesy($img) / 2; // Descomentar si quieres centrarlo verticalmente
    // $text_height = abs($box[5]) - abs($box[3]); // Descomentar si quieres centrarlo verticalmente
    // $y = ($image_height + $text_height) / 2; // Descomentar si quieres centrarlo verticalmente

    imagettftext($img, $size, 0, $x + $offsetx, $y, imagecolorallocate($img, 255, 255, 255), dirname(__FILE__) . "/font/{$font}.ttf", $text);
}

function searchPower($power, $powers) {
    foreach ($powers as $p) {
        if (strtolower(trim($p["type"])) == strtolower(trim($power))) {
            return true;
        }
    }
    return false;
}

function getProperties($bbcode) {
    // All
    preg_match_all("/(.*?)\[b](.*?)\[\/b]/iu", $bbcode, $matches, PREG_SET_ORDER);
    // Set
    preg_match_all("/^(\D*)/iu", $matches[0][1], $set, PREG_SET_ORDER);
    // Code
    preg_match_all("/^\D*(\d*.)\D*/iu", $matches[0][1], $code, PREG_SET_ORDER);
    // Range
    preg_match_all("/^(\d*)/iu", $matches[2][2], $range, PREG_SET_ORDER);
    // Icons
    preg_match_all("/\[icon](.*?)\[\/icon]/iu", $bbcode, $icons, PREG_SET_ORDER);

    $prop["movement"] = trim($icons[0][1]);
    $prop["attack"]   = trim($icons[1][1]);
    $prop["defense"]  = trim($icons[2][1]);
    $prop["damage"]   = trim($icons[3][1]);
    $prop["set"]      = trim($set[0][1]);
    $prop["code"]     = trim($code[0][1]);
    $prop["name"]     = trim($matches[0][2]);
    $prop["team"]     = trim($matches[1][2]);
    $prop["range"]    = trim($range[0][1]);
    $prop["targets"]  = substr_count($matches[2][2], "bolt");
    $prop["points"]   = trim($matches[3][2]);
    $prop["keywords"] = explode(", ", $matches[4][2]);

    // Powers
    preg_match_all("/\[b]\((.*?)\)(.*?)\[\/b](.*?)$/ium", $bbcode, $powers, PREG_SET_ORDER);
    foreach ($powers as $power) {
        // Eliminamos los : finales en el nombre del poder
        $name = trim($power[2]);
        if (strrev($name)[0] == ":") {
            $name = substr($name, 0, -1);
        }

        $p["type"] = strtolower(trim($power[1]));
        $p["name"] = trim($name);
        $p["desc"] = trim($power[3]);
        $prop["powers"][] = $p;
    }

    // Debug
    //header("Content-type: text/plain");
    //print_r($prop);
    //exit();

    return $prop;
}
