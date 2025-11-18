<?php
session_start();
require('dbconnect.php');

if(isset($_SESSION['id'])) {
    $id = $_REQUEST['id'];

    //投稿を削除する
    $message = $db -> prepare('SELECT * FROM posts WHERE id=?');
    $message -> execute(array($id));
    $messages = $message -> fetch();

    if($messages['member_id'] == $_SESSION['id']) {
        $del = $db->prepare('DELETE FROM posts WHERE id=?');
        $del -> execute(array($id));
    }
}

header('Location: index2.php'); exit();?>