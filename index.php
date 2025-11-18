<?php 
session_start();
require('./dbconnect.php');
$error=array();

if(!empty($_POST)) {
    //エラー項目の確認
    if($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if($_POST['password'] == '') {
        $error['password'] ='blank';
    } elseif(strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    } 
    $fileName = $_FILES['image']['name'];
    if(empty($fileName)) {
            $error['image'] = 'blank';
        } else { 
            $ext = substr($fileName, -3);
            if ($ext !='jpg' && $ext != 'gif' && $ext !='png') {
            $error['image'] ='type';
        } 
    }
    if(!empty($error) && empty($error['image'])) {
        $error['image'] = 'reselect';
    }

    if(empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record=$member->fetch();
        if($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }
    if(empty($error)) {
        //画像をアップロードする
        $image = date('YmdHis').$_FILES['image']['name'] ?? '';
        move_uploaded_file($_FILES['image']['tmp_name'],'./member_picture/'.$image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

//書き直し
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = 'true';
}
?>

<link rel="stylesheet" href="style.css">
<p>フォームに必須事項を記入してください。</p>
<form action="" method="post" enctype="multipart/form-data">
    
    <dl>
        <dt>ニックネーム<span class="required">必須</span></dt>
        <dd>
            <input type="text" name="name" size="35" maxlength="255" value="<?php if(!empty($_POST['name'])) echo htmlspecialchars($_POST['name'],ENT_QUOTES)?>"/>
            <?php if(isset($error['name']) && $error['name'] == 'blank'):?>
            <p class = "error">*ニックネームを入力してください</p>
            <?php endif;?>
        </dd>
        <dt>メールアドレス<span class="required">必須</span></dt>
        <dd>
            <input type="text" name="email" size="35" maxlength="255" value="<?php if(!empty($_POST['email'])) echo htmlspecialchars($_POST['email'],ENT_QUOTES);?>" />
            <?php if(isset($error['email']) && $error['email'] == 'blank'):?>
            <p class = "error">*メールアドレスを入力してください</p>
            <?php endif;?>
            <?php if(isset($error['email']) && $error['email'] == 'duplicate'):?>
            <p class = "error">*指定されたメールアドレスは既に登録されています。</p>
            <?php endif;?>
        </dd>
        <dt>パスワード<span class="required">必須</span></dt>
        <dd>
            <input type="password" name="password" size="10" maxlength="20"/>
            <?php if(isset($error['password']) && $error['password'] == 'blank'):?>
            <p class = "error">*パスワードを入力してください</p>
            <?php elseif(isset($error['password']) && $error['password'] == 'length'):?>
            <p class = "error">*４文字以上で入力してください</p>
            <?php endif ;?>
        </dd>
        <dt>写真</dt>
        <dd><input type="file" name="image" size="35" />
            <?php if(isset($error['image']) && $error['image'] == 'blank'):?>
            <p class="error">*画像を選択してください</p>
            <?php elseif(isset($error['image']) && $error['image'] == 'type'):?>
            <p class="error">*写真などは「.gif」または「.jpg」の画像を指定してください</p>
            <?php elseif(!empty($error['image'])):?>
            <p class="error">*申し訳ありませんが、もう一度画像を指定してください</p>
            
            <?php endif;?>
    </dd>
    </dl>
    <div><input type="submit" value="入力内容を確認する" /></div>
    <p><a href='./login.php'>会員登録がお済の方はこちら</p>
</form>

