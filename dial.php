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

// Imagen
$img = imagecreatefromjpeg(dirname(__FILE__) . "/dial/dial_template.jpg");
imagealphablending($img, true);
imagesavealpha($img, true);

// Clicks
$count  = 1;
$clicks = getClicks($bbcode);
foreach ($clicks as $click) {
    addClick($click[0], $click[1], $click[2], $click[3]);

    // Para los diales de 24 clicks, solo obtendremos los de 12
    if ($count++ >= 48) break;
}

// Propiedades
$props = getProperties($bbcode);

// Nombre de la figura
//addCenterName($props["name"]);
textInsideArc($img, 521, 175, 160, -15, 0, imagecolorallocate($img, 255, 255, 255), strtoupper($props["name"]), dirname(__FILE__) . '/font/oss.ttf', 12, 3);
// Puntos
textInsideArc($img, 521, 175, 160, -230, 0, imagecolorallocate($img, 255, 255, 255), strtoupper($props["points"]), dirname(__FILE__) . '/font/oss.ttf', 14, 2);
// Codigo
textInsideArc($img, 521, 175, 160, -315, 0, imagecolorallocate($img, 255, 255, 255), "#" . $props["code"], dirname(__FILE__) . '/font/oss.ttf', 12, 2);
// Rango
imagettftext($img, 14, 38, 569, 261, imagecolorallocate($img, 255, 255, 255), dirname(__FILE__) . '/font/osb.ttf', $props["range"]);
drawTargets($props['targets']);

// Iconos
$icon = imagecreatefrompng(dirname(__FILE__) . "/icon/{$props['movement']}.png");
imagecopy($img, $icon, 466, 238, 0, 0, 30, 30);
$icon = imagecreatefrompng(dirname(__FILE__) . "/icon/{$props['attack']}.png");
imagecopy($img, $icon, 466, 271, 0, 0, 30, 30);
$icon = imagecreatefrompng(dirname(__FILE__) . "/icon/{$props['defense']}.png");
imagecopy($img, $icon, 466, 305, 0, 0, 30, 30);
$icon = imagecreatefrompng(dirname(__FILE__) . "/icon/{$props['damage']}.png");
imagecopy($img, $icon, 587, 285, 0, 0, 30, 30);

if (searchPower("improved", $props["powers"])) {
    $icon = imagecreatefrompng(dirname(__FILE__) . "/icon/enhancement.png");
    imagecopy($img, $icon, 384, 245, 0, 0, 30, 30);
}

if (searchPower("epic", $props["powers"])) {
    $icon = imagecreatefrompng(dirname(__FILE__) . "/icon/epic.png");
    imagecopy($img, $icon, 410, 235, 0, 0, 30, 30);
}

if (searchPower("special", $props["powers"])) {
    $icon = imagecreatefrompng(dirname(__FILE__) . "/icon/trait.png");
    imagecopy($img, $icon, 410, 275, 0, 0, 30, 30);
}

// Team
$team = strtolower(preg_replace("/[^A-Z]/iu", '', $props["team"]));
if (file_exists(dirname(__FILE__) . "/ta/{$team}.png")) {
    $icon = imagecreatefrompng(dirname(__FILE__) . "/ta/{$team}.png");
    $icon = imagerotate($icon, 182, imagecolorallocatealpha($icon, 0, 0, 0, 127));
    imagecopyresized($img, $icon, 510, 10, 0, 0, 40, 40, 20, 20);
}

// Set
$set = strtolower(preg_replace("/[^A-Z]/iu", '', $props["set"]));
$filename = null;
if (file_exists(dirname(__FILE__) . "/set/{$set}.png")) {
    $filename = dirname(__FILE__) . "/set/{$set}.png";
}
elseif (file_exists(dirname(__FILE__) . "/set/{$set}.gif")) {
    $filename = dirname(__FILE__) . "/set/{$set}.gif";
}

if ($filename) {
    $icon = imagecreatefrompng($filename);
    $icon = imagerotate($icon, 224, imagecolorallocatealpha($icon, 0, 0, 0, 127));
    imagecopy($img, $icon, 392, 48, 0, 0, 44, 45);
}

// Set filename
$imagename = "heroclix_dial_" . $props["set"] . $props["code"] . ".jpg";
header('Content-Disposition: inline; filename=' . $imagename);
header('Connection: Keep-Alive');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

// Send Image to Browser
imagejpeg($img, null, 100);

// Clear Memory
imagedestroy($img);

