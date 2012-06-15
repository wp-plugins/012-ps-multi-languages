=== 012 Ps Multi Languages ===
Contributors: ouhinit
Tags: multilingual,multi languages, multilingualization, m17n,wpml, i18n
Requires at least: 1.0
Tested up to: 3.3.2

wordpress multilingualization.
== Description ==
*Manager multi-language Web site for　one URL.
*Easy to setting config file realization multi-language Web for Manager.
*makes this plugin easy to build multilingual sites.multi site OK.


= Functions =
*1.wordpress site multilingualization
*2.One URL
*3.Coverage category,tags,taxonomy,post,page,custom post type,site

= Usage =
*1.General Settings : Site Name , Tagline, keywod
*2.Taxonomy : category, tag, custom taxonomy
*3.Post : page, post, custom post type
*4.List : wp_list_pages wp_nav_menu
*5.Bread Crumb : 
*6.Languages List : 

== Installation ==

1. Upload the 012 Ps Multi Languages folder to the plugins directory in your WordPress installation
2. You can Sample file from (_config.php), to add the multilingual and rename from  _config.php to config.php( if multi site can config-$blog_id.php )
   or 
   make dir 012-m17n-config in /wp-content/ and move setting files to  /wp-content/012-m17n-config/config
   * priority is hight of /wp-content/012-m17n-config/setting files
3. Go to plugins list and activate "012 Ps Multi Languages". 

= Examples =
**Default**<br />
<?php if ( function_exists( 'pml_bread_crumb' ) ) pml_bread_crumb(); ?>

<?php if ( function_exists( 'pml_multilingual_list' ) ) pml_multilingual_list(); ?>

== Changelog ==
= Version 1.0 (15-06-2012) =
* PUBLISH: [012 ps multi languages] release


== Screenshots ==
1. category list
2. add category
3. post list
4. edit post/page