<img src="https://www.mirko.jp/yo-kan/img_uploaded/sample_logo.png" />

# （準備中です！！）オープンデータプラットフォーム Yo-KAN
Yo-KAN は CKAN や DKAN と同じような、データ公開のためのオープンソースプログラムです。
Yo-KAN を利用すれば、WordPress風のデータストアが実に簡単に作成できます。
 
# DEMO
* <a href="https://www.mirko.jp/yo-kan/" target="_blank">（VPSサーバー）メインのYo-KAN紹介サイト</a><br>
* <a href="http://yokan.php.xdomain.jp/" target="_blank">（無料レンタルサーバー）XFREE に設置</a><br>
* <a href="http://yokan.starfree.jp/" target="_blank">（無料レンタルサーバー）スターサーバーフリー に設置</a><br>
* <a href="https://ss1.xrea.com/yookan.s1010.xrea.com/" target="_blank">（無料レンタルサーバー）XREA Freeに設置（SSL対応）</a><br>
* <a href="https://yo-kan.azurewebsites.net/" target="_blank">（Free Paas cloud）Microsoft Azure</a><br>
* <a href="https://yo-kan.herokuapp.com/" target="_blank">（Free Paas cloud）Heroku</a> ←寝てると起きるのに10秒ほどかかります<br>
 
# Requirement
* PHP 7.0 以上（5系のPHPは未確認）
* MySQLなどのデータベースは不要です。
 
# Installation
このリポジトリのファイル・ディレクトリをダウンロード（又はClone）し、FTPやGITなどお好みの方法で、PHPが動作するWEBサーバーへすべてまとめてアップロードしてください。

# Usage
WEBブラウザで、ファイルを置いたディレクトリのURLにアクセスしてください。login.php にリダイレクトします。<br>
最初に「管理者アカウント」の作成を求められますので、ID・パスワード・メールアドレスを入力し作成してください。
管理者アカウントを作成すると、Yo-KANのWEBサイトが公開されます。<br>
その後、管理画面にて、公開するデータをアップロードしたり、サイトの見た目の設定を行ってください。権限を限定した一般アカウントを作成することも可能です。
 
# Note
できるだけ<b>SSL対応（https）のサーバーに設置してください</b>。<br>
SSL未対応（http）のサーバーの場合は、ログインIDやパスワードが盗まれる可能性があります。<br>
ただし、パスワードは10文字以上のものを、ブラウザJavascriptで SHA256ハッシュ（ソルト＆ストレッチング）した上で送信するため、平文送信でも解析が難しい状態にはなっています。このあたりは<b>自己責任において判断</b>してください。（20字程度のパスワードにすれば、よっぽどのことがない限り大丈夫でしょう。）<br>
ログイン時にパスワードを5回連続誤ると、そのアカウントはロックされます。管理者アカウントがロックされると、解除するためのメールが管理者へ送信されます。

# Note for free cloud
当アプリは php-mbstring（マルチバイト文字処理の関数群）を利用しています。PHPが利用できる通常のレンタルサーバーであれば、ほとんどの場合あらかじめインストールされていますので意識する必要はありませんが、クラウドサーバーで利用する場合は各自でインストールしてください。（下記のように **composer.json** に require で追加）<br>
また、クラウドサービスの制限によりメール送信用の mb_send_mail関数が利用できない場合は **SendGrid** の利用に自動で切り替わります。（事前にSendGridのアカウントとAPIキーを取得し、利用するクラウドサービス上にて環境変数の設定がしてあるのが前提です。）これも下記のようにcomposer.json に require で追加してください。<br>
その後、**composer update** コマンドでアップデートすると、**composer.lock** が生成されます。その状態でデプロイしてください。

**composer.json**
```bash
{
  "require": {
    "ext-mbstring": "*",
    "sendgrid/sendgrid": "~7"
  }
}
```
**SendGridの環境変数の設定**
```bash
    キー                  値（例）
SENDGRID_USERNAME     yourname@example.com
SENDGRID_PASSWORD     yourpassword
SENDGRID_API_KEY      SG.xxxx......................................
```
私はAzureとHerokuでデプロイできることは確認しましたが、腕に覚えがある方はその他いろいろな環境でお試しいただければと思います。<br>
（AWSやGoogle Cloudは未確認ですが、SendGridの環境変数の設定さえちゃんとしていれば多分大丈夫でしょう。）

# Author
林　正洋
 
# License
[CCO](https://creativecommons.org/publicdomain/zero/1.0/deed.ja)




