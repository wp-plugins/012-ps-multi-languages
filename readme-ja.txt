=== 012 Ps Multi Languages ===
Contributors: ouhinit
Tags: multilingual,multi languages, multilingualization, m17n,wpml, i18n
Requires at least: 1.0
Tested up to: 3.3.2

wordpress multilingualization.
== Description ==
One URLより多国語化を関する、簡単にConfigファイルを設定してから、WEB上で多国語の管理ができます。
このプラグインはマルチサイトを対応可能です。

= Functions =
1.Wordpressサイトを多国語化
2.One URLで言語切替
3.適用項目、カテゴリー、タグ、タクソノミー、投稿、ページ、カスタム投稿タイプなどの管理表示

= Usage =
1.General Settings : 多国語のサイト名、キャッチフレーズ、キーワードが設定可能です。
2.Taxonomy : カテゴリー、タグ、タクソノミーの簡単管理。フロントエンドの表示が言語より自動に切替
3.Post : 投稿、ページ、カスタム投稿タイプの簡単管理。フロントエンドの表示が言語より自動に切替
4.List : wp_list_pages wp_nav_menuの関数を使う場合、既に対応済み
5.Bread Crumb : パンくずナビの使用方法：<?php if ( function_exists( 'pml_bread_crumb' ) ) pml_bread_crumb(); ?>
6.Languages List :　言語一覧の表示と取得使用方法： <?php if ( function_exists( 'pml_multilingual_list' ) ) pml_multilingual_list(); ?>
					pml_multilingual_list( $echo = false, $lang = null ) $echo:配列を取得か、Htmlを生成か、$lang：特定の言語のクラスにcurrentを設定
== Installation ==

1. pluginsフォルダに、ダウンロードした 012 Ps Multi Languages のフォルダをアップロードしてください。
2. _congif.phpという設定を設定して、congif.phpにリネームします。（もしマルチサイトの場合、config-$blog_id.phpのような設定ファイルが優先されます）
	あるいは
	/wp-content/に012-m17n-configというディレクトリを作成し、configの設定ファイルを012-m17n-configにコピーOR移動します。
	（/wp-content/に012-m17n-configディレクトリしたの設定ファイルが優先されます。）
3. プラグイン一覧から"012 Ps Multi Languages"というプラグインを有効する。



== Changelog ==
= Version 1.0 (15-06-2012) =
* PUBLISH: [012 ps multi languages] リリース
