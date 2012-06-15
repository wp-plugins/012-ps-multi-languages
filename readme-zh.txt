=== 012 Ps Multi Languages ===
Contributors: ouhinit
Tags: multilingual,multi languages, multilingualization, m17n,wpml, i18n
Requires at least: 1.0
Tested up to: 3.3.2

wordpress multilingualization.
== Description ==
在一个URL下实现多国语的功能，只是简单地设定一下设定文件就能实现WEB上的多国语言管理网站。

= Functions =
1.Wordpress网站实现多国语言
2.能在相同的URL下实现各国语言切换
3.适用功能:分类，标签，自定义分类，文章，页面，自定义文章类型的管理和表示

= Usage =
1.General Settings : 能管理多国语言的站点标题，副标题，关键字等。
2.Taxonomy : 实现分类，标签，自定义分类簡単管理。语言切换是表示页面自动变换语言。
3.Post : 实现文章，页面，自定义文章类型的管理和表示的簡単管理。语言切换是表示页面自动变换语言。
4.List : wp_list_pages wp_nav_menu等函数使用时，自动变换语言関数。
5.Bread Crumb : 面包屑的使用方法：<?php if ( function_exists( 'pml_bread_crumb' ) ) pml_bread_crumb(); ?>
6.Languages List :　设定语言的取得和使用方法： <?php if ( function_exists( 'pml_multilingual_list' ) ) pml_multilingual_list(); ?>
					pml_multilingual_list( $echo = false, $lang = null ) $echo:取得语言数组、生成语言一览表的Html、$lang：给特定的语言加CLsss的标记
== Installation ==

1. 先下载012 Ps Multi Languages插件然后上传到plugins文件夹下
2. 在_congif.php按照例子设定好想要的多国语言，然后把_congif.php从新命名成congif.php（如果是多语言的网站 可以用子网站ID命名审定文件名config-$blog_id.php，插件自动优先读取该设定文件。） 
	或者
	在/wp-content/下作成名为012-m17n-config的文件夹、把config設定文件拷贝或者移动到012-m17n-config中
	（/wp-content/に012-m17n-config这里的设定文件最优先）
3. 最后在插件一览中启用"012 Ps Multi Languages"。



== Changelog ==
= Version 1.0 (15-06-2012) =
* PUBLISH: [012 ps multi languages] 公布