<?php
ini_set('display_errors', "On");
session_start();
header('X-FRAME-OPTIONS: SAMEORIGIN'); //クリックジャッキング対策
function hsc($str){return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');} //htmlspecialchars定義関数
$fileName = 'accountData.php' ;
if (file_exists($fileName)) {
	copy($fileName, $fileName.'copy'); // コピーを作成
	unlink($fileName);                 // 原本を削除
	copy($fileName.'copy', $fileName); // コピーから原本を再作成
	unlink($fileName.'copy');          // コピーを削除
	require_once './accountData.php'; //allDataList.phpを呼び出し
}else{
	unset($_SESSION['sessionname']); //セッションの中身をクリア
	header( "Location: ./login.php" ) ; //ログインページにとぶ
	exit;	
}
?>
<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0" />
<style type="text/css">
/* index.php 専用 */
.index_hr{
	margin: 40px 0 50px 0 ;
}
.index_pushTextArea {
	padding: 30px 0 ;
	line-height: 2;
}
</style>
<link rel="stylesheet" type="text/css" href="css/index.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<?php
$urlStr = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; // 現在のURLを取得
$urlStr = str_replace('index.php', '', $urlStr); // [index.php]を削除してベースURLを確定 

echo '<meta name="description" content="'.$site_setting[0]['s2_snsText'].'" />'."\n";
echo '<!--OGP設定-->'."\n";
echo '<meta property="og:site_name" content="'.$site_setting[0]['s0_siteName'].'" />'."\n";
echo '<meta property="og:title" content="'.$site_setting[0]['s0_siteName'].'" />'."\n";
echo '<meta property="og:url" content="'.$urlStr.'" />'."\n";
echo '<meta property="og:image" content="'.$urlStr.'img_uploaded/'.$site_setting[0]['s3_snsImg'].'" />'."\n";
echo '<meta property="og:description" content="'.$site_setting[0]['s2_snsText'].'" />'."\n";
echo '<meta property="og:type" content="website" />'."\n";
echo '<!--Twitter Card設定-->'."\n";
echo '<meta name="twitter:card" content="summary_large_image">'."\n";
echo '<meta name="twitter:url" content="'.$urlStr.'" />'."\n";
echo '<meta name="twitter:title" content="'.$site_setting[0]['s0_siteName'].'" />'."\n";
echo '<meta name="twitter:description" content="'.$site_setting[0]['s2_snsText'].'">'."\n";
echo '<meta name="twitter:image:src" content="'.$urlStr.'img_uploaded/'.$site_setting[0]['s3_snsImg'].'">'."\n";
echo '<title>'.$site_setting[0]['s0_siteName'].'</title>'."\n";
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
<div class="content1">
	<form action="index_list.php" name="search_submit" method="GET">
	<input id="sbox5" name="search" type="text">
	<input type="hidden" name="page" value="1" >
	<input type="hidden" name="sort_select" value="A" >
	<input id="sbtn5" type="button" value="検索" onclick="excute_submit()">
	</form>
</div>
<div class="content2">
<p class="index_pushTextArea">
<?php echo $site_setting[0]['s6_contentTextTop']."\n"; ?>
</p>
<?php
$fileName = 'allDataList.php' ;
if (file_exists($fileName)) {

	copy($fileName, $fileName.'copy'); // コピーを作成
	unlink($fileName);                 // 原本を削除
	copy($fileName.'copy', $fileName); // コピーから原本を再作成
	unlink($fileName.'copy');          // コピーを削除
	require_once './allDataList.php'; //allDataList.phpを呼び出し
	
	$displayLimit = 5 ; // 画面に表示させる最大数
	if($displayLimit > count($alldata)){
		$displayLimit = count($alldata) ;
	}
	
	///////////////////////////////////////////////////// 新着順
	$alldata_rectime_sort = $alldata ; //新着順用のコピーをつくる
	//更新日時があるものは作成日時に上書きしてしまう
	for ($i=0; $i<$displayLimit; $i++) {
		if($alldata_rectime_sort[$i]['updtime'] != ''){
			$alldata_rectime_sort[$i]['rectime'] = $alldata_rectime_sort[$i]['updtime'] ;
		}
	}
	// foreachで1つずつ値を取り出す
	foreach ($alldata_rectime_sort as $key => $value) {
		$rectime[$key] = $value['rectime'];
	}
	// array_multisortで昇順に並び替る
	array_multisort($rectime, SORT_DESC, $alldata_rectime_sort);

	echo '<hr class="index_hr"><h3>新着データ</h3>'."\n";
	for ($i=0; $i<$displayLimit; $i++) {

		echo '<p>';
		echo '<span class="datetime">';
		echo hsc( substr($alldata_rectime_sort[$i]['rectime'],0,10) ); // 左から10文字切り出し
		echo ' 公開</span>';
		echo '<span class="dataname">';
		echo '<a href="./data/'.$alldata_rectime_sort[$i]['num'].'/">';
		echo hsc($alldata_rectime_sort[$i]['dataname']);
		echo '</a>';
		echo '</span>';
		echo '<span class="filesizeClass">';

		// ファイルサイズ変換
		$filesize = $alldata_rectime_sort[$i]['filesize'] ;
		if( $filesize <= 1024 ) {
			$filesize = $filesize.' byte' ;
		}else if( $filesize <= 1048576 ){
			$filesize = $filesize / 1024 ;
			$filesize = floor($filesize * pow(10,2) ) / pow(10,2 ).' KB' ; //小数点第3位以下を切り捨ててKBをつける
		}else{
			$filesize = $filesize / 1048576 ;
			$filesize = floor($filesize * pow(10,2) ) / pow(10,2 ).' MB' ; 
		}
		echo $filesize ;
		
		echo '</span></p>';
	}
	
	///////////////////////////////////////////////////// ダウンロード数順
	$alldata_counter_sort = $alldata ;
	foreach ($alldata_counter_sort as $key => $value) {
		$counter[$key] = $value['counter'];
	}
	array_multisort($counter, SORT_DESC, $alldata_counter_sort);
	echo '<hr class="index_hr"><h3>ダウンロードランキング</h3>'."\n";

	for ($i=0; $i<$displayLimit; $i++) {

		echo '<p>';
		echo '<span class="datetime">';
		echo ($i+1).'位';
		echo '</span>';
		echo '<span class="dlcountClass">DL数:';
		echo $alldata_counter_sort[$i]['counter'] ;
		echo '</span>';
		echo '<span class="dataname">';
		echo '<a href="./data/'.$alldata_counter_sort[$i]['num'].'/">';
		echo hsc($alldata_counter_sort[$i]['dataname']);
		echo '</a>';
		echo '</span>';
		echo '<span class="filesizeClass">';

		// ファイルサイズ変換
		$filesize = $alldata_counter_sort[$i]['filesize'] ;
		if( $filesize <= 1024 ) {
			$filesize = $filesize.' byte' ;
		}else if( $filesize <= 1048576 ){
			$filesize = $filesize / 1024 ;
			$filesize = floor($filesize * pow(10,2) ) / pow(10,2 ).' KB' ; //小数点第3位以下を切り捨ててKBをつける
		}else{
			$filesize = $filesize / 1048576 ;
			$filesize = floor($filesize * pow(10,2) ) / pow(10,2 ).' MB' ; 
		}
		echo $filesize ;
		echo '</span>';
		echo '</p>';
	}
}
?>
</p>
<hr class="index_hr">
<p class="index_pushTextArea">
<?php echo $site_setting[0]['s7_contentTextBottom']."\n"; ?>
</p>
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
function excute_submit(){ 
	document.getElementById("sbtn5").disabled = true ;
	const timersubmit = function(){
		document.search_submit.submit();
	}
	setTimeout(timersubmit, 100); 
}
</script>

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