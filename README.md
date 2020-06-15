# オープンデータプラットフォーム Yo-KAN
Yo-KAN は CKAN や DKAN と同じような、データ公開のためのオープンソースプログラムです。
Yo-KAN を利用すれば、WordPress風のデータストアが実に簡単に作成できます。
 
# DEMO
Yo-KAN は 無料レンタルサーバーでも超余裕で動作します。<br>
* <a href="https://www.mirko.jp/yo-kan/" target="_blank">メインのYo-KAN紹介サイト</a><br>
* <a href="http://yokan.php.xdomain.jp/" target="_blank">無料レンタルサーバー XFREE に設置したデモ</a><br>
* <a href="http://yokan.starfree.jp/" target="_blank">スターサーバーフリー に設置したデモ</a><br>
* <a href="https://ss1.xrea.com/yookan.s1010.xrea.com/" target="_blank">XREA Free（無料プラン）に設置したデモ（SSL対応）</a><br>
* 流行りの <a href="https://yo-kan.herokuapp.com/" target="_blank">Herokuでデプロイ</a><br>
 
# Features
Yo-KAN が目指すところ。それは小規模の自治体や企業、NPO、シビックテック等が、自前で構築し自力で運用できるオープンデータストア。
PHPが動くサーバーであれば、実行ファイルを設置するだけでサクサク動きます。

# Requirement
* PHP 7.0以上（5系のPHPは動作確認要！！！！！！！！！！！！！）
* MySQLなどのRDBMSはデータベースは不要です。
 
# Installation
このリポジトリのファイル・ディレクトリをダウンロード（Clone）し、FTPやGITなどお好みの方法で、PHPが動作するWEBサーバーへすべてまとめてアップロードしてください。

# Usage
WEBブラウザで、設置したURL（ルートディレクトリ）にアクセスしてください。<br>
login.php にリダイレクトし、「管理者アカウント」の作成を求められますので、ID・パスワード・メールアドレスを入力し作成してください。
管理者アカウントを作成すると、Yo-KANのWEBサイトが公開されます。<br>
その後、管理画面にて公開するデータをアップロードしたり、サイトの見た目の変更を行ってください。権限を限定した一般アカウントを作成することも可能です。
 
# Note
できるだけSSL対応（https）のサーバーに設置してください。<br>
SSL未対応（http）のサーバーの場合は、ログインIDやパスワードが盗まれる可能性があります。<br>
ただし、パスワードはブラウザJavascriptでハッシュ化（ソルト⇒ストレッチング）された上で送信されるため、平文送信でも解析が困難な状態にはなっています。
自己責任において判断してください。

# Author
作成情報を列挙する
* 作成者
* 所属
* E-mail
 
# License
ライセンスを明示する
 
"hoge" is under [MIT license](https://en.wikipedia.org/wiki/MIT_License).
 
社内向けなら社外秘であることを明示してる
 
"hoge" is Confidential.




