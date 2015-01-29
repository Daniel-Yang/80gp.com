<?php

//define your token
include_once "inc/config.php";
include_once "inc/funcs.php";
include_once "image_deal.php";

require_once "WechatSDK.php";

$wechatObj = new WechatSDK();

$wechatObj->valid();
//$wechatObj->uploadImage(__DIR__ . "/kevin.jpg");

//echo "<img src=\"".make_my_image("Test")."\"/>";
//exit;

if($wechatObj->checkSignature()){

    try{
        $wechatObj->responseMsg();
    }catch (Exception $ex){
        file_put_contents("log/err.txt", $ex->getMessage());
    }
}
else
{
    file_put_contents("log/warn.txt", "not valid!");
}
