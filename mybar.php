<?php
require "include/bittorrent.php";
dbconn();
global $userbar_class;
function lastModified($modifiedTime, $notModifiedExit = true)
{
    $ret = false;
    if (isset ($_SERVER ['HTTP_IF_MODIFIED_SINCE']) && $modifiedTime == $_SERVER ['HTTP_IF_MODIFIED_SINCE']) {
        if ($notModifiedExit) {
            header("HTTP/1.0 304 Not Modified");
            exit ();
        } else {
            $ret = true;
        }
    }
    header("Last-Modified: $modifiedTime");
    return $ret;
}

lastModified($Cache->get_value('userbar_md_' . $_SERVER ['REQUEST_URI']));

if (!$my_img = unserialize($Cache->get_value('userbar_' . $_SERVER ['REQUEST_URI']))) {
    $userid = 0 + $_GET ["userid"];
    $bgpic = 0 + $_GET ["bgpic"];
    if (!$userid) {
        die ();
    }

    if (!preg_match("/userid=([0-9]+)\.(png|jpeg|jpg|gif)$/i", $_SERVER ['REQUEST_URI'], $matches)) {
        die ();
    }

    switch (strtolower($matches [2])) {
        case 'gif' :
            $format = IMAGETYPE_GIF;
            break;
        case 'jpeg' :
        case 'jpg' :
            $format = IMAGETYPE_JPEG;
            break;
        default :
            $format = IMAGETYPE_PNG;
    }

    $res = sql_query("SELECT username, uploaded, downloaded, class, privacy FROM users WHERE id=" . sqlesc($userid) . " LIMIT 1");
    $row = mysql_fetch_array($res);
    if (!$row) {
        die ();
    }

    $fsaa = 4;
    $my_img = imagecreatefrompng("pic/userbar/" . $bgpic . ".png");
    imagealphablending($my_img, true);
    // imageantialias($my_img, true);
    // putenv('GDFONTPATH=' . realpath('pic/userbar/'));
    $im = imagecreatetruecolor(450 * $fsaa, 75 * $fsaa);
    imagealphablending($im, true);
    $trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
    imagefill($im, 0, 0, $trans_colour);

    if ($row ['privacy'] == 'strong') {
        $color = imagecolorallocatealpha($my_img, 77, 60, 42, 0);
        $font = 'pic/userbar/方正宋刻本秀楷简.TTF';
        $name_box = imagettfbbox(20, 0, $font, '~这是个秘密~');
        $namex = ceil((450 - $name_box[2]) / 2);
        imagettftext($im, 20 * $fsaa, 0, $namex * $fsaa, 37 * $fsaa, $color, $font, '~这是个秘密~');
    } elseif ($row ['class'] < $userbar_class) {
        $color = imagecolorallocatealpha($my_img, 77, 60, 42, 0);
        $font = 'pic/userbar/方正宋刻本秀楷简.TTF';
        $name_box = imagettfbbox(20, 0, $font, '~等级太低啦~');
        $namex = ceil((450 - $name_box[2]) / 2);
        imagettftext($im, 20 * $fsaa, 0, $namex * $fsaa, 37 * $fsaa, $color, $font, '~等级太低啦~');
    } else {
        $username = $row ['username'];
        $uploaded = mksize($row ['uploaded']);
        $downloaded = mksize($row ['downloaded']);
        $font = 'pic/userbar/方正宋刻本秀楷简.TTF';

        if (!$_GET ['noname']) {
            if (isset ($_GET ['namered']) && $_GET ['namered'] >= 0 && $_GET ['namered'] <= 255)
                $namered = 0 + $_GET ['namered'];
            else
                $namered = 77;
            if (isset ($_GET ['namegreen']) && $_GET ['namegreen'] >= 0 && $_GET ['namegreen'] <= 255)
                $namegreen = 0 + $_GET ['namegreen'];
            else
                $namegreen = 60;
            if (isset ($_GET ['nameblue']) && $_GET ['nameblue'] >= 0 && $_GET ['nameblue'] <= 255)
                $nameblue = 0 + $_GET ['nameblue'];
            else
                $nameblue = 42;
            if (isset ($_GET ['namesize']) && $_GET ['namesize'] >= 1 && $_GET ['namesize'] <= 5)
                $namesize = 0 + $_GET ['namesize'];
            else
                $namesize = 20;
            if (isset ($_GET ['namex']) && $_GET ['namex'] >= 0 && $_GET ['namex'] <= 450)
                $namex = 0 + $_GET ['namex'];
            else {
                $name_box = imagettfbbox($namesize, 0, $font, $username);
                $namex = ceil((450 - $name_box[2]) / 2);
            }
            if (isset ($_GET ['namey']) && $_GET ['namey'] >= 0 && $_GET ['namey'] <= 75)
                $namey = 0 + $_GET ['namey'];
            else
                $namey = 37;
            $name_colour = imagecolorallocate($my_img, $namered, $namegreen, $nameblue);
            // imagestring($my_img, $namesize, $namex, $namey, $username,
            // $name_colour);
            imagefttext($im, $namesize * $fsaa, 0, $namex * $fsaa, $namey * $fsaa, $name_colour, $font, $username);
        }

        if (!$_GET ['noup']) {
            if (isset ($_GET ['upred']) && $_GET ['upred'] >= 0 && $_GET ['upred'] <= 255)
                $upred = 0 + $_GET ['upred'];
            else
                $upred = 77;
            if (isset ($_GET ['upgreen']) && $_GET ['upgreen'] >= 0 && $_GET ['upgreen'] <= 255)
                $upgreen = 0 + $_GET ['upgreen'];
            else
                $upgreen = 60;
            if (isset ($_GET ['upblue']) && $_GET ['upblue'] >= 0 && $_GET ['upblue'] <= 255)
                $upblue = 0 + $_GET ['upblue'];
            else
                $upblue = 42;
            if (isset ($_GET ['upsize']) && $_GET ['upsize'] >= 1 && $_GET ['upsize'] <= 5)
                $upsize = 0 + $_GET ['upsize'];
            else
                $upsize = 12;
            if (isset ($_GET ['upx']) && $_GET ['upx'] >= 0 && $_GET ['upx'] <= 450)
                $upx = 0 + $_GET ['upx'];
            else
                $upx = 137;
            if (isset ($_GET ['upy']) && $_GET ['upy'] >= 0 && $_GET ['upy'] <= 75)
                $upy = 0 + $_GET ['upy'];
            else
                $upy = 61.5;
            $up_colour = imagecolorallocate($my_img, $upred, $upgreen, $upblue);
            // imagestring($my_img, $upsize, $upx, $upy, $uploaded, $up_colour);
            imagettftext($im, $upsize * $fsaa, 0, $upx * $fsaa, $upy * $fsaa, $up_colour, $font, $uploaded);
        }

        if (!$_GET ['nodown']) {
            if (isset ($_GET ['downred']) && $_GET ['downred'] >= 0 && $_GET ['downred'] <= 255)
                $downred = 0 + $_GET ['downred'];
            else
                $downred = 77;
            if (isset ($_GET ['downgreen']) && $_GET ['downgreen'] >= 0 && $_GET ['downgreen'] <= 255)
                $downgreen = 0 + $_GET ['downgreen'];
            else
                $downgreen = 60;
            if (isset ($_GET ['downblue']) && $_GET ['downblue'] >= 0 && $_GET ['downblue'] <= 255)
                $downblue = 0 + $_GET ['downblue'];
            else
                $downblue = 42;
            if (isset ($_GET ['downsize']) && $_GET ['downsize'] >= 1 && $_GET ['downsize'] <= 5)
                $downsize = 0 + $_GET ['downsize'];
            else
                $downsize = 12;
            if (isset ($_GET ['downx']) && $_GET ['downx'] >= 0 && $_GET ['downx'] <= 450)
                $downx = 0 + $_GET ['downx'];
            else
                $downx = 250;
            if (isset ($_GET ['downy']) && $_GET ['downy'] >= 0 && $_GET ['downy'] <= 75)
                $downy = 0 + $_GET ['downy'];
            else
                $downy = 61.5;
            $down_colour = imagecolorallocate($my_img, $downred, $downgreen, $downblue);
            // imagestring($my_img, $downsize, $downx, $downy, $downloaded,
            // $down_colour);
            imagettftext($im, $downsize * $fsaa, 0, $downx * $fsaa, $downy * $fsaa, $down_colour, $font, $downloaded);
        }
    }
    imagecopyresampled($my_img, $im, 0, 0, 0, 0, 450, 75, 450 * 4, 75 * 4);
    imagedestroy($im);
    imagesavealpha($my_img, true);
    $Cache->cache_value('userbar_' . $_SERVER ['REQUEST_URI'], serialize($my_img), 300);
    $modifiedTime = date('r');
    $Cache->cache_value('userbar_md_' . $_SERVER ['REQUEST_URI'], $modifiedTime, 300);
    header("Last-Modified: $modifiedTime");
}
switch ($format) {
    case IMAGETYPE_GIF :
        header("Content-type: image/gif");
        imagegif($my_img);
        break;
    case IMAGETYPE_JPEG :
        header("Content-type: image/jpeg");
        imagejpeg($my_img);
        break;
    case IMAGETYPE_PNG :
        header("Content-type: image/png");
        imagepng($my_img);
        break;
}
imagedestroy($my_img);