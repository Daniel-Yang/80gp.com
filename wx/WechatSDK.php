<?php
define('WEBSITE','http://adidasres.youku.com/2014/adidasneolabel/wx/');
class WechatSDK
{
    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;

            $touser = strval($fromUsername);

            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();

            session_id($fromUsername);
            session_start();

            $imgTpl =  "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                        </Image>
						</xml>";

            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";

            if($postObj->MsgType == "image"){

                $tmpimg = $this->downloadImage($postObj->MediaId);


                if(!empty($_SESSION["U_NAME"])){
                    $record_name = $_SESSION["U_NAME"];
                    $record_name = strtoupper($record_name);

                    $img = make_my_image2($tmpimg,$record_name);

                    $msgType = "text";
                    $url = WEBSITE."neo.php?t=1&f=$img";
                    $contentStr = "你的专属海报已经出炉！还不赶快点击 $url 获取！";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;

                }
            }
            else
            {
                $prefix_text = "NEO";

                $index = stripos($keyword,$prefix_text);
                if($index !== FALSE && $index == 0)
                {
                    $record_name = substr($keyword, strlen($prefix_text));
                    $record_name = strtoupper($record_name);

                    srand(time());
                    $ran = rand(1,3);

                    $img1 = make_my_image(__DIR__.'/images/'.$ran.'_1.jpg',$record_name);

                    //$img2 = make_my_image(__DIR__.'/images/'.$ran.'_2.jpg',$record_name);

                    $msgType = "text";
                    $url = WEBSITE."neo.php?t=2&f=$img1";
                    $contentStr = "你的专属海报已经出炉！还不赶快点击 $url 获取！";
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                    echo $resultStr;
                }
                else
                {
                    $prefix_text = "自由你发现";
                    $index = stripos($keyword,$prefix_text);
                    if($index !== FALSE && $index == 0){

                        $_SESSION["U_NAME"] = substr($keyword, strlen($prefix_text));

                        $msgType = "text";
                        $contentStr = "你好！{$_SESSION["U_NAME"]} 请拍照上传现场地贴照片，获取你的专属海报，和NEO一起青春出走！";
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }
                    else
                    {
                        $msgType = "text";
                        $contentStr = <<<E
"NEO#自由你发现#青春出走第一步！
如果你正在路演现场，输入【自由你发现+你的名字】来创造自己的专属海报吧!
没能到场的小伙伴也不用遗憾，输入【NEO+你的名字】也能获得你的专属海报！小伙伴们一起来寻找自由吧！
E;
                        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                        echo $resultStr;
                    }
                }
            }
        }
        else
        {
            echo "";
            exit;
        }
    }

    private function getAccessToken(){
        $token = json_decode(file_get_contents("wxtoken"));

        $time = $token->time;
        $access_token = $token->access_token;

        if(time() - $time > $token->timeout)
        {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.APPKEY.'&secret='.APPSECRET);
            //curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            curl_close($ch);

            $json = json_decode($result);

            $access_token = $json->access_token;

            $timeout = $json->expires_in;

            file_put_contents("wxtoken",json_encode(array(
                'access_token'  => $access_token,
                'time'          => time(),
                'timeout'       => $timeout - 20
            )));
        }
        return $access_token;
    }

    /**
     * @param $img image's full path
     */
    public function uploadImage($img){

        $ch = curl_init();

        $data = array('name' => 'Foo', 'file' => '@'.$img);

        curl_setopt($ch, CURLOPT_URL, 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->getAccessToken().'&type=image');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($result);
        if(isset($json->errcode))
            record_err($json->errmsg);
        return $json->media_id;
    }

    /**
     * @param $img image's full path
     */
    public function sendMsg($touser,$msg){

        $ch = curl_init();

        $body = json_encode(array(
            'touser' => $touser,
            'msgtype'=> 'text',
            'text' =>
            array(
                'content'  => $msg
            )
        ));
        //$data = array('body' => $body);

        curl_setopt($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getAccessToken());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//        record_err('token:'.$this->getAccessToken().',body:'.$body);

        $result = curl_exec($ch);

        curl_close($ch);

        $json = json_decode($result);
        if(isset($json->errcode))
            record_err($json->errcode.'=>'.$json->errmsg);
        return $json->media_id;

    }

    public function downloadImage($media_id){

        $ch = curl_init();

        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;


        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);

        curl_close($ch);

        $im = imagecreatefromstring($result);
        $w = imagesx($im);
        $h = imagesy($im);
        $nim = imagecreatefrompng(__DIR__."/images/logo.png");

        imagecopymerge($im, $nim, ($w - 48)/2,0,0,0,48,75,100);

        $file = __DIR__ . "/tmp/".uniqid().'.jpg';
        imagejpeg($im, $file, 80);
        imagedestroy($im);
        imagedestroy($nim);

//        $json = json_decode($result);
//        if(isset($json->errcode))
//            record_err($json->errmsg);
        return $file;
    }

    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}