<?php
ini_set('display_errors', "On");
session_start();
header('X-FRAME-OPTIONS: SAMEORIGIN'); //クリックジャッキング対策
function hsc($str){return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');} //htmlspecialchars定義関数
require_once './makeLog.php'; //ログファイル書き込み関数を呼び出し
if(isset($_SESSION['sessionname'])) { //セッションがすでにある
	header( "Location: ./controlpanel.php" ) ; //コントロールパネルにとぶ
	exit;
}
if(!isset($_POST["user_name"])){ //ポストなし
	header( "Location: ./login.php" ) ; //ログインページにとぶ
	exit;
}

// セッショントークンの確認
$token = $_POST['token']; //tokenを変数に入れる
if(!(hash_equals($token, $_SESSION['token']) && !empty($token))) { 
	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;
	makeLog($url.' => 【不正アクセス注意】何者かが不正なアクセスによりログインを試みたが失敗') ;
	exit("不正アクセスの可能性があります。");
}

// サーバーキャッシュのクリアのための処理
$fileName = "accountData.php" ;
copy($fileName, $fileName.'copy'); // コピーを作成
unlink($fileName);                 // 原本を削除
copy($fileName.'copy', $fileName); // コピーから原本を再作成
unlink($fileName.'copy');          // コピーを削除
	
require_once 'accountData.php'; //accountData.phpを呼び出し

$passHash  = hash("sha256",($_POST["password"]."opendata")); //ソルト入りパスワードをまず１回ハッシュ化
for ($i = 0; $i < 1000; $i++){ //ストレッチング1000回
	$passHash  = hash("sha256",$passHash);
}

$login = false ; //ログイン成功したかどうか（成功=true）

///////////////////////////////////////////  ①ログイン成功
//管理者 かつ パスワード合致 かつ ロックされていない
if($admin_info[0]['id']===$_POST["user_name"] && $admin_info[0]['pass']===$passHash && $admin_info[0]['rock']===0){

	$_SESSION['sessionname'] = $_POST["user_name"] ; //セッションをセット
	
	// ログ記録
	makeLog($_POST["user_name"].' => 管理者として正常にログイン') ;
	
	unset($_SESSION['loginFailure']); //ログイン失敗セッションをクリア
	
	$login = true ;
	
	// 深いところにありheaderでは難しいのでメタタグでリダイレクト
	// header( "Location: ./controlpanel.php" ) ; //会員ページにとぶ
	echo '<meta http-equiv="refresh" content=" 0; url=./controlpanel.php">'; 
	exit;
	
}else{
	for ($i=0; $i<count($user_info); $i++) {
		//ユーザー かつ パスワード合致 かつ ロックされていない
		if($user_info[$i]['id']===$_POST["user_name"] && $user_info[$i]['pass']===$passHash && $user_info[$i]['rock']===0 ){
			$_SESSION['sessionname'] = $_POST["user_name"] ; //セッションをセット
			
			// ログ記録
			makeLog($_POST["user_name"].' => 正常にログイン') ;
			
			$login = true ;
			unset($_SESSION['loginFailure']); //ログイン失敗セッションをクリア
			
			// 深いところにありheaderでは難しいのでメタタグでリダイレクト
			// header( "Location: ./controlpanel.php" ) ; //会員ページにとぶ
			echo '<meta http-equiv="refresh" content=" 0; url=./controlpanel.php">'; 
			exit;
			
			break;
		}
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />
<meta name="robots" content="noindex,nofollow,noarchive" /> <!-- 検索エンジンに登録させない -->
<title>ログインの判定</title>
<link rel="stylesheet" type="text/css" href="css/setting.css">
</head>
<body>
<div class="contentS">

<?php

$rockFlag = false ; //ロックされているかどうか（ロック=true）

///////////////////////////////////////////  ロックがかかっている場合
//管理者
if($_POST["user_name"] === $admin_info[0]['id'] && $admin_info[0]['rock'] === 1){
	$rockFlag = true ;
	echo "ID:".$_POST["user_name"]."はロックされています。対応方法を管理者のメールへ送信していますのでご確認ください。<br><br>" ;
	
	// ログ記録
	makeLog('ロックされている管理者のID:'.$_POST["user_name"].' でログインを試みたが失敗') ;
	
	echo "<a href='login.php'>ログインフォームへ</a><br><br>" ;
	$login = true ; //成功でないが成功扱いにして失敗時の処理に移行させないようにする。
}
//一般ユーザー
for ($j=0; $j<count($user_info); $j++) {
	if($_POST["user_name"] === $user_info[$j]['id'] && $user_info[$j]['rock'] === 1){ 
		$rockFlag = true ;
		echo "ID:".$_POST["user_name"]."はロックされています。ロックを解除する場合は管理者に依頼してください。<br><br>" ;
			
		// ログ記録
		makeLog('ロックされているID:'.$_POST["user_name"].' でログインを試みたが失敗') ;
	
		echo "<a href='login.php'>ログインフォームへ</a><br><br>" ;
		$login = true ; //成功でないが成功扱いにして失敗時の処理に移行させないようにする。
		break;
	}
}

///////////////////////////////////////////  ログイン失敗
if(!$login){ 
	unset($_SESSION['sessionname'] ); //セッションの中身をクリア
	echo "ID またはパスワードが違います。<br><br>" ;
	echo "<a href='login.php'>ログインフォームへ</a><br><br>" ;
	
	
	if(!isset($_SESSION['loginFailure'])){ //ログイン失敗のセッションが無ければ失敗セッションを新規作成
		$_SESSION['loginFailure'][] = array('ID'=>$_POST["user_name"] , 'count'=>1 );
		
		// ログ記録
		makeLog('ID:'.$_POST["user_name"].' でのログイン失敗（ID又はパスワード違い） / 失敗'.$_SESSION['loginFailure'][0]['count'].'回目') ;

	}else{ //ログイン失敗のセッションが既にある場合
		$newFailFlag = true; //新規のIDかどうかの判定フラグ
		for ($i=0; $i<count($_SESSION['loginFailure']); $i++) {
			if($_SESSION['loginFailure'][$i]['ID'] ===  $_POST["user_name"]){ //失敗のIDがあれば
				$_SESSION['loginFailure'][$i]['count']++ ; //カウントを増やす
				$newFailFlag = false ; //新規のIDではなかったフラグ
				break;
			}
		}
		if($newFailFlag){ //新規フラグの場合
			$_SESSION['loginFailure'][] = array('ID'=>$_POST["user_name"] , 'count'=>1 ); //配列の最後に追加	
		}

		// ログ記録
		makeLog('ID:'.$_POST["user_name"].' でのログイン失敗（ID又はパスワード違い） / 失敗'.$_SESSION['loginFailure'][$i]['count'].'回目') ;
	}

	for ($i=0; $i<count($_SESSION['loginFailure']); $i++) { //$_SESSION['loginFailure']の配列数をカウント
		
		if($_SESSION['loginFailure'][$i]['ID'] === $_POST["user_name"]){ 
		
			// アカウントのロックフラグをたてる処理
			if($_SESSION['loginFailure'][$i]['count']>4){ //失敗カウントが５になったら

				$_SESSION['loginFailure'][$i]['count'] = 0; //セッションの失敗カウントを0にリセット

				if($admin_info[0]['id'] === $_POST["user_name"]){
					echo "アカウントID: ".$_POST["user_name"]." はロックされました。対応方法を管理者のメールへ送信しましたのでご確認ください。<br>" ;
					echo "※ メールが届かない場合は迷惑メールとしてブロックされている可能性がありますのでご注意ください。" ;
					
					// ログ記録
					makeLog('【不正アクセスに注意】ID:'.$_POST["user_name"].'（管理者）でのログインに連続5回失敗したのでアカウントロック') ;
					makeLog('アカウントロック解除のための案内メールをシステムから管理者のメールアドレスへ送信') ;

					$admin_info[0]['rock'] = 1 ; //管理者をロック
					sendMessage( $admin_info[0]['mail'], $admin_info[0]['id'] ) ; // ロック解除するためのメール送信関数
					
				}
				for ($j=0; $j<count($user_info); $j++) {
					if($user_info[$j]['id'] === $_POST["user_name"]){ 
						echo "アカウントID: ".$_POST["user_name"]." はロックされました。ロックを解除する場合は管理者に依頼してください。<br><br>" ;
						
						// ログ記録
						makeLog('【不正アクセスに注意】ID:'.$_POST["user_name"].' でのログインに連続5回失敗したのでアカウントロック') ;
						
						$user_info[$j]['rock'] = 1 ; //ユーザーをロック（ユーザーの場合はメールなし）
						break;
					}
				}
				//書き込むテキストの生成
				$inputText = '<?php'."\n";
				$inputText .= '$admin_info = '.var_export($admin_info,true).' ;'."\n"; //var_export の第２引数にtrueを入れると文字列として変数に代入できる
				$inputText .= '$user_info = '.var_export($user_info,true).' ;'."\n"; //var_export の第２引数にtrueを入れると文字列として変数に代入できる
				$inputText .= '$site_setting = '.var_export($site_setting,true).' ;'."\n"; 
				$inputText .= '?>'."\n";
				
				$fp = fopen("accountData.php", "a");
				//$fp = fopen($fileName, "a");
				if (flock($fp, LOCK_EX)) {  // 排他ロックを確保
					ftruncate($fp, 0);      // ファイルを切り詰め
					fwrite($fp, $inputText);
					fflush($fp);            // 出力をフラッシュしてから
					flock($fp, LOCK_UN);    // ロックを解放
				}
				fclose($fp);
			}
			break;
		}
	}		
}
	

//ロック解除するためのメール送信
function sendMessage($mailAddress, $id) {
	mb_language("Japanese"); 
	mb_internal_encoding("UTF-8");

	// 変数の設定
	$to = $mailAddress ;
	$subject = "【自動応答】アカウントのロックについて";
	$text = "（これはYo-KANシステムからの自動応答メールです。このメールには返信できません。）"."\n"."\n";
	$text .= "管理者アカウントへのログインに５回失敗したため、管理者アカウントがロックされました。"."\n";
	$text .= "ロックを解除する場合は以下のURLにアクセスしてください"."\n";
	
	$myPath = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; //このファイルのフルパス

	//$dirname = dirname($myPath); //親ディレクトリのパス
	$dirname = str_replace('/loginProcess.php', '', $myPath);

	$randomTxt = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 36).".php"; //36文字のランダムテキスト + 拡張子php

	$text .= $dirname . "/" . $randomTxt ; //解除のURL

	makeFileForRelease($randomTxt, $id, $mailAddress, $dirname); //解除のためのファイル作成関数

	// メール送信
	//mb_send_mail( $to, $subject, $text);
		
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
	//現在のURLを取得し利用サービスを判断
	$url = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] ;

	if(strpos($url,'.herokuapp.com') !== false){ // Heroku の場合（SendGrid利用）
		
		require 'vendor/autoload.php';
		$emailArr = new \SendGrid\Mail\Mail();
		$emailArr->setFrom("system@yookan.com", "");
		$emailArr->setSubject($subject);
		$emailArr->addTo($to,"");
		$emailArr->addContent("text/plain", $text);
		$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
		try {
			$response = $sendgrid->send($emailArr);
			//echo '<br><br><br>サイト管理者へメールを送信しました。ありがとうございました。<br><br><br><br><br>' ;
		} catch (Exception $e) {
			echo '<hr>サーバーのエラーのためメールを送信できませんでした。メールでのロック解除はできません。'."\n";
		}

	} else { // 通常のレンタルサーバー等の場合（mb_send_mail利用）

		
		mb_send_mail( $to, $subject, $text);
		
	}
	
}

//ロック解除するためのファイル作成
function makeFileForRelease($filename, $id, $mailAddress, $dirname) {
	$inputText1 = '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>ロック解除</title></head><body>'."\n";
	$inputText1 .= "<?php"."\n";
	
	// ログ記録
	$inputText1 .= 'require_once \'./makeLog.php\';'."\n";
	$inputText1 .= 'makeLog(\'管理者のパスワード再発行のメールをシステムから管理者のメールアドレスへ送信。\') ;'."\n";
	
	//ロックを解除
	$inputText1 .= 'require_once "accountData.php";'."\n";
	$inputText1 .= '$admin_info[0]["rock"] = 0 ;'."\n";

	//新パスワード生成
	$randomPass = substr(str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz'), 0, 10) ; //10文字のランダムテキスト

	// JSと同一のハッシュ作成ロジック
	$passHash = $randomPass.'opendata'.$randomPass ; //レインボーテーブル対策のソルト
	$passHash  = hash("sha256",$passHash);		
	for ($i = 0; $i < 1000; $i++){ //ストレッチング1000回
		$passHash  = hash("sha256",$passHash);
	}
		
	// ハッシュ化
	$passHash  = hash("sha256",($passHash."opendata")); //ソルト入りパスワードをまず１回ハッシュ化
	for ($i = 0; $i < 1000; $i++){ //ストレッチング1000回
		$passHash  = hash("sha256",$passHash);
	}
	
	$inputText1 .= '$admin_info[0]["pass"] = \''.$passHash.'\' ;'."\n";
		
	//メールを再送信する部分
	$inputText1 .= 'mb_language("Japanese"); '."\n";
	$inputText1 .= 'mb_internal_encoding("UTF-8"); '."\n";
	$inputText1 .= '$to = \''.$mailAddress.'\' ; '."\n";
	$inputText1 .= '$subject = \'【自動応答】管理者パスワードの再発行について\'; '."\n";
	$inputText1 .= '$text = \'（これはYo-KANシステムからの自動応答メールです。）\'."\n"."\n"; '."\n";
	$inputText1 .= '$text .= \'管理者アカウントのパスワードを再発行しました。以下のIDとパスワードでログインしてください。\'."\n"; '."\n";
	$inputText1 .= '$text .= \'ログイン後、パスワードは変更できます。\'."\n"."\n"; '."\n";
	$inputText1 .= '$text .= \'管理者アカウントID：　\'; '."\n";
	$inputText1 .= '$text .= \''.$id.'\'."\n"; '."\n";
	$inputText1 .= '$text .= \'管理者パスワード：　\'; '."\n";
	$inputText1 .= '$text .= \''.$randomPass.'\'."\n"."\n"; '."\n";
	
	
/*
	if(strpos($url,\'.herokuapp.com\') !== false){ 
	require \'vendor/autoload.php\';
	$emailArr = new \SendGrid\Mail\Mail();
	$emailArr->setSubject($subject);
	$emailArr->addTo($to);
	$emailArr->addContent(\'text/plain\', $text);
	$sendgrid = new \SendGrid(getenv(\'SENDGRID_API_KEY\'));
	$response = $sendgrid->send($emailArr);
	}else{ mb_send_mail( $to, $subject, $text); }
*/	
	$inputText1 .= '$url = (empty($_SERVER[\'HTTPS\']) ? \'http://\' : \'https://\').$_SERVER[\'HTTP_HOST\'].$_SERVER[\'REQUEST_URI\'] ;'."\n";
	$inputText1 .= 'if(strpos($url,\'.herokuapp.com\') !== false){'."\n";
	$inputText1 .= 'require \'vendor/autoload.php\';'."\n";
	
	$inputText1 .= '$emailArr = new \SendGrid\Mail\Mail();'."\n";
	
	$inputText1 .= '$emailArr->setFrom(\'system@yookan.com\', \'\');'."\n";
	
	$inputText1 .= '$emailArr->setSubject($subject);'."\n";
	$inputText1 .= '$emailArr->addTo($to,\'\');'."\n";
	$inputText1 .= '$emailArr->addContent(\'text/plain\', $text);'."\n";
	$inputText1 .= '$sendgrid = new \SendGrid(getenv(\'SENDGRID_API_KEY\'));'."\n";
	$inputText1 .= '$response = $sendgrid->send($emailArr);'."\n";
	$inputText1 .= '}else{ mb_send_mail( $to, $subject, $text); }'."\n";

		
	//$inputText1 .= ' mb_send_mail( $to, $subject, $text); '."\n";

	//アカウントデータを上書きするコードの生成
	$inputText1 .= '$inputText = "<?php"."\n";'."\n";
	$inputText1 .= '$inputText .= \'$admin_info = \'.var_export($admin_info,true).\' ;\'."\n";'."\n";
	$inputText1 .= '$inputText .= \'$user_info = \'.var_export($user_info,true).\' ;\'."\n";'."\n";
	$inputText1 .= '$inputText .= \'$site_setting = \'.var_export($site_setting,true).\' ;\'."\n";'."\n";
	$inputText1 .= '$inputText .= "?>"."\n";'."\n";
	$inputText1 .= '$fp = fopen("accountData.php", "w");'."\n";
	$inputText1 .= 'fwrite($fp, $inputText);'."\n";
	$inputText1 .= 'fclose($fp);'."\n";
	//メッセージ
	$inputText1 .= 'echo "管理者アカウントのロックを解除し、パスワードを再発行しました。<br>" ;'."\n";
	$inputText1 .= 'echo "仮パスワードをメールで送信しましたので確認してください。<br><br>" ;'."\n";
	$inputText1 .= 'echo "<a href=\"login.php\">ログインフォームへ</a><br><br>" ;'."\n";
	
	//自己ファイルを削除
	$inputText1 .= 'unlink(\''.$filename.'\');'."\n";
		
	$inputText1 .= '?>'."\n";
	$inputText1 .= '</body></html>'."\n";
	//解除ファイル作成
	$fp = fopen($filename, "w");
	fwrite($fp, $inputText1);
	fclose($fp);
}
?>
</div>
</body> 
</html>