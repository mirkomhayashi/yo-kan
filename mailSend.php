<<<<<<< HEAD
<?php
ini_set('display_errors', "On");
session_cache_expire(0);
session_cache_limiter('private_no_expire'); //戻るボタンのWebページの有効期限切れ対策
session_start();
header('X-FRAME-OPTIONS: SAMEORIGIN'); //クリックジャッキング対策
function hsc($str){return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');} //htmlspecialchars定義関数
$fileName = 'accountData.php' ;
if (!(file_exists($fileName))) {
	header( "Location: ./login.php" ) ; //ログインページにとぶ
	exit;
}
$token = $_POST['token']; //tokenを変数に入れる

if(!(hash_equals($token, $_SESSION['token']) && !empty($token))) { // トークンを確認し、確認画面を表示

	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
	makeLog($url.' => 【不正アクセス注意】何者かが不正なアクセスにより、メールフォームから送信しようとしたが失敗') ;
	exit("不正アクセスの可能性があります。");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/index.css">
<?php
copy($fileName, $fileName.'copy'); // コピーを作成
unlink($fileName);                 // 原本を削除
copy($fileName.'copy', $fileName); // コピーから原本を再作成
unlink($fileName.'copy');          // コピーを削除
require_once './accountData.php'; //allDataList.phpを呼び出し

echo '<title>お問い合わせ / '.$site_setting[0]['s0_siteName'].'</title>';
echo '<style type="text/css">'."\n";
echo 'body{ background-image: url(./img_uploaded/'.$site_setting[0]['s1_backgroundImg'].') ;} '."\n";
echo '</style>'."\n";
?>
</head>
<body>
<header class="site-header">
	<div class="site-logo">
		<a href="./">
		<?php
		if($site_setting[0]['s5_headerBanner'] != "N"){
			echo '<img src="./img_uploaded/'.$site_setting[0]['s5_headerBanner'].'"/>'."\n";
		}
		if($site_setting[0]['s4_headerName'] != ""){
			echo '<h1>'.$site_setting[0]['s4_headerName'].'</h1>'."\n";
		}
		?>
		</a>
	</div>
	<div id="wrapper">
		<p class="btn-gnavi">
			<span></span>
			<span></span>
			<span></span>
		</p>
		<nav id="global-navi">
			<ul class="gnav__menu">
				<li class="gnav__menu__item"><a href="./">ホーム</a></li>
				<li class="gnav__menu__item"><a href="index_list.php">オープンデータ一覧</a></li>
				<li class="gnav__menu__item"><a href="index_mail.php">お問い合わせ</a></li>
				<li class="gnav__menu__item"><a href="index_api.php">WEB API</a></li>
			</ul>
		</nav>
	</div>
</header> 
<div id="menu_close"></div><!-- ハンバーガーメニューをクローズするためのサイドバー -->
<div style="margin-top:140px;">  </div>
<div class="content2">
<?php
mb_language('japanese');
mb_internal_encoding('UTF-8');

$email = $admin_info[0]['mail'] ;
$subject = '【自動応答】Yo-KANのお問い合わせフォームからのメッセージを受信';

$mail = $_SESSION['mail'];
$name = $_SESSION['name'];
$comment = $_SESSION['comment'];

$body = "……　Yo-KANのお問い合わせフォームから以下のメッセージを受信しました　……"."\n";
$body .= "-------------------------------------------"."\n";
$body .= "送信者 : ".hsc($name)."\n"."\n";
$body .= "メールアドレス : ".$mail."\n"."\n";
$body .= "お問い合わせ内容 : "."\n";
$body .= $comment ;

//headerを設定
$charset = "UTF-8";
$headers['MIME-Version'] = "1.0";
$headers['Content-Type'] = "text/plain; charset=".$charset;
$headers['Content-Transfer-Encoding'] = "8bit";

//headerを編集
foreach ($headers as $key => $val) {
	$arrheader[] = $key . ': ' . $val;
}
$strHeader = implode("\n", $arrheader);

// mb_send_mail が使えるサーバーの場合（レンタルサーバー等）
if (mb_send_mail($email, $subject, $body , $strHeader)){ 
	echo '<br><br><br>サイト管理者へメールを送信しました。ありがとうございました。<br><br><br><br><br>' ;

// mb_send_mail が使えない場合（無料クラウドなど）はSendGridを利用
}else{ 
	require 'vendor/autoload.php';
	$emailArr = new \SendGrid\Mail\Mail();
	$emailArr->setFrom($mail, $name);
	$emailArr->setSubject($subject);
	$emailArr->addTo($email, "サイト管理者");
	$emailArr->addContent("text/plain", $body);
	$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
	try {
		$response = $sendgrid->send($emailArr);
		echo '<br><br><br>サイト管理者へメールを送信しました。ありがとうございました。<br><br><br><br><br>' ;
	} catch (Exception $e) {
		echo '<br><br><br>申し訳ありません。サーバーのエラーのため送信できませんでした。<br><br><br><br><br>' ;
	}
}
?>
</div>
<footer class="site-footer">
<div class="site-footer-inner">
<?php echo $site_setting[0]['s8_footerText']."\n"; ?>
</div>
</footer>
<div class="yokan">
<a href="https://www.mirko.jp/yo-kan/">Powerd by Yo-KAN</a>
</div>

<script type="text/javascript">
// ヘッダーを出したり隠したりするためのスクリプト
var _window = $(window),
    _header = $('.site-header'),
    heroBottom,
    startPos,
    winScrollTop;
_window.on('scroll',function(){
    winScrollTop = $(this).scrollTop();
    heroBottom = $('.hero').height();
	
    heroBottom = 50;
	
    if (winScrollTop >= startPos) {
        if(winScrollTop >= heroBottom){
            _header.addClass('hide');
        }
    } else {
        _header.removeClass('hide');
    }
    startPos = winScrollTop;
});
_window.trigger('scroll');
</script>

<script type="text/javascript">
// ハンバーガーメニューのためのスクリプト
$(function(){
    $(".btn-gnavi").on("click", function(){
        // ハンバーガーメニューの位置を設定
        var rightVal = 0;
        if($(this).hasClass("open")) {
            // 位置を移動させメニューを開いた状態にする
            rightVal = -240;
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).removeClass("open");
			document.getElementById("menu_close").style.display ="none";
        } else {
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).addClass("open");
			// サイドクローズメニューの幅を取得
			document.getElementById('menu_close').style.width = (document.body.clientWidth - 240)+'px';
			document.getElementById("menu_close").style.display ="block";
        }
        $("#global-navi").stop().animate({
            right: rightVal
        }, 200);
    });
});
// ハンバーガーメニューをクローズサイドバーのスクリプト
$(function(){
    $("#menu_close").on("click", function(){
		var rightVal = 0;
        if($(".btn-gnavi").hasClass("open")) {
            // 位置を移動させメニューを開いた状態にする
            rightVal = -240;
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(".btn-gnavi").removeClass("open");
        } else {
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(".btn-gnavi").addClass("open");
        }
        $("#global-navi").stop().animate({
            right: rightVal
        }, 200);
		document.getElementById("menu_close").style.display ="none";
    });
});
</script>

</body>
</html>
=======
<?php
ini_set('display_errors', "On");
session_cache_expire(0);
session_cache_limiter('private_no_expire'); //戻るボタンのWebページの有効期限切れ対策
session_start();
header('X-FRAME-OPTIONS: SAMEORIGIN'); //クリックジャッキング対策
function hsc($str){return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');} //htmlspecialchars定義関数
$fileName = 'accountData.php' ;
if (!(file_exists($fileName))) {
	header( "Location: ./login.php" ) ; //ログインページにとぶ
	exit;
}
$token = $_POST['token']; //tokenを変数に入れる

if(!(hash_equals($token, $_SESSION['token']) && !empty($token))) { // トークンを確認し、確認画面を表示

	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
	makeLog($url.' => 【不正アクセス注意】何者かが不正なアクセスにより、メールフォームから送信しようとしたが失敗') ;
	exit("不正アクセスの可能性があります。");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/index.css">
<?php
copy($fileName, $fileName.'copy'); // コピーを作成
unlink($fileName);                 // 原本を削除
copy($fileName.'copy', $fileName); // コピーから原本を再作成
unlink($fileName.'copy');          // コピーを削除
require_once './accountData.php'; //allDataList.phpを呼び出し

echo '<title>お問い合わせ / '.$site_setting[0]['s0_siteName'].'</title>';
echo '<style type="text/css">'."\n";
echo 'body{ background-image: url(./img_uploaded/'.$site_setting[0]['s1_backgroundImg'].') ;} '."\n";
echo '</style>'."\n";
?>
</head>
<body>
<header class="site-header">
	<div class="site-logo">
		<a href="./">
		<?php
		if($site_setting[0]['s5_headerBanner'] != "N"){
			echo '<img src="./img_uploaded/'.$site_setting[0]['s5_headerBanner'].'"/>'."\n";
		}
		if($site_setting[0]['s4_headerName'] != ""){
			echo '<h1>'.$site_setting[0]['s4_headerName'].'</h1>'."\n";
		}
		?>
		</a>
	</div>
	<div id="wrapper">
		<p class="btn-gnavi">
			<span></span>
			<span></span>
			<span></span>
		</p>
		<nav id="global-navi">
			<ul class="gnav__menu">
				<li class="gnav__menu__item"><a href="./">ホーム</a></li>
				<li class="gnav__menu__item"><a href="index_list.php">オープンデータ一覧</a></li>
				<li class="gnav__menu__item"><a href="index_mail.php">お問い合わせ</a></li>
				<li class="gnav__menu__item"><a href="index_api.php">WEB API</a></li>
			</ul>
		</nav>
	</div>
</header> 
<div id="menu_close"></div><!-- ハンバーガーメニューをクローズするためのサイドバー -->
<div style="margin-top:140px;">  </div>
<div class="content2">
<?php
mb_language('japanese');
mb_internal_encoding('UTF-8');

$email = $admin_info[0]['mail'] ;
$subject = '【自動応答】Yo-KANのお問い合わせフォームからのメッセージを受信';

$mail = $_SESSION['mail'];
$name = $_SESSION['name'];
$comment = $_SESSION['comment'];

$body = "……　Yo-KANのお問い合わせフォームから以下のメッセージを受信しました　……"."\n";
$body .= "-------------------------------------------"."\n";
$body .= "送信者 : ".hsc($name)."\n"."\n";
$body .= "メールアドレス : ".$mail."\n"."\n";
$body .= "お問い合わせ内容 : "."\n";
$body .= $comment ;

//headerを設定
$charset = "UTF-8";
$headers['MIME-Version'] = "1.0";
$headers['Content-Type'] = "text/plain; charset=".$charset;
$headers['Content-Transfer-Encoding'] = "8bit";

//headerを編集
foreach ($headers as $key => $val) {
	$arrheader[] = $key . ': ' . $val;
}
$strHeader = implode("\n", $arrheader);

// mb_send_mail が使えるサーバーの場合（レンタルサーバー等）
if (mb_send_mail($email, $subject, $body , $strHeader)){ 
	echo '<br><br><br>サイト管理者へメールを送信しました。ありがとうございました。<br><br><br><br><br>' ;

// mb_send_mail が使えない場合（無料クラウドなど）はSendGridを利用
}else{ 
	require 'vendor/autoload.php';
	$emailArr = new \SendGrid\Mail\Mail();
	$emailArr->setFrom($mail, $name);
	$emailArr->setSubject($subject);
	$emailArr->addTo($email, "サイト管理者");
	$emailArr->addContent("text/plain", $body);
	$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
	try {
		$response = $sendgrid->send($emailArr);
		echo '<br><br><br>サイト管理者へメールを送信しました。ありがとうございました。<br><br><br><br><br>' ;
	} catch (Exception $e) {
		echo '<br><br><br>申し訳ありません。サーバーのエラーのため送信できませんでした。<br><br><br><br><br>' ;
	}
}
?>
</div>
<footer class="site-footer">
<div class="site-footer-inner">
<?php echo $site_setting[0]['s8_footerText']."\n"; ?>
</div>
</footer>
<div class="yokan">
<a href="https://www.mirko.jp/yo-kan/">Powerd by Yo-KAN</a>
</div>

<script type="text/javascript">
// ヘッダーを出したり隠したりするためのスクリプト
var _window = $(window),
    _header = $('.site-header'),
    heroBottom,
    startPos,
    winScrollTop;
_window.on('scroll',function(){
    winScrollTop = $(this).scrollTop();
    heroBottom = $('.hero').height();
	
    heroBottom = 50;
	
    if (winScrollTop >= startPos) {
        if(winScrollTop >= heroBottom){
            _header.addClass('hide');
        }
    } else {
        _header.removeClass('hide');
    }
    startPos = winScrollTop;
});
_window.trigger('scroll');
</script>

<script type="text/javascript">
// ハンバーガーメニューのためのスクリプト
$(function(){
    $(".btn-gnavi").on("click", function(){
        // ハンバーガーメニューの位置を設定
        var rightVal = 0;
        if($(this).hasClass("open")) {
            // 位置を移動させメニューを開いた状態にする
            rightVal = -240;
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).removeClass("open");
			document.getElementById("menu_close").style.display ="none";
        } else {
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(this).addClass("open");
			// サイドクローズメニューの幅を取得
			document.getElementById('menu_close').style.width = (document.body.clientWidth - 240)+'px';
			document.getElementById("menu_close").style.display ="block";
        }
        $("#global-navi").stop().animate({
            right: rightVal
        }, 200);
    });
});
// ハンバーガーメニューをクローズサイドバーのスクリプト
$(function(){
    $("#menu_close").on("click", function(){
		var rightVal = 0;
        if($(".btn-gnavi").hasClass("open")) {
            // 位置を移動させメニューを開いた状態にする
            rightVal = -240;
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(".btn-gnavi").removeClass("open");
        } else {
            // メニューを開いたら次回クリック時は閉じた状態になるよう設定
            $(".btn-gnavi").addClass("open");
        }
        $("#global-navi").stop().animate({
            right: rightVal
        }, 200);
		document.getElementById("menu_close").style.display ="none";
    });
});
</script>

</body>
</html>
>>>>>>> 2f1cca9c3f64d21b7e8db90094c84461f74e977e
