<?php
/*********************************************
 * Fitting text to arc
 *
 * TextOnArc
 * Author   :  Barand August2007
 **********************************************/
$im = imagecreate(400,400);

$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
$grey  = imagecolorallocate($im, 0xCC, 0xCC, 0xCC);
$txtcol = imagecolorallocate($im, 0xFF, 0x00, 0x00);

$r = 150;
$cx = 200;
$cy = 200;
$txt1 = 'Text on an Arc';
$txt2 = 'by Barand';
$font = dirname(__FILE__) . '/font/osr.ttf';
#$font = 'bauhausm.ttf';
$size = 48;

imagearc($im,$cx,$cy,$r*2,$r*2,$s,$e,$grey);
$pad = 2;                      // extra char spacing for text
$s = 180;
$e = 360;
textOnArc($im,$cx,$cy,$r,$s,$e,$txtcol,$txt1,$font,$size,$pad);
$pad = 6;                      // extra char spacing for text
$s = 0;
$e = 180;
textInsideArc($im,$cx,$cy,$r,$s,$e,$txtcol,$txt2,$font,$size,$pad);

header("content-type: image/png");
imagepng($im);
imagedestroy($im);

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
    $s = $arccentre - $textangle/2;
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