function addClick(int $click, int $slot, string $power, string $value)
{
    global $img;

    // Coordenadas de clicks y slots
    $clicks = array(
        array(array(159, 240, 0, 0, 30, 30,   0),array(159, 275, 0, 0, 30, 30,   0),array(159, 309, 0, 0, 30, 30,   0),array(194, 302, 0, 0, 36, 36,  12)),
        array(array(196, 225, 0, 0, 39, 40,  30),array(214, 255, 0, 0, 39, 40,  30),array(230, 284, 0, 0, 39, 40,  30),array(258, 262, 0, 0, 39, 41,  42)),
        array(array(226, 195, 0, 0, 39, 39,  60),array(256, 212, 0, 0, 39, 39,  60),array(285, 229, 0, 0, 39, 40,  60),array(300, 198, 0, 0, 36, 36,  72)),
        array(array(240, 159, 0, 0, 30, 30,  90),array(275, 159, 0, 0, 30, 30,  90),array(309, 159, 0, 0, 30, 30,  90),array(303, 120, 0, 0, 34, 35, 102)),
        array(array(226, 112, 0, 0, 39, 40, 120),array(256,  96, 0, 0, 39, 40, 120),array(285,  79, 0, 0, 39, 40, 120),array(264,  50, 0, 0, 41, 42, 132)),
        array(array(196,  82, 0, 0, 39, 40, 150),array(214,  53, 0, 0, 39, 40, 150),array(230,  23, 0, 0, 39, 40, 150),array(198,  11, 0, 0, 36, 37, 162)),
        array(array(159,  77, 0, 0, 30, 30, 181),array(159,  43, 0, 0, 30, 30, 181),array(159,   9, 0, 0, 30, 30, 181),array(120,  10, 0, 0, 34, 35, 192)),
        array(array(113,  82, 0, 0, 39, 40, 210),array( 96,  53, 0, 0, 39, 40, 210),array( 80,  23, 0, 0, 39, 40, 210),array( 50,  43, 0, 0, 41, 42, 222)),
        array(array( 83, 112, 0, 0, 39, 39, 240),array( 54,  96, 0, 0, 39, 40, 240),array( 23,  80, 0, 0, 39, 40, 240),array( 10, 115, 0, 0, 36, 37, 252)),
        array(array( 78, 159, 0, 0, 30, 30, 270),array( 44, 159, 0, 0, 30, 30, 270),array(  9, 159, 0, 0, 30, 30, 270),array( 10, 194, 0, 0, 34, 35, 282)),
        array(array( 83, 195, 0, 0, 39, 39, 300),array( 54, 210, 0, 0, 39, 39, 300),array( 23, 226, 0, 0, 39, 39, 300),array( 42, 254, 0, 0, 41, 41, 312)),
        array(array(113, 225, 0, 0, 39, 39, 330),array( 96, 254, 0, 0, 39, 39, 330),array( 80, 284, 0, 0, 39, 39, 330),array(115, 301, 0, 0, 36, 36, 342))
    );

    // Merge de imagenes
    imagecopy($img, createClick($power, $value, $clicks[$click - 1][$slot - 1][6]),
        $clicks[$click - 1][$slot - 1][0],
        $clicks[$click - 1][$slot - 1][1],
        $clicks[$click - 1][$slot - 1][2],
        $clicks[$click - 1][$slot - 1][3],
        $clicks[$click - 1][$slot - 1][4],
        $clicks[$click - 1][$slot - 1][5]
    );
}


function createClick(string $power, string $value, int $angle)
{
    $powers = array(
        "ko"       => array(array(255, 255, 255), array(255, 0, 0)),
        "special"  => array(array(255, 255, 255), array(0, 0, 0)),
        "none"     => array(array(255, 255, 255), array(0, 0, 0)),
        "red"      => array(array(255, 0, 0),     array(0, 0, 0)),
        "orange"   => array(array(255, 128, 0),   array(0, 0, 0)),
        "yellow"   => array(array(255, 255, 0),   array(0, 0, 0)),
        "lime"     => array(array(190, 200, 15),  array(0, 0, 0)),
        "green"    => array(array(0, 255, 0),     array(0, 0, 0)),
        "blue"     => array(array(0, 255, 255),   array(0, 0, 0)),
        "darkblue" => array(array(0, 0, 204),     array(255, 255, 255)),
        "purple"   => array(array(180, 0, 180),   array(255, 255, 255)),
        "pink"     => array(array(205, 170, 170), array(0, 0, 0)),
        "brown"    => array(array(165, 106, 23),  array(255, 255, 255)),
        "black"    => array(array(0, 0, 0),       array(255, 255, 255)),
        "gray"     => array(array(136, 136, 136), array(255, 255, 255))
    );

    // Cuadrado
    $box = imagecreate(30, 30);

    // Relleno de cuadrado
    if ($power == "special") {
        imagefill($box, 0, 0, imagecolorallocatealpha($box, 0, 0, 0, 0));
        imagefilledrectangle($box, 2, 2, 27, 27, imagecolorallocate($box, $powers[$power][0][0], $powers[$power][0][1], $powers[$power][0][2]));
    }
    else {
        imagefilledrectangle($box, -1, -1, 31, 31, imagecolorallocate($box, $powers[$power][0][0], $powers[$power][0][1], $powers[$power][0][2]));
    }

    // Texto
    $x = 2;
    if (is_numeric($value) && $value <= 9) {
        $x = 9;
    }

    // Color de la fuente
    imagettftext($box, 16, 0, $x, 23, imagecolorallocatealpha($box, $powers[$power][1][0], $powers[$power][1][1], $powers[$power][1][2], 0), dirname(__FILE__) . '/font/oss.ttf', (is_numeric($value) ? $value : strtoupper($value)));

    // Rotacion de cuadrado
    return imagerotate($box, $angle, imagecolorallocatealpha($box, 0, 0, 0, 127));
}

