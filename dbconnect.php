<?php
try{
    $db = new PDO('mysql:dbname=mini_bbs;host=127.0.0.1;charset=utf8','root','');
} catch (PDOException $e) {
 echo 'ＤＢ接続エラー：'.$e -> getmessage();
}

?>