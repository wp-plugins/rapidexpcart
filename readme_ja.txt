=== RapidExpCart ===
Contributors: rapidexp
Donate link: http://cart.rapidexp.com/resume/
Tags: shopping,cart,EC
Requires at least: 2.0.2
Tested up to: 3.0.4
Stable tag: 1.0

RapidExpCartはショッピングカートシステムで、WordPressプラグインとショッピングカート本体の２つのプログラムから構成されます。

== Description ==

RapidExpCartはショッピングカートシステムで、WordPressプラグインとショッピングカート本体の２つのプログラムから構成されます。
このプラグインの他にショッピングカート本体プログラムを [rapidexp.com](http://cart.rapidexp.com/resume/downloads/) からダウンロードする必要があります。

本体プログラムの必要動作要件は次の通りです。

* php5 + mysqli (モジュールモードを推奨)
* MySql5 (InnoDBを推奨)
* mod_write

現在のところ、日本語にのみ対応しています。


== Installation ==

1. プラグインを /wp-content/plugins/ にディレクトリごとアップロードします。
2. WordPressの「プラグイン」メニューでプラグインを有効にします。
3. WordPressの「設定 > RapidExpCart」メニューに、本体プログラムのインストール先パスを設定します。
4. WordPressの「ページ」メニューで '_RAPIDEXPCART_TEMPLATE_TITME_' のパーマリンクをメモします。
5. 本体プログラムを3で決めたディレクトリにアップロードします。
6. ディレクトリ data, logs, `session のパーミッション設定で書き込みを許可します。
7. 本体プログラムの /php/share/ ディレクトリで、config.sample.inc.php を参考に config.inc.phpを作成します。
8. メモしておいたパーマリンクをconfig.inc.phpに書き写します。
9. ブラウザのアドレス欄から本体プログラムの /php/admin/install.php を実行します。

RapidExpCartの商品一覧と商品ページはブログとは独立しています。
ブログコンテント内に商品情報を含ませたい場合は、次のマークアップを記述してください。

* [rapidexpcart button=_sku_] SKU番号で指定されたカートボタンに置換されます。

== Frequently Asked Questions ==

= プラグインの他にショッピングカート本体プログラムが必要なのはなぜ？ =

RapidExpCartはカスタムオーダーの本格ECシステムをベースにしています。
システム導入後のカスタマイズには、独立したプログラムのほうが便利です。

== 「プロ版」を手に入れるにはどうすればよいですか？ ==

安定バージョンとなったら「プロ版」は有償販売する予定です。
それより先に入手されたい方はご連絡ください。
動作確認等の支援や寄付をしてくださる方からの連絡をお待ちしています。

== Screenshots ==

1. ショッピングカート

== Changelog ==

= 1.0 =
* テンプレートページににrobotsのメタタグを追加
* イベントとダウンロードのマークアップを追加
* 無効なマークアップを画面出力しないようにした

= 0.10 =
* WordPress.orgで公開

== Upgrade Notice ==

= 1.0 =
プラグイン1.0は本体プログラム1.0以降用ですが、異なるバージョンを組み合わせて使用しても支障はありません。

