<?php
require('dbconnect.php');

session_start();

if(!empty($_COOKIE['email'])) {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['save'] = 'on';
}

if(!empty($_POST)) {
    //ログイン処理
    if($_POST['email'] !='' && $_POST['password'] !='') {
        $login = $db->prepare('SELECT * FROM members WHERE email=?');
        $login -> execute(array(
            $_POST['email']
        ));
        $member = $login -> fetch();

        if($member) {
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            //ログイン情報を保存する
            if($_POST['save'] == 'on') {
                setcookie('email',$_POST['email'],time()+60*60*24*14);
            }

            header('Location:index2.php'); exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<link rel="stylesheet" href="style.css">
<div id="lead">
<p>メールアドレスとパスワードを入力してください</p>
<p>入会手続きがまだの方はこちら</p>
<p>&raquo;<a href="./index.php">入会手続きをする</a></p>
</div>
<form action="" method="post">
    <dl>
        <dt>メールアドレス</dt>
        <dd>
        <input type="text" name="email" size="35" maxlength="255" 
        value="<?php echo htmlspecialchars($_POST['email'] ?? '',ENT_QUOTES);?>"/>
        <?php if(isset($error['login']) && $error['login'] == 'blank'):?>
        <p class="error">*メールアドレスとパスワードを入力してください</p>
        <?php endif;?>
        <?php if(isset($error['login']) && $error['login'] == 'failed'):?>
        <p class="error">*ログインに失敗しました。正しく入力してください。</p>
        <?php endif;?> 
        </dd>
        <dt>パスワード</dt>
        <dd>
        <input type="password" name="password" size="35" maxlength="255" 
        value=""/>
        </dd>
        <dt>ログイン情報を保存する</dt>
        <dd>
        <input id="save" type="checkbox" name="save" value="on"><label for="save">次回から自動的にログインする</label>
        </dd>
</dl>
<div><input type="submit" value="ログインする" /></div>
</form>