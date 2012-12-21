=== 012 Ps Multi Languages ===
Contributors: ouhinit
Tags: multilingual,multi languages, multilingualization, m17n,wpml, i18n
Requires at least: 3.3.x
Tested up to: 3.4

wordpress multilingualization.
== Description ==
* Manager multi-language Web site for　one URL.
* Easy to setting config file realization multi-language Web for Manager.
* Use this plugin easy to build multilingual sites.
* Multi site OK.


= Functions =
* 1.wordpress site multilingualization
* 2.One URL
* 3.Coverage category,tags,taxonomy,post,page,custom post type,site

= Usage =
* 1.General Settings : Site Name , Tagline, keywod
* 2.Taxonomy : category, tag, custom taxonomy
* 3.Post : page, post, custom post type
* 4.List : wp_list_pages wp_nav_menu
* 5.Bread Crumb : 
* 6.Languages List : 

== Installation ==

1. Upload the 012 Ps Multi Languages folder to the plugins directory in your WordPress installation
2. You can Sample file from (_config.php), to add the multilingual and rename from  _config.php to config.php( if multi site can config-$blog_id.php )
   or 
   make dir 012-m17n-config in /wp-content/ and move setting files to  /wp-content/012-m17n-config/config
   ※priority is hight of /wp-content/012-m17n-config/setting files
3. Go to plugins list and activate "012 Ps Multi Languages". 

= Examples =
**Default**<br />
<?php if ( function_exists( 'ps_012_m17n_bread_crumb' ) ) ps_012_m17n_bread_crumb(); ?>

<?php if ( function_exists( 'pml_multilingual_list' ) ) pml_multilingual_list(); ?>

== Changelog ==
= Version 1.0 (15-06-2012) =
* PUBLISH: [012 ps multi languages] release
* MESSAGE: [012 ps multi languages] 3.4 Validated
= Version 1.2 (16-08-2012) =
* MESSAGE: [012 ps multi languages] 1.2 setting multilingual title's size (includescss/edit-post-prefix-style.css)
* MESSAGE: [012 ps multi languages] 1.2 used wp_editor to  function add_multilingual_content (includes/ps-multilingual-edit-post.php)
* Fix    : fixed error
* Fix    : [012 ps multi languages] 1.3 update add_action('the_posts'	to add_filter('the_posts'	.  add add_filter('pre_get_posts') fore function get_posts .
= Version 1.4 (2012.12.07) =
* Fix    : [012 ps multi languages] 1.4 update options-general's key_word 
= Version 1.5 (2012.12.12) =
* Fix    : [012 ps multi languages] 1.5 update add_filter's wp_nav_menu Argument problem
= Version 1.6 (2012.12.12) =
* Fix    : [012 ps multi languages] 1.6 update media edit page for wp's ver 3.5
* MESSAGE: [012 ps multi languages] 3.5 Validated


== Screenshots ==
1. category list.
2. add category.
3. post list.
4. edit post edit page edit custom post.