function drawTargets($num) {
    global $img;

    $num = intval($num) > 4 ? 4 : $num;
    $box = imagecreatefrompng(dirname(__FILE__) . "/icon/targets{$num}.png");
    imagealphablending($box, true);
    imagesavealpha($box, true);

    $final = imagerotate($box, 36, imagecolorallocatealpha($box, 0, 0, 0, 127));
    imagecopy($img, $final, 578, 208, 0, 0, 41, 41);
}

function getClicks($bbcode) {
    $pattern = "/\[click\]\[slot=(.*?)\](.*?)\[\/slot\]\[slot=(.*?)\](.*?)\[\/slot\]\[slot=(.*?)\](.*?)\[\/slot\]\[slot=(.*?)\](.*?)\[\/slot\]\[\/click\]/imu";
    preg_match_all($pattern, $bbcode, $matches, PREG_SET_ORDER);

    $clicks = array();
    $numclick = 1;
    foreach ($matches as &$match) {
        unset($match[0]);
        $clicks[] = array($numclick, 1, strtolower($match[1]), $match[2]);
        $clicks[] = array($numclick, 2, strtolower($match[3]), $match[4]);
        $clicks[] = array($numclick, 3, strtolower($match[5]), $match[6]);
        $clicks[] = array($numclick, 4, strtolower($match[7]), $match[8]);
        $numclick++;
    }

    return $clicks;
}

function textWidth($txt, $font, $size)
{
    $bbox = imagettfbbox($size,0,$font,$txt);
    $w = abs($bbox[4]-$bbox[0]);
    return $w;
}

function textOnArc($im,$cx,$cy,$r,$s,$e,$txtcol,$txt,$font,$size, $pad=0)
{
    $tlen = strlen($txt);
    $arccentre = ($e + $s)/2;
    $total_width = textWidth($txt, $font, $size) - ($tlen-1)*$pad;
    $textangle = rad2deg($total_width / $r);
    $s = $arccentre - $textangle/2;
    $e = $arccentre + $textangle/2;
    for ($i=0, $theta = deg2rad($s); $i < $tlen; $i++)
    {
        $ch = $txt[$i];
        $tx = $cx + $r*cos($theta);
        $ty = $cy + $r*sin($theta);
        $dtheta = (textWidth($ch,$font,$size))/$r;
        $angle = rad2deg(M_PI*3/2 - ($dtheta/2 + $theta) );
        imagettftext($im, $size, $angle, $tx, $ty, $txtcol, $font, $ch);
        $theta += $dtheta;
    }
}

function textInsideArc($im,$cx,$cy,$r,$s,$e,$txtcol,$txt,$font,$size, $pad=0)
{
    $tlen = strlen($txt);
    $arccentre = ($e + $s)/2;
    $total_width = textWidth($txt, $font, $size) + ($tlen-1)*$pad;
    $textangle = rad2deg($total_width / $r);
    $e = $arccentre + $textangle/2;
    for ($i=0, $theta = deg2rad($e); $i < $tlen; $i++)
    {
        $ch = $txt[$i];
        $tx = $cx + $r*cos($theta);
        $ty = $cy + $r*sin($theta);
        $dtheta = (textWidth($ch,$font,$size)+$pad)/$r;
        $angle = rad2deg(M_PI/2 - ($theta - $dtheta/2));
        imagettftext($im, $size, $angle, $tx, $ty, $txtcol, $font, $ch);
        $theta -= $dtheta;
    }
}