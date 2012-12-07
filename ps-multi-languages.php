<?php
/*
Plugin Name: 012 PS Multi Languages
Plugin URI: http://wordpress.org/extend/plugins/012-ps-multi-languages/
Description: Manager Multilingual Wordpress for one URL 
Author: Wang Bin (oh@prime-strategy.co.jp)
Version: 1.4
Author URI: http://www.prime-strategy.co.jp/about/staff/oh/
*/

/**
 * ps_multi_languages
 *
 * Main Manager Custom Plugin Class
 *
 * @package ps_multi_languages
 */
class ps_multi_languages{
	/*
	 *マルチ言語
	 */
	var $multilingual;
	
	/*
	 * マルチ言語コード
	 */
	var $multilingual_code;
	
	/*
	 * 検索の表示タイトル
	 */
	var $search_multilingual_title;
	
	/*
	 * タクソノミーの頭文字
	 */
	var $prefix_taxonomy;
	
	/*
	 * バックエンドの言語（システムの言語）
	 */
	var $WPLANGKEY;
	
	/*
	 * デフォルトの言語
	 */
	private  $load_lang = 'en';

	/*
	*	Start Manager Custom  on plugins loaded
	*/
	function ps_multi_languages( ){
		$this->__construct( );
	}

	/*
	 * コンストラクタ.
	 */
	function __construct( ) {
		$this->init( );
			
	}

	/**
	* ファンクション名：get_load_lang
	* 機能概要：ロード言語を取得する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param なし
	* @return String ロード言語
	*/
	function get_load_lang( ){
		return $this->load_lang;
	}
	
	/**
	* ファンクション名：set_load_lang
	* 機能概要：ロード言語を設定する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param String ロード言語
	* @return なし
	*/
	function set_load_lang( $lang ){
		
		if ( $this->multilingual_code[$lang] ):
			$this->load_lang = $lang;
		else:
			$this->load_lang = $this->WPLANGKEY;
		endif;

	}

	/*
	 * initializing
	 */
	function init( ){

		if( !defined('DOCUMENTROOT') ):
			define( 'DOCUMENTROOT' , $_SERVER['DOCUMENT_ROOT'] );
		endif;

		if( !defined('HOMEDIR') ):
			define( 'HOMEDIR' , dirname($_SERVER['DOCUMENT_ROOT']) );
		endif;
		
		if( !defined('DS') ):
			define( 'DS', DIRECTORY_SEPARATOR );
		endif;

		define( 'CONFIG_DIR' , '/config' );
		
	    define( 'MULTI_LANGUAGES_DIR' , dirname(__FILE__) );

	    define( 'WPCONTENT_M17N_CONFIG_DIR' , WP_CONTENT_DIR . '/012-m17n-config' );

		if ( !$this->ps_012_m17n_include_once_or_notices( )):
	    	return;
	    endif;    
	    
		if ( !$this->multilingual || !is_array( $this->multilingual ) ){
			add_action('admin_notices', array(&$this,'admin_notices_multilingual'));
			return;	
		}
			    
	    $this->init_mulit_languages( );
	    
		$this->Start( );
		
	}	
	
	/**
	* ファンクション名：Start
	* 機能概要：プラグインの機能実行をスタート 
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param なし
	* @return なし
	*/
	function Start( ){
	
		if ( is_admin( ) ):
			//管理画面カスタマイズ
			add_action( 'admin_init'									, array( &$this , 'admin_multilingual_edit' ) 				);
			//タクソノミークラス、投稿編集クラス、一般設定クラスの読み込み
			add_action( 'admin_init'									, array( &$this , 'admin_multilingual_includes' ) 			);
			//サイトのタイトルとキャッチフレーズを多国語対応
			add_action( 'admin_init'									, array( &$this, 'regist_contact_info_field' ) 				);
			add_filter( 'whitelist_options'								, array( &$this, 'add_contact_info_field' ) 				);
			//JSとCSSを読み込みします。
			//add_action( 'admin_footer-post.php'							, array( &$this, 'add_admin_print_styles' ) );
			add_action( 'admin_print_styles-edit-tags.php'				, array( &$this, 'add_admin_print_styles' ) 				);			
		else:
			add_filter('pre_get_posts'									, array( &$this , 'ps_multilingual_pre_get_posts' ) );
			//add_action( 'init'											, array( &$this, 'ps_init_multilingua' ) 				);
			//カテゴリー、タグ、タクソノミー
			add_filter( 'get_terms' 									, array( &$this, 'ps_multilingual_get_terms') 		, 10 , 3);
			add_filter( 'get_the_terms' 								, array( &$this, 'ps_multilingual_get_the_terms') 	, 10 , 3);
			add_filter( 'get_term' 										, array( &$this, 'ps_multilingual_get_term') 		, 10 , 2);
			
			//言語コード
			add_filter('language_attributes'							, array( &$this, 'ps_language_attributes_multilingual')		);
			//サイト名と
			add_filter('bloginfo'										, array( &$this, 'ps_bloginfo_multilingual') 		, 10 , 2);
			/***********/
			add_filter('blog_details'									, array( &$this, 'ps_blog_details_multilingual') 		, 10);
			
			//
			add_filter('locale'											, array( &$this, 'ps_locale_multilingual') 					);

			add_filter('wp_list_pages'									, array( &$this, 'ps_wp_list_page_multilingual') 			);
			add_filter('wp_nav_menu'									, array( &$this, 'ps_wp_nav_menu_multilingual') 	, 10 , 2);
			add_filter('wp_page_menu'									, array( &$this, 'ps_wp_nav_menu_multilingual') 	, 10 , 2);
			//add_filter('language_attributes'							, array(&$this,'reset_language_attributes') );
			
			add_filter('wp_title' 										, array( &$this, 'ps_wp_title_multilingual' ) 		, 10 , 2);
			add_filter('single_post_title' 								, array( &$this, 'ps_single_post_title_multilingual'),10 , 2);
			add_filter('the_posts'										, array( &$this, 'ps_012_m17n_the_posts')			, 10 , 2);
			
			//検索対応
			add_filter('pre_get_posts'									, array( &$this , 'ps_012_m17n_search' ) 					);
			
			
		endif;
		
		$this->init_view_mulit_lang( );		
	}

