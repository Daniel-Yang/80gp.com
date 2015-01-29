<?php
function make_my_image($old_img,$name){

    $r = 0xa4;
    $g = 0x6b;
    $b = 0x3d;
    $img = imagecreatefromjpeg($old_img);
    $adiNeue = __DIR__.'/fonts/兰亭特黑简.TTF';
    $txtColor = imagecolorallocate($img, $r,$g,$b);

    $txtSize = 80;
    $txtBox = imagettfbbox($txtSize,0,$adiNeue,$name);
    $txtW = $txtBox[2] - $txtBox[0];
    $txtH = $txtBox[3] - $txtBox[7];

    $txtIM = imagecreatetruecolor($txtW + 10,$txtH+3);
    $tranColor = imageColorAllocate($txtIM,255,255,255);
    imagecolortransparent($txtIM,$tranColor);

    //imageantialias($txtIM,false);

    imagettftext($txtIM,$txtSize,0,
        -$txtBox[0],-$txtBox[7],
        $txtColor,$adiNeue,$name);

    for($x = 0; $x < imagesx($txtIM); ++$x)
    {
        for($y = 0; $y < imagesy($txtIM); ++$y)
        {
            $index = imagecolorat($txtIM, $x, $y);
            $rgb = imagecolorsforindex($txtIM,$index);
            //$color = imagecolorallocatea($txtIM, 255 - $rgb['red'], 255 - $rgb['green'], 255 - $rgb['blue']);

            if($rgb['red'] != $r || $rgb['green'] != $g || $rgb['blue'] != $b)
                imagesetpixel($txtIM, $x, $y, $tranColor);
//            else
//                imagesetpixel($txtIM, $x, $y, $color);
        }
    }

    //imagefilter($txtIM,IMG_FILTER_MEAN_REMOVAL);
    imagefilter($txtIM,IMG_FILTER_SMOOTH,2);

    imagecopymerge($img,$txtIM,// $img,
        (imagesx($img) - $txtW) / 2 -10,
        100,
        0,
        0,
        $txtW+10,
        $txtH+3,
        35
    );

    $fid = uniqid();
    $file = __DIR__."/n/".$fid.'.jpg';

    //header("Content-Type:image/jpeg");
    imagejpeg($img,$file);

    imagedestroy($txtIM);
    imagedestroy($img);
    return $fid;
}

function make_my_image2($old_img,$name){

    $r = 0xff;
    $g = 0xff;
    $b = 0xff;
    $img = imagecreatefromjpeg($old_img);
    $adiNeue = __DIR__.'/fonts/兰亭特黑简.TTF';
    $txtColor = imagecolorallocate($img, $r,$g,$b);

    $txtSize = 80;
    $txtBox = imagettfbbox($txtSize,0,$adiNeue,$name);
    $txtW = $txtBox[2] - $txtBox[0];
    $txtH = $txtBox[3] - $txtBox[7];

    $txtIM = imagecreatetruecolor($txtW + 10,$txtH+3);
    $tranColor = imageColorAllocate($txtIM,254,254,254);
    imagecolortransparent($txtIM,$tranColor);

    //imageantialias($txtIM,false);

    imagettftext($txtIM,$txtSize,0,
        -$txtBox[0],-$txtBox[7],
        $txtColor,$adiNeue,$name);

    for($x = 0; $x < imagesx($txtIM); ++$x)
    {
        for($y = 0; $y < imagesy($txtIM); ++$y)
        {
            $index = imagecolorat($txtIM, $x, $y);
            $rgb = imagecolorsforindex($txtIM,$index);
            //$color = imagecolorallocatea($txtIM, 255 - $rgb['red'], 255 - $rgb['green'], 255 - $rgb['blue']);

            if($rgb['red'] != $r || $rgb['green'] != $g || $rgb['blue'] != $b)
                imagesetpixel($txtIM, $x, $y, $tranColor);
//            else
//                imagesetpixel($txtIM, $x, $y, $color);
        }
    }

    //imagefilter($txtIM,IMG_FILTER_MEAN_REMOVAL);
    imagefilter($txtIM,IMG_FILTER_SMOOTH,2);

    imagecopymerge($img,$txtIM,// $img,
        (imagesx($img) - $txtW) / 2 -10,
        100,
        0,
        0,
        $txtW+10,
        $txtH+3,
        100
    );

    $fid = uniqid();
    $file = __DIR__."/n/".$fid.'.jpg';

    //header("Content-Type:image/jpeg");
    imagejpeg($img,$file);

    imagedestroy($txtIM);
    imagedestroy($img);
    return $fid;
}


//make_my_image($_GET['s']);