<!Doctype html>
<html>
<head>
    <title>
        自由你发现
    </title>
    <meta name="description" content="" />

    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=0.5,user-scalable=1,target-densitydpi=326">
    <meta name="author" content="80GP.COM Dev Team">
    <meta content="telephone=no" name="format-detection" />
    <meta name="screen-orientation" content="portrait"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="full-screen" content="yes"/>
    <meta name="x5-fullscreen" content="true"/>
    <style>
        body{margin: 0; padding: 0; font-size: 0;}
        img{margin: 400px auto 0 auto; border: 12px #fff solid;}

    </style>
</head>
<body>
    <div style="background:url('images/bg<?php echo @$_GET['t']?>.png') no-repeat;width:640px;height:1011px;text-align: center; margin: 0 auto;">
    <?php
    $f = $_GET['f'];
    if(@$_GET['t'] == '1')
        echo "<img src=\"n/$f.jpg\" style='margin-top: 340px;max-width: 600px;max-height: 500px'/>";
    else
        echo "<img src=\"n/$f.jpg\" width='460'/>";
    ?></div>
</body>
</html>