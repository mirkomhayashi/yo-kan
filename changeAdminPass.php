<?php
ini_set('display_errors', "On");
session_start();
header('X-FRAME-OPTIONS: SAMEORIGIN'); //クリックジャッキング対策
function hsc($str){return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');} //htmlspecialchars
require_once './makeLog.php'; //ログファイル書き込み関数を呼び出し
if(!isset($_SESSION['sessionname'])) { //ログインセッションなし

	// ログ記録
	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
	makeLog($url.' => 【エラー】ログインなしでダイレクトアクセスし、ログインページに飛ばされる') ;
	header( "Location: ./login.php" ) ; //ログインページにとぶ
	exit;
}

// サーバーキャッシュのクリアのための処理
$fileName = "accountData.php" ;
copy($fileName, $fileName.'copy'); // コピーを作成
unlink($fileName);                 // 原本を削除
copy($fileName.'copy', $fileName); // コピーから原本を再作成
unlink($fileName.'copy');          // コピーを削除

require_once 'accountData.php'; //accountData.phpを呼び出し

if(!($_SESSION['sessionname'] === $admin_info[0]['id'])){ //管理者でない
	// ログ記録
	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
	makeLog($_SESSION['sessionname'].' => '.$url.' => 【エラー】ダイレクトアクセスし、ログインページに飛ばされる') ;
	header( "Location: ./login.php" ) ;
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />
<meta name="robots" content="noindex,nofollow,noarchive" /> <!-- 検索エンジンに登録させない -->
<title>管理者パスワード変更</title>
<link rel="stylesheet" type="text/css" href="css/setting.css">
<script type="text/javascript" src="js/sha.js"></script><!-- パスワードをハッシュ化するライブラリ -->
</head>
<body>
<div class="contentS">
<?php
echo "管理者アカウント ".hsc($_SESSION['sessionname'])." でログイン中です。" ;
echo '<form action="logout.php" method="POST" style="display:inline">'."\n";
echo '<input name="logout" class="logoutButton" type="submit" value="ログアウト"></form>'."\n";
echo "<hr>"."\n";

echo "<hr>"."\n";
echo '<a href="redirect.html">管理画面に戻る</a>'; 
?>
</div>
<script type="text/javascript">
function excute_submit(){ 
	let pass1 = document.getElementById('pass_word1').value; 
	let pass2 = document.getElementById('pass_word2').value; 
	document.getElementById('pass_word1').value = null; //ポストされる平文パスをnullに
	document.getElementById('pass_word2').value = null; //ポストされる平文パスをnullに
	const regex = new RegExp(/^[0-9A-Za-z]+$/); 
	if (pass1 != pass2){
		alert("再入力のパスワードが一致しません。");
		return;
	}else if (!regex.test(pass1)) {
		alert("使える文字は半角英数字のみです。");
		return;
	}else if (pass1.length < 10 || pass1.length > 30 ) {
		alert("10～30字の間にしてください。");
		return;
	}
	pass1 = pass1 + 'opendata' + pass1 ;
	pass2 = pass2 + 'opendata' + pass2 ;
	let shaObj1 = new jsSHA("SHA-256", "TEXT");
	let shaObj2 = new jsSHA("SHA-256", "TEXT");
	shaObj1.update(pass1);
	shaObj2.update(pass2);
	let passhash1 = shaObj1.getHash("HEX");
	let passhash2 = shaObj2.getHash("HEX");
	for (i=0; i<1000; i++) { 
		let shaObj3 = new jsSHA("SHA-256", "TEXT");
		let shaObj4 = new jsSHA("SHA-256", "TEXT");
		shaObj3.update(passhash1);
		shaObj4.update(passhash2);
		passhash1 = shaObj3.getHash("HEX"); 
		passhash2 = shaObj4.getHash("HEX"); 
	}
	document.getElementById('pass_hide1').value = passhash1 ;
	document.getElementById('pass_hide2').value = passhash2 ;
	document.getElementById("sosinbutton").disabled = true ;
	document.getElementById("sosinbutton").value= "お待ち下さい..." ;
	const timersubmit = function(){
		document.sousin_submit.submit();
	}
	setTimeout(timersubmit, 1500);
}
</script>
</body> 
</html>