	/**
	* ファンクション名：ps_012_m17n_include_once_or_notices
	* 機能概要： check confing file and include
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param なし
	* @return なし
	*/
	function  ps_012_m17n_include_once_or_notices( ){
		if ( is_multisite( ) ):
			global $blog_id;
			if (file_exists( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php' ) ):
				include_once ( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php' );
				return true;
			endif;
			if ( file_exists( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config.php')):
				include_once ( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config.php' );
	    		return true;
			endif;
			if (file_exists( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php' ) ):
	    		include_once ( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php' );
				return true;
			endif;
			if ( file_exists( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config.php')):
	    		include_once ( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config.php');
	    		return true;
			endif;
		else:
			if( file_exists( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config.php') ):
	    		include_once ( WPCONTENT_M17N_CONFIG_DIR . CONFIG_DIR . DS .'config.php' );
	    		return true;
			endif;
			if( file_exists( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config.php') ):
	    		include_once ( MULTI_LANGUAGES_DIR . CONFIG_DIR . DS .'config.php');
	    		return true;
			endif;

	    endif;
	    
	    add_action('admin_notices', array(&$this,'custom_taxonomy_admin_notices'));
	    return false;
	}
	/**
	* ファンクション名：init_mulit_languages
	* 機能概要：デフォルトのロード言語を設定する()
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param なし
	* @return なし
	*/
	function init_mulit_languages( ){

		if( defined('MULTILINGUAL_WPLANG') ):
			$this->WPLANGKEY = MULTILINGUAL_WPLANG;
			//$this->WPLANGKEY = WPLANG ;
		elseif( defined('WPLANG') ):
			$this->WPLANGKEY = WPLANG;
		else:
			$WPLANG = get_option('WPLANG' , true);
			if( $WPLANG ):
				$this->WPLANGKEY = $WPLANG ;
			else:
				$this->WPLANGKEY = 'en' ;
			endif;
		endif;
		
		$this->WPLANGKEY = strtolower( substr( $this->WPLANGKEY, 0, 2 ) );
		unset( $this->multilingual[$this->WPLANGKEY] );

	}
		
	/**
	* ファンクション名：admin_multilingual_includes
	* 機能概要：管理画面の多国語化タクソノミーとポスト
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function admin_multilingual_includes( ){
	    //新規編集する場合、多国語化
	    include_once ( MULTI_LANGUAGES_DIR . '/includes/ps-multilingual-edit-post.php');
		
		//タクソノミー管理画面の多国語化
	    include_once ( MULTI_LANGUAGES_DIR . '/includes/ps-multilingual-taxonomy.php');

	}
	
	/**
	* ファンクション名：init_view_mulit_lang
	* 機能概要：フォロートの表示ロード言語
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function init_view_mulit_lang( ){
		
		if ( $_GET['lang'] ):
		
			$this->set_load_lang( $_GET['lang'] );
			
			setcookie( '_ps_enqueue_language', $this->get_load_lang() , time() + 31536000 , "/");

		elseif( $_COOKIE['_ps_enqueue_language'] ):
			
			$this->set_load_lang( $_COOKIE['_ps_enqueue_language'] );
			
		else:
		
			#-------------------------------------------------------------------------------------------------
			$UserLanguage = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] ); #ユーザー言語の取得
			#-------------------------------------------------------------------------------------------------
	
			$language = array_shift($UserLanguage);
	
			$language = $language ? substr( $language, 0, 2 ) : $language ;

			$this->set_load_lang( $language );
			
			setcookie( '_ps_enqueue_language', $this->get_load_lang() , time() + 31536000 , "/");
			
		endif;
		
	}
	
	function ps_init_multilingua( ){

	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_admin_print_styles( ){

		wp_enqueue_script( 'prefix-js-' . strtolower(__CLASS__) , plugins_url('js/prefix-js.js', __FILE__) );

		wp_register_style( 'prefix-style-'. strtolower(__CLASS__) , plugins_url('css/prefix-style.css', __FILE__) );
		
		wp_enqueue_style( 'prefix-style-' . strtolower(__CLASS__) );
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function ps_locale_multilingual( $locale ){
		
		$multilingual = $this->get_load_lang();
		
		$multilingual_locale = $this->multilingual_code[$multilingual];
		
		return $multilingual_locale ? $multilingual_locale : 'en_US';
	
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/	
	function ps_bloginfo_multilingual( $show = '', $filter = '' ){
		
		$multilingual = $this->get_load_lang( );
		
		if ( $multilingual == $this->WPLANGKEY && $filter != 'keyword' ){
			return $show;
		}
		
		if ( $filter == 'name' || $filter == 'blogname'):
			$show = $this->get_multilingual_option( $multilingual , 'blogname' );
		elseif ( $filter == 'description' || $filter == 'blogdescription' ):
			$show = $this->get_multilingual_option( $multilingual , 'blogdescription' );
		elseif ( $filter == 'keyword' ):
			$show = $this->get_multilingual_option( $multilingual , $filter );
		endif;
		
		return $show;
	}
	
	/**
	* ファンクション名：ps_blog_details_multilingual
	* 機能概要：子サイトのサイト名を該当言語にする
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param Object $details
	* @param Object $details
	* @return
	*/	
	function ps_blog_details_multilingual( $details ){
		$multilingual = $this->get_load_lang( );

		if ( $multilingual == $this->WPLANGKEY ){
			return $details;
		}
		
		if ( is_multisite( ) ):
			global $blog_id;
			$blogname = $this->get_multilingual_option( $multilingual , 'blogname' );
			
			if ( $blogname && $blog_id == $details->blog_id ) {
				$details->blogname = $blogname;	
			}
		endif;
		
		return $details;		
		
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/	
	function get_multilingual_option( $lang, $key ){

		if ( $key  ){
			$option = get_option( $key . '_' . $lang );
		}
		if ( $option ){
			return $option;
		}
		
		return get_option( $key );
		
	}

	/**
	* ファンクション名：ps_language_attributes_multilingual
	* 機能概要：<html dir="ltr" lang="言語コード">の設定
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function ps_language_attributes_multilingual( $output = null ){
		$attributes = array();
		$doctype = 'html';

		if ( preg_match('|xml|' , $output)){
			$doctype = 'xhtml';
		}

		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $output;
		}
	
		if ( function_exists( 'is_rtl' ) )
			$attributes[] = 'dir="' . ( is_rtl() ? 'rtl' : 'ltr' ) . '"';
	
		$lang = get_bloginfo('language');
		if ( $lang_code ):
			$lang = str_replace('_', '-', $lang_code);
		endif;

		if ( $lang ) :
			if ( get_option('html_type') == 'text/html' || $doctype == 'html' )
				$attributes[] = "lang=\"$lang\"";
	
			if ( get_option('html_type') != 'text/html' || $doctype == 'xhtml' )
				$attributes[] = "xml:lang=\"$lang\"";
		endif;
		
		$output = implode(' ', $attributes);
		
		$output = apply_filters('regist_language_attributes', $output);
		
		echo $output;
	}

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function get_multilingual_code( $lang ){
		
		if ( $lang ):
			$lang_code = $this->multilingual_code[$lang];
			$lang_code = str_replace('_', '-', $lang_code);
		else:
			$lang_code = $this->multilingual_code[$this->WPLANGKEY];
			$lang_code = str_replace('_', '-', $lang_code);
		endif;	
		return $lang_code;
	}
	
	/**
	* ファンクション名：ps_012_m17n_the_posts
	* 機能概要： 投稿、ページ、カスタム投稿タイプのタイトルと内容を該当言語にする
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param Object $posts
	* @param Object $query_this
	* @return Object $posts
	*/
	function ps_012_m17n_the_posts( $posts , $query_this){


		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $posts;
		}

		if ( !$posts ){
			return $posts;
		}
	
		foreach ( $posts as $key => $post){
			$multilingual_Name = get_post_meta( $post->ID, 'post_content_' . $this->load_lang , true);
			
			if ( $multilingual_Name ):
				$post->post_content =  $multilingual_Name;
			endif;
			
			$multilingual_title = get_post_meta( $post->ID, 'post_title_' . $this->load_lang , true);
			
			if ( $multilingual_title ):
				$post->post_title =  $multilingual_title;
			endif;
			$post->post_content = preg_replace('|<p>&gt;</p>|' , '' , $post->post_content );
			$post->post_content = preg_replace('|&gt;|' , '' , $post->post_content );		
		}
		
		return $posts;
		
	}
	
	/**
	* ファンクション名：ps_single_post_title_multilingual
	* 機能概要： 投稿、ページ、カスタム投稿タイプのタイトルを該当言語にする
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $title
	* @param Object $_post
	* @return String $title
	*/
	function ps_single_post_title_multilingual( $title , $_post ){
		
		if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
			
			$multilingual_title = get_post_meta( $_post->ID, 'post_title_' . $this->load_lang , true);
			
			if ( $multilingual_title ):
				$title =  $multilingual_title;
			endif;					
		}
		
		return $title;
	}
	
	/**
	* ファンクション名：ps_012_m17n_search
	* 機能概要：多国化の検索対応
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param Array $pre
	* @return Array $pre 
	*/	
	function ps_012_m17n_search( $pre ){
		global $blog_id, $wpdb;
		if ( !is_search() ){
		    return $pre;
		}
		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $pre;
		}
		
		$lang =  $this->get_load_lang();
	    if ( $lang ){
	        $meta_query = array(
	            array(
	                'key'  => 'post_title_' . $lang,
	                'value' => $pre->query_vars['s'],
	                'compare' => 'LIKE'
	            ), 
	            array(
	                'key'  => 'post_content_' . $lang,
	                'value' => $pre->query_vars['s'],
	                'compare' => 'LIKE'
	            ),
	            'relation'=>'OR'
	         );
	        $teest = $pre->query_vars['s'];
	        $pre->query_vars['s'] = "";
	        $pre->query_vars['meta_query'] = $meta_query;
	        //add_filter('posts_where', array( &$this , 'ps_search_where' ));
	    }
	    		
		return $pre;
	}
	
	/**
	* ファンクション名：ps_wp_title_multi_lang
	* 機能概要：多国語のタイトルを表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param String $title（デフォルト語のタイトル）
	* @return String $title(多国語タイトル)
	*/
	function ps_wp_title_multilingual( $title, $mark = '' ){
		
		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $title;
		}
		
		if ( is_search() ):
		
			$lang = $this->get_load_lang();
			
			$searchResults = $this->search_multilingual_title[$lang];
			
			if ( $searchResults ){
				$preg_quote = preg_quote($mark,'|');
				$title = preg_replace('|' . $preg_quote .'(.*)'. $preg_quote .'|', $mark . ' ' . $searchResults . ' ' . $mark , $title);
			}
			
			if ( $_GET['s'] ):
				return $_GET['s'] . $title;
			elseif ( $_POST['s'] ):
				return  $_POST['s']  . $title;
			endif;
		endif;
		return $title;
	}
		
	/**  
	 * ファンクション名：ps_wp_nav_menu_multilingual
	 * 機能概要：wp_nav_menuの表示タイトルを該当言語にする
	 * 作成：プライム・ストラテジー株式会社 王 濱
	 * 変更：
	 * @param   Array $nav_menu
	 * @param   Array $args
	 * @return  Array 全$nav_menu
	 */
	function ps_wp_nav_menu_multilingual( $nav_menu, $args  ) {

		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $nav_menu;
		}
		
		$aliases = $this->get_multilingual_title( );
		if ( $aliases ):
			foreach ( $aliases as $alias  ):
				$nav_menu = preg_replace( '/>' . preg_quote( trim($alias['post_title']), '/' ) . '</', '>' . $alias['meta_value'] . '<',  $nav_menu );
			endforeach;
		endif;
		
		return $nav_menu;
	}
	
	/**  
	 * ファンクション名：ps_wp_list_page_multilingual
	 * 機能概要：wp_list_pageの表示タイトルを該当言語にする
	 * 作成：プライム・ストラテジー株式会社 王 濱
	 * 変更：
	 * @param   Array $global_navi
	 * @return  Array 全$global_navi
	 */
	function ps_wp_list_page_multilingual( $global_navi ){

		if ( $this->get_load_lang() == $this->WPLANGKEY  ){
			return $global_navi;
		}
		
		$aliases = $this->get_multilingual_title( );

		if ( $aliases ):
			foreach( $aliases as $alias):
				$global_navi = preg_replace( '/>' . preg_quote( trim($alias['post_title']), '/' ) . '</', '>' . $alias['meta_value'] . '<',  $global_navi );
			endforeach;
		endif;
		return $global_navi;
	}

	/**  
	 * ファンクション名：get_multilingual_title
	 * 機能概要：全カスタムフィールドを取得
	 * 作成：プライム・ストラテジー株式会社 王 濱
	 * 変更：
	 * @param   String post_id
	  * @return  Array 全カスタムフィールド
	 */
	function get_multilingual_title( ){
		global $wpdb;
		$sql ="
		SELECT	a.`post_title`,
				b.`meta_value`
		FROM	`$wpdb->posts` as a,
				`$wpdb->postmeta` as b
		WHERE	a.`ID` = b.`post_id`
		AND		b.`meta_key` = 'post_title_{$this->get_load_lang()}'";
		
		if ( $this->get_load_lang() != $this->WPLANGKEY ):
			$aliases = $wpdb->get_results( $sql, 'ARRAY_A' );
		endif;
		
		return $aliases;
		
	}
	
	/**  
	 * ファンクション名：get_ps_post_customs
	 * 機能概要：全カスタムフィールドを取得
	 * 作成：プライム・ストラテジー株式会社 王 濱
	 * 変更：
	 * @param   String post_id
	  * @return  Array 全カスタムフィールド
	 */
	function get_ps_post_customs( $post_id  ){   
	    $custom_fields = get_post_custom($post_id);
	    $return = array();  
	    foreach( $custom_fields as $key => $field ){
	    	if ( count( $field ) > 1 ){
	    		foreach ( $field as $key2 => $val  ){
	    			$return[$key][$key2] = $val;
	    		}
	    	}else{
	    		if ( preg_match('/^_(.*)/' , $key  )){
	        		$return[$key] = maybe_unserialize($field[0]) ;
	    		}else{
	    			$return[$key] = $field[0];
	    		}
	    	}
	    }
	    return $return;
	}


	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function ps_multilingual_get_terms ( $terms, $taxonomies, $args ){

		$lang = $this->get_load_lang( ); 
		
		$current_option = get_option( $taxonomies[0] . '-' . $lang );

		foreach ( $terms as $key => $term ):
			if ( $this->WPLANGKEY != $lang ):
				$multilingual_Name = $current_option[$term->term_id];
				if ( $multilingual_Name ):
					$terms[$key]->name = is_array($multilingual_Name) ? join(',', $multilingual_Name) :  $multilingual_Name ;
				endif;
			endif;
						
		endforeach;

		return $terms;
	}

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function ps_multilingual_get_the_terms( $terms, $id, $taxonomy ){
		$lang = $this->get_load_lang( ); 
	
		$current_option = get_option( $taxonomy . '-' . $lang );
		
		foreach ( $terms as $key => $term ):
			if ( $this->WPLANGKEY != $lang ):
				$multilingual_Name = $current_option[$term->term_id];
				if ( $multilingual_Name ):
					$terms[$key]->name = is_array($multilingual_Name) ? join(',', $multilingual_Name) :  $multilingual_Name ;
				endif;
			endif;
						
		endforeach;

		return $terms;	
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/	
	function ps_multilingual_get_term( $term, $taxonomy ){
		$lang = $this->get_load_lang( );
		$current_option = get_option( $taxonomy . '-' . $lang );

		$multilingual_Name = $current_option[$term->term_id];

		if ( $this->WPLANGKEY != $lang ):
			if ( $multilingual_Name ):
				$term->name = is_array($multilingual_Name) ? join(',', $multilingual_Name) :  $multilingual_Name ;
			endif;
		endif;
		
		return $term;
		
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function regist_contact_info_field() {
		
		$flag_icon = $this->flags_dir . $this->WPLANGKEY . '.png';
			if ( ps_url_exists( $flag_icon )):
				$flag_icon = '<img src="' .  $flag_icon. '">&nbsp; ';	
			endif;
		add_settings_field( 'hr_' . $this->WPLANGKEY , $flag_icon   , array($this , 'display_option_general_hr' ), 'general' , 'default' , array($this->WPLANGKEY));
		
		add_settings_field( 'keyword_' . $this->WPLANGKEY ,  __('Keyword')  , array($this , 'display_keyword_info' ), 'general' , 'default' , array($this->WPLANGKEY));

		foreach ( $this->multilingual as $key => $lang ):
			$flag_icon = $this->flags_dir . $key . '.png';
			if ( ps_url_exists( $flag_icon )):
				$flag_icon = '<img src="' .  $flag_icon. '">&nbsp; ';	
			endif;
			add_settings_field( 'hr_' . $key , $flag_icon  .  $lang  , array($this , 'display_option_general_hr' ), 'general' , 'default' , array($key));
			add_settings_field( 'blogname_' . $key ,  __('Site Title')  , array($this , 'display_blogname_info' ), 'general' , 'default' , array($key));
			add_settings_field( 'blogdescription_' . $key , __('Site Tagline')  , array($this , 'display_blogdescription_info' ), 'general' , 'default' , array($key));
			add_settings_field( 'keyword_' . $key , __('Keyword')  , array($this , 'display_keyword_info' ), 'general' , 'default' , array($key));
		endforeach;
		
	}

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function display_blogname_info( $lang ) {
		$lang = $lang[0];
		$blogname_lang = get_option( 'blogname_' . $lang );
?>
		<input type="text" id="blogname_<?php echo $lang;?>"  name="blogname_<?php echo $lang;?>" value="<?php echo esc_html( $blogname_lang ); ?>" class="regular-text">
<?php
	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function display_option_general_hr( $lang ){

	}
	
	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function display_blogdescription_info( $lang ) {
		$lang = $lang[0];
		$blogdescription_lang = get_option( 'blogdescription_' . $lang );
?>
				<input type="text" id="blogdescription_<?php echo $lang;?>"  name="blogdescription_<?php echo $lang;?>" value="<?php echo esc_html( $blogdescription_lang ); ?>" class="regular-text">
<?php
	}

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function display_keyword_info( $lang ) {
		$lang = $lang[0];
		$keyword_lang = get_option( 'keyword_' . $lang );
?>
		<input type="text" id="keyword_<?php echo $lang;?>"  name="keyword_<?php echo $lang;?>" value="<?php echo esc_html( $keyword_lang ); ?>" class="regular-text">
<?php
	}	

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_contact_info_field( $whitelist_options ) {

		foreach ( $this->multilingual as $key => $lang ):
			$whitelist_options['general'][] = 'blogname_' . $key;
			$whitelist_options['general'][] = 'blogdescription_' . $key;
			$whitelist_options['general'][] = 'keyword_' . $key;
		endforeach;
		$whitelist_options['general'][] = 'keyword_' . $this->WPLANGKEY;	
		return $whitelist_options;
	}
	
	/**
	* ファンクション名：admin_multilingual_edit
	* 機能概要：管理画面のカスタマイズ処理
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function admin_multilingual_edit( ){
		
		//カスタマイズ処理国旗のディレクトリを取得する
		$this->flags_dir = plugins_url('flags/', __FILE__);
		
	}
	
	/**
	* ファンクション名：ps_012_multilingual_list
	* 機能概要：多国語の切替リンクを設定する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $lang
	* @return String $snippet
	*/
	function ps_012_multilingual_list( $lang = null ){
		if ( !$lang ){
			$lang = $this->get_load_lang();
		}
		$snippet = array();
		$request_uri = $_SERVER['REQUEST_URI'];
	
		foreach ( $this->multilingual_code as $key => $load_lang ){
				$snippet[$key]['name'] =  $this->format_code_lang( $key );// == 'ja' ? '日本' : strtoupper($key);
				$snippet[$key]['url'] = $this->get_multilingual_request_uri($request_uri, $key );
				$snippet[$key]['current'] = $key == $lang ? ' current' : '';
		}
		return $snippet;
	}
	
	/**
	* ファンクション名：get_multilingual_request_uri
	* 機能概要：多言語のリクエストのURLを作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $url URL
	* @param string $lang 言語コード
	* @return url 再設定URL
	*/
	function get_multilingual_request_uri($url ,  $lang ){
		
		if ( preg_match( '|\?lang='. $lang .'|' , $url) ||  preg_match( '|&lang='. $lang .'|' , $url ) ){
			return $url;
		}elseif( preg_match( '|\?lang=[\w]{2}|' , $url) ){
			return preg_replace('|(\?)lang=([\w]{2})|' , "$1lang=" . $lang , $url);
		}elseif( preg_match( '|&lang=[\w]{2}|' , $url) ){
			return preg_replace('|(&)lang=([\w]{2})|' , "$1lang=" . $lang , $url);
		}else{
			if ( preg_match('|\?[\w]+=|', $url, $m )){
				return $url . '&lang=' . $lang;
			}else{
				return $url . '?lang=' . $lang;
			}
		}
	}

	/**
	* ファンクション名：
	* 機能概要：
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function format_code_lang( $code = '' ) {
		$code = strtolower( substr( $code, 0, 2 ) );
		$lang_codes = array(
			'aa' => 'Afar', 'ab' => 'Abkhazian', 'af' => 'Afrikaans', 'ak' => 'Akan', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese', 'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan', 'ay' => 'Aymara', 'az' => 'Azerbaijani', 'ba' => 'Bashkir', 'bm' => 'Bambara', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali',
			'bh' => 'Bihari', 'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian', 'my' => 'Burmese', 'ca' => 'Catalan; Valencian', 'ch' => 'Chamorro', 'ce' => 'Chechen', 'zh' => 'Chinese', 'cu' => 'Church Slavic; Old Slavonic; Church Slavonic; Old Bulgarian; Old Church Slavonic', 'cv' => 'Chuvash', 'kw' => 'Cornish', 'co' => 'Corsican', 'cr' => 'Cree',
			'cs' => 'Czech', 'da' => 'Danish', 'dv' => 'Divehi; Dhivehi; Maldivian', 'nl' => 'Dutch; Flemish', 'dz' => 'Dzongkha', 'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe', 'fo' => 'Faroese', 'fj' => 'Fijjian', 'fi' => 'Finnish', 'fr' => 'French', 'fy' => 'Western Frisian', 'ff' => 'Fulah', 'ka' => 'Georgian', 'de' => 'German', 'gd' => 'Gaelic; Scottish Gaelic',
			'ga' => 'Irish', 'gl' => 'Galician', 'gv' => 'Manx', 'el' => 'Greek, Modern', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian; Haitian Creole', 'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi', 'ho' => 'Hiri Motu', 'hu' => 'Hungarian', 'ig' => 'Igbo', 'is' => 'Icelandic', 'io' => 'Ido', 'ii' => 'Sichuan Yi', 'iu' => 'Inuktitut', 'ie' => 'Interlingue',
			'ia' => 'Interlingua (International Auxiliary Language Association)', 'id' => 'Indonesian', 'ik' => 'Inupiaq', 'it' => 'Italian', 'jv' => 'Javanese', 'ja' => 'Japanese', 'kl' => 'Kalaallisut; Greenlandic', 'kn' => 'Kannada', 'ks' => 'Kashmiri', 'kr' => 'Kanuri', 'kk' => 'Kazakh', 'km' => 'Central Khmer', 'ki' => 'Kikuyu; Gikuyu', 'rw' => 'Kinyarwanda', 'ky' => 'Kirghiz; Kyrgyz',
			'kv' => 'Komi', 'kg' => 'Kongo', 'ko' => 'Korean', 'kj' => 'Kuanyama; Kwanyama', 'ku' => 'Kurdish', 'lo' => 'Lao', 'la' => 'Latin', 'lv' => 'Latvian', 'li' => 'Limburgan; Limburger; Limburgish', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lb' => 'Luxembourgish; Letzeburgesch', 'lu' => 'Luba-Katanga', 'lg' => 'Ganda', 'mk' => 'Macedonian', 'mh' => 'Marshallese', 'ml' => 'Malayalam',
			'mi' => 'Maori', 'mr' => 'Marathi', 'ms' => 'Malay', 'mg' => 'Malagasy', 'mt' => 'Maltese', 'mo' => 'Moldavian', 'mn' => 'Mongolian', 'na' => 'Nauru', 'nv' => 'Navajo; Navaho', 'nr' => 'Ndebele, South; South Ndebele', 'nd' => 'Ndebele, North; North Ndebele', 'ng' => 'Ndonga', 'ne' => 'Nepali', 'nn' => 'Norwegian Nynorsk; Nynorsk, Norwegian', 'nb' => 'Bokmål, Norwegian, Norwegian Bokmål',
			'no' => 'Norwegian', 'ny' => 'Chichewa; Chewa; Nyanja', 'oc' => 'Occitan, Provençal', 'oj' => 'Ojibwa', 'or' => 'Oriya', 'om' => 'Oromo', 'os' => 'Ossetian; Ossetic', 'pa' => 'Panjabi; Punjabi', 'fa' => 'Persian', 'pi' => 'Pali', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ps' => 'Pushto', 'qu' => 'Quechua', 'rm' => 'Romansh', 'ro' => 'Romanian', 'rn' => 'Rundi', 'ru' => 'Russian',
			'sg' => 'Sango', 'sa' => 'Sanskrit', 'sr' => 'Serbian', 'hr' => 'Croatian', 'si' => 'Sinhala; Sinhalese', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'se' => 'Northern Sami', 'sm' => 'Samoan', 'sn' => 'Shona', 'sd' => 'Sindhi', 'so' => 'Somali', 'st' => 'Sotho, Southern', 'es' => 'Spanish; Castilian', 'sc' => 'Sardinian', 'ss' => 'Swati', 'su' => 'Sundanese', 'sw' => 'Swahili',
			'sv' => 'Swedish', 'ty' => 'Tahitian', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu', 'tg' => 'Tajik', 'tl' => 'Tagalog', 'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tonga (Tonga Islands)', 'tn' => 'Tswana', 'ts' => 'Tsonga', 'tk' => 'Turkmen', 'tr' => 'Turkish', 'tw' => 'Twi', 'ug' => 'Uighur; Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu', 'uz' => 'Uzbek',
			've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volapük', 'cy' => 'Welsh','wa' => 'Walloon','wo' => 'Wolof', 'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang; Chuang', 'zu' => 'Zulu' );
		$lang_codes = apply_filters( 'format_code_lang', $lang_codes, $code );
		return strtr( $code, $lang_codes );
	}

	/**
	* ファンクション名：custom_taxonomy_admin_notices
	* 機能概要：設定ファイルなし、警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function custom_taxonomy_admin_notices(){
		echo '<div class="error" style="text-align: center;"><p style="color: red; font-size: 14px; font-weight: bold;">プラグイン012 PS Multi Languages :設定ファイル<strong>_config.php</strong>の名前を<strong>config.php OR config-{$blog_id}.php</strong>に変更し、<strong>configファイル</strong>の設定を行ってください。</p></div>';
	}
	
	/**
	* ファンクション名：admin_notices_multilingual
	* 機能概要：設定ファイルあり、$ultilingualがない場合警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function admin_notices_multilingual(){
		echo '<div class="error" style="text-align: center;"><p style="color: red; font-size: 14px; font-weight: bold;">プラグイン012 PS Multi Languages:設定ファイル<strong>config.php OR config-{$blog_id}.php</strong>の多国語設定配列($multilingual)の設定を行ってください。</p></div>';
	}
	
	/**
	* ファンクション名：get_bread_crumb_array
	* 機能概要：パンくずナビのデータを作成
	* 作成：
	* 変更：
	* @param void
	* @return Array $bread_crumb_arr
	*/	
	function get_bread_crumb_array() {
		global $post,$term , $wp_post_types , $blog_id , $all_post_type , $wp_query ;
		
		$bread_crumb_arr = array();
		
		$multilingual = $this->get_load_lang( );
		
		$default_ml = $this->WPLANGKEY;
	
		//  if ( $multilingual == $default_ml ){
		
		if ( is_multisite() && false ){
			$site=get_blog_details($blog_id);
			$site_url = get_bloginfo( 'url' );
			$bread_crumb_arr[] = array( 'title' => 'Top Home' , 'link' =>  '/' );
			$bread_crumb_arr[] = array( 'title' => $site->blogname , 'link' => $site_url . '/' );
		}else{
			$front_page_id = get_option( 'page_on_front' );
		
			if ( $front_page_id > 0 ){
				$front_page = get_page( $front_page_id );
				if ( $multilingual == $default_ml ){
					$front_page_title =  $front_page->post_title;
				}else{
					$front_page_title = get_post_meta( $front_page_id , 'post_title_' . $multilingual , true);
					if ( !$front_page_title ){
						$front_page_title =  $front_page->post_title;	
					}
				}
			}else{
				$front_page_title = 'Home';
			}
			$bread_crumb_arr[] = array( 'title' => $front_page_title , 'link' =>  '/' );
		}

		
		if ( is_404() ) {
			$bread_crumb_arr[] = array( 'title' =>  'No results found.' , 'link' => false );
		} elseif ( is_search() || ( isset( $_GET['s'] ) && $_GET['s'] == false )) {
			$search_results = $this->search_multilingual_title[$multilingual];
			if ( $search_results ){
				$bread_crumb_arr[] = array( 'title' => sprintf( $search_results . ' &#8220;%s&#8221;' , $_GET['s'] ), 'link' => false );
			}else{
				$bread_crumb_arr[] = array( 'title' => sprintf( __('Search results for &#8220;%s&#8221;')  , $_GET['s'] ), 'link' => false );
			}
		} elseif ( is_tax() ) {//oh start 2011/08/04
				//カスタム投稿タイプを取得する
				//$post_type =  get_post_type(); 
				//クエリからtaxonomy(カスタム分類タクソノミー)のslug取
				$taxonomy = get_query_var( 'taxonomy' );

				//termの情報を取得
				$term_object = get_term_by( 'slug', get_query_var( 'term' ), $taxonomy );
	
				if ( is_taxonomy_hierarchical( $taxonomy ) && $term_object->parent != 0 ) {
			        $ancestors = array_reverse( get_ancestors( $term_object->term_id, $taxonomy ) );
			        foreach ( $ancestors as $ancestor_id ) {
			            $ancestor = get_term( $ancestor_id, $taxonomy );
			            $bread_crumb_arr[] = array( 'title' => $ancestor->name, 'link' => get_term_link( $ancestor, $term) );
			        }
			    }
		   		$bread_crumb_arr[] = array( 'title' => $term_object->name , 'link' => get_term_link( $term_object, $term ) );
			
		} elseif ( is_home() || is_front_page() ) {
		} elseif ( is_attachment() ) {
			if ( $post->post_parent ) {
				if ( $parent_post = get_post( $post->post_parent ) ) {
					if ( $multilingual == $default_ml ){
						$parent_post_title =  $parent_post->post_title;
					}else{
						$parent_post_title = get_post_meta( $parent_post->ID , 'post_title_' . $multilingual , true);
						if ( !$front_page_title ){
							$parent_post_title =  $parent_post->post_title;
						}
					}
				}
				$bread_crumb_arr[] = array( 'title' => $parent_post->post_title, 'link' => get_permalink( $parent_post->ID ) );
			}
			$bread_crumb_arr[] = array( 'title' => $post->post_name, 'link' => get_permalink( $post->ID ) );

		} elseif ( is_single() ) {
	
			//カスタム投稿タイプを取得する
			$post_type =  get_post_type(); 
			
			$taxonomy = get_query_var( 'taxonomy' );
		
			//$taxonomy = ( in_array($post->post_type,array('post','attachment')) ? 'category':$post->post_type . '-category');
			$post_term = get_the_terms($post->ID,$taxonomy); 

			$post_type_taxonomies = get_object_taxonomies( $post_type, false );
			
			if ( is_array( $post_type_taxonomies ) && count( $post_type_taxonomies ) ) {
				foreach( $post_type_taxonomies as $tax_slug => $taxonomy ) {
					if ( $taxonomy->hierarchical ) {
						$terms = get_the_terms( $post->ID, $tax_slug );
						if ( $terms ) {
							$post_term = array_shift( $terms );
							$taxonomy = $post_term->taxonomy;
							break;
						}
					}
				}
			}
			
			if ( !$post_term || is_wp_error($post_term)){
				$default_category = get_option( $post_type .  '_default_category');
				$post_term = get_term(  $default_category , $taxonomy);
			}

			if ( is_taxonomy_hierarchical( $taxonomy ) && $post_term->parent != 0 ) {
		        $ancestors = array_reverse( get_ancestors( $post_term->term_id, $taxonomy ) );
		        foreach ( $ancestors as $ancestor_id ) {
		            $ancestor = get_term( $ancestor_id, $taxonomy );
	
		           	if (!in_array($post->post_type,array('post','attachment'))){
		            	 $bread_crumb_arr[] = array( 'title' => $ancestor->name , 'link' => get_term_link( $ancestor, $post_term->slug ) );
		           	}else{
		           		$bread_crumb_arr[] = array( 'title' => $ancestor->name , 'link' => get_term_link( $ancestor, $post_term->slug ) );
		           	}
		        }
		    }
	
		    if ( $post_term && !$post_term->errors){
				$bread_crumb_arr[] = array( 'title' => $post_term->name, 'link' => get_term_link( $post_term, $post_term->slug ));
		    }

		    if ( $multilingual != $default_ml ){
	       		$multi_lang_Name = get_post_meta( $post->ID, 'post_title_' . $multilingual , true);
	        	$post->post_title = $multi_lang_Name ? $multi_lang_Name : $post->post_title;
		    }
	        
			$bread_crumb_arr[] = array( 'title' => $post->post_title , 'link' => get_permalink( $post->ID ) );
					
		} elseif ( is_page() ) {
			$ancestors = array_reverse(get_post_ancestors( $post ) );
			$ancestor_posts = get_posts( 'post_type=page&include=' . implode( ',', $ancestors ) );
			foreach( $ancestors as $ancestor ) {
				foreach ( $ancestor_posts as $ancestor_post ) {
					if ( $ancestor == $ancestor_post->ID ) {
						if ( $multilingual != $default_ml ){
				       		$multi_lang_Name = get_post_meta( $ancestor_post->ID, 'post_title_' . $multilingual , true);
				        	$ancestor_post->post_title = $multi_lang_Name ? $multi_lang_Name : $ancestor_post->post_title;
						}				
						$bread_crumb_arr[] = array( 'title' => $ancestor_post->post_title , 'link' => get_permalink( $ancestor_post->ID ) );
					}
				}
			}
			$bread_crumb_arr[] = array( 'title' => $post->post_title , 'link' => get_permalink( $post->ID ) );
			
		} elseif ( is_category() ) {
			global $cat;
			
			$category = get_category( $cat );

			if ( $category->parent != 0 ) {
				$ancestors = array_reverse( $this->get_category_ancestors( $category->parent ) );
				foreach ( $ancestors as $ancestor ) {
					$bread_crumb_arr[] = array( 'title' => $ancestor->name , 'link' => get_category_link( $ancestor->term_id ) );
				}
			}
	  		$bread_crumb_arr[] = array( 'title' => $category->name , 'link' => get_category_link( $cat ) );
		} elseif ( is_tag() ) {
			global $tag_id;
			$tag = get_tag( $tag_id );
			$bread_crumb_arr[] = array( 'title' => $tag->name , 'link' => get_tag_link( $tag_id ) );
		} elseif ( is_author() ) {
			global $author;
			$bread_crumb_arr[] = array( 'title' =>  get_author_name( $author ), 'link' => get_author_posts_url( $author ) );
		} elseif ( is_day() ) {
			$year = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
			$day = get_query_var( 'day' );
			$bread_crumb_arr[] = array( 'title' =>  $year , 'link' => get_year_link( $year ) );
			$bread_crumb_arr[] = array( 'title' =>  $month , 'link' => get_month_link( $year, $month ) );
			$bread_crumb_arr[] = array( 'title' =>  $day , 'link' => get_day_link( $year, $month, $day ) );
		} elseif ( is_month() ) {
			$year = get_query_var( 'year' );
			$month = get_query_var( 'monthnum' );
			$bread_crumb_arr[] = array( 'title' =>  $year , 'link' => get_year_link( $year ) );
			$bread_crumb_arr[] = array( 'title' =>  $month , 'link' => get_month_link( $year, $month ) );
		} elseif ( is_year() ) {
			$year = get_query_var( 'year' );
			$bread_crumb_arr[] = array( 'title' =>  $year , 'link' => get_year_link( $year ) );
		}
		return $bread_crumb_arr;
	}
	
	/**
	* ファンクション名：get_bread_crumb_array
	* 機能概要：パンくずナビのデータを作成
	* 作成：
	* 変更：
	* @param void
	* @return Array $bread_crumb_arr
	*/	
	function bread_crumb( $type = 'list', $echo = false ) {
	
		$bread_crumb_arr = $this->get_bread_crumb_array();
	
		if ( $type == 'string' ) {
			$output = array();
			$cnt = 1;
			foreach ( $bread_crumb_arr as $ancestor ) {
				if ( $cnt == count( $bread_crumb ) ) {
					$output[] = '<strong>' . apply_filters( 'the_title', $ancestor['title'] ) . '</strong>';
				} else {
					$output[] = '<a href="' . $ancestor['link'] . '">' . apply_filters( 'the_title', $ancestor['title'] ) . '</a>';
				}
				$cnt++;
			}
			$output = implode( esc_html( $this->settings['disp']['bc_joint_string'] ), $output );
		} else {
			$output = '<ul id="bread_crumb">' . "\n";
			$cnt = 1;
			foreach ( $bread_crumb_arr as $ancestor ) {
	            $classes = array();
	            $classes[] = 'bc_level-' . $cnt;
	            if ( $cnt == 1 ) {
	                $classes[] = 'bc_top';
	            } else {
	                $classes[] = 'bc_sub';
	            }    
	            if ( $cnt == count( $bread_crumb_arr ) ) {
	                $classes[] = 'tail';
	                $output .= '    <li class="' . implode( ' ', $classes ) . '">' . apply_filters( 'the_title', $ancestor['title'] ) . '</li>' . "\n";
	            } elseif( $ancestor['no_link'] === true ){
	                $output .= '    <li class="' . implode( ' ', $classes ) . '">' . apply_filters( 'the_title', $ancestor['title'] ) . '</li>' . "\n";
	            } else {
	                $output .= '    <li class="' . implode( ' ', $classes ) . '"><a href="' . $ancestor['link'] . '" '. $ancestor['target'] .'>' . apply_filters( 'the_title', $ancestor['title'] ) . '</a></li>' . "\n";
	            }    
	            $cnt++;
			}
			$output .= '</ul>' . "\n";
		}
		
		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
	
	function get_category_ancestors( $cat_id, $ancestors = array() ) {
		$cat = get_category( $cat_id );
		$ancestors[] = $cat;
		if ( $cat->parent != 0 ) {
			$ancestors = $this->get_category_ancestors( $cat->parent, $ancestors );
		}
		return $ancestors;
	}

	/**
	* ファンクション名：ps_multilingual_pre_get_posts
	* 機能概要：フィルター処理を行う場合はfalseを指定(the_postsのため)
	* 作成：
	* 変更：
	* @param Object $pre
	* @return Object $pre
	*/	
	function ps_multilingual_pre_get_posts( $pre ){
		$pre->query_vars['suppress_filters'] = false;
		return $pre;
	}
	
}//class end

$ps_multi_languages = new ps_multi_languages( );

if ( isset($ps_multi_languages ) ){
	include_once ( MULTI_LANGUAGES_DIR . '/config/functions.php' );
}
?>
