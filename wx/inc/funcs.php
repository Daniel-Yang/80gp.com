<?php
function record_err($msg){
    file_put_contents("log/".uniqid().".txt",date("Y-m-d H:i:s")."\t".$msg."\r\n");
}