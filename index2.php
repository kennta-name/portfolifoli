<?php 
session_start();
require('dbconnect.php');

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    //ログインしている
    $_SESSION['time'] = time();

    $members = $db -> prepare('SELECT * FROM members WHERE id=?');
    $members -> execute(array($_SESSION['id']));
    $member = $members -> fetch();
} else {
    //ログインしていない
    header('Location:login.php');
    exit();
}
 if(!empty($_POST)) {
    if($_POST['message'] != '') {
        $message = $db -> prepare('INSERT INTO posts SET member_id=?, message=?, replay_post_id=?, created=NOW()');
        $message -> execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['replay_post_id']
        ));

        header('Location: index2.php');
        exit();
    }
 }

 //投稿を取得する
$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page']:1;
$page = max($page,1);

//最終ページを取得する
$counts = $db->prepare('SELECT COUNT(*) AS cnt FROM posts');
$counts -> execute();
$cnt = $counts ->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page -1) * 5;

$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
$posts -> bindParam(1,$start,PDO::PARAM_INT);
$posts -> execute();



//返信の場合
$message='';//初期値でメッセージを空にしておく
if(isset($_REQUEST['res'])) {
    $response = $db -> prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));

    $table = $response->fetch();
    $message = '@'.$table['name'].''.$table['message'];
}

//本文のURLリンクに飛べるようにする
function makeLink($value) {
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",'<a href="\1\2">\1\2</a>',$value);
}
?>


<!DOCTYPE html>
<link rel="stylesheet" href="style2.css">
<div style="text-align: right"><a href="logout.php">ログアウト</a></div> 
<form action="" method="post">
    <dl>
        <dt><?php echo htmlspecialchars($member['name'],ENT_QUOTES);?>さん、メッセージを投稿してください</dt>
        <dd>
            <textarea name="message" cols="50" rows="5"><?php echo htmlspecialchars($message,ENT_QUOTES);?></textarea>
            <input type="hidden" name="replay_post_id" value="<?php echo htmlspecialchars($_REQUEST['res'],ENT_QUOTES);?>" />
        </dd>
    </dl>
    <div>
        <input type="submit" value="投稿する" />
    </div>
</form>

<?php foreach ($posts as $post):?>

    <div class='msg'>
     <img src="member_picture/<?php echo htmlspecialchars($post['picture'],ENT_QUOTES);?>" 
             width="48" height="48" 
             alt="<?php echo htmlspecialchars($post['name'],ENT_QUOTES);?>" />
          <p>
             <?php echo makeLink(htmlspecialchars($post['message'],ENT_QUOTES));?>
             <span class="name">
             (<?php echo htmlspecialchars($post['name'],ENT_QUOTES);?>)</span>
             [<a href="index2.php?res=<?php echo htmlspecialchars($post['id'],ENT_QUOTES);?>&page=<?php echo $page;?>">Re</a>]
         </P>
         <p class="day"><a href="view.php?id=<?php echo htmlspecialchars($post['id'],ENT_QUOTES);?>&page=<?php echo $page;?>">
             <?php echo htmlspecialchars($post['created'],ENT_QUOTES);?></a>|
         <?php if($post['replay_post_id'] > 0):?>
            <a href="view.php?id=<?php echo htmlspecialchars($post['replay_post_id'],ENT_QUOTES);?>&page=<?php echo $page;?>">返信元のメッセージ </a>
            <?php endif ?>
            <?php 
            if($_SESSION['id'] == $post['member_id']):?>
            [<a href="delete.php?id=<?php echo htmlspecialchars($post['id']);?>" style="color:#F33;">削除</a>]
            <?php endif;?>
         </p>
    </div>
<?php endforeach;?>

<ul class="paging">
    <?php 
    if($page > 1) {
        ?>
        <li><a href="index2.php?page=<?php print($page - 1);?>">前のページへ</a></li>
        <?php 
        } else {
            ?>
            <li>前のページへ</li>
            <?php 
        }?>
        <?php 
        if($page < $maxPage) {
            ?>
            <li><a href="index2.php?page=<?php print($page+1);?>">次のページへ</a></li>
            <?php
        } else {
            ?>
            <li>次のページへ</li>
            <?php
        }
        ?>
        </ul>
    </div>
    </div>
    </body>
    </html>
        