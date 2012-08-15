<?php
/**
 * edit multilingual post 
 *
 * Main Custom Multilingual for Edit Post include Class
 *
 * Author: Ouhinit(Wangbin)
 * 
 * @package ps_multilingual_edit_post
 */
/*
 * 投稿、ページ、カスタム投稿の編集などを多国語に対応する
 * このクラスの作成に関しては、「custom-field-gui-utility」（CSS）(edit_meta_value)と「Custom Field Template」（tinyMCE）を参考させていただきました。
 *  Tomohiro Okuwaki（Web屋かたつむりくん）さんとHiroaki Miyashitaさんに心より感謝しております。
 */
Class ps_multilingual_edit_post {

	/*
	 * 多国語化カスタムフィールドのprefix
	 */
	var $prefix_meta_keys = array( 'post_title_', 'post_content_' );
	
	/*
	 *コンストラクタ.
	 */
	function __construct( ) {
		$this->init( );
	}
	
	/*
	 * initializing
	 */
	function init( ){
		global $ps_multi_languages;

		if ( !isset( $ps_multi_languages ) ) return;

	    //対応する多国語
   		$this->_items = $ps_multi_languages->multilingual;
   		//国旗画像ディレクトリ
   		$this->flags_dir = $ps_multi_languages->flags_dir;
   		//デフォルトの言語
   		$this->WPLANGKEY = $ps_multi_languages->WPLANGKEY;
   		//デフォルトの一覧の多国語表示個数
   		$this->max_count = $ps_multi_languages->max_count; 

	    $this->Start( );

	}
	
	/*
	 *プラグインの機能実行をスタート 
	 */
	function Start(){
		if ( is_admin( ) ):
			
			//多国語のメタボックスを生成する
			add_action ('add_meta_boxes'							, array(&$this,'insert_multilingual_custom_field') );
			
			/* edit_post : 投稿記事またはページが更新・編集された際に実行する。これには、コメントが追加・更新された場合（投稿またはページのコメント数が更新される）も含む。 */
			add_action('edit_post'									, array(&$this,'edit_meta_value'), 10, 2 );
			
			/* save_post : インポート機能の利用、記事・ページ編集フォームの利用、XMLRPCでの投稿、メールでの投稿のうちいずれかの方法で記事・ページが作成・更新された際に実行する。 */
			add_action( 'save_post'									, array( &$this, 'edit_meta_value'), 10, 2 );
			
			/* publish_post : 投稿記事が公開された際、または公開済みの記事の情報が編集された際に実行する。 */
			add_action( 'publish_post'								, array( &$this, 'edit_meta_value') );
			
			/* transition_post_status : 記事・ページが公開された際、またはステータスが「公開」に変更された場合に実行する。 */
			add_action( 'transition_post_status'					, array( &$this, 'edit_meta_value') );
			
			//投稿を新規と編集する場合、JSファイルをCSSファイルを読み込みする
			add_action( 'admin_print_styles-post.php'       		, array( &$this, 'add_admin_print_styles' ) );
	    	add_action( 'admin_print_styles-post-new.php'   		, array( &$this, 'add_admin_print_styles' ) );
	    	
	    	//投稿、ページ、カスタム投稿の一覧多国語表示
	    	add_filter( 'manage_posts_columns'							, array(&$this,'add_multilingual_posts_columns' ) );
			add_filter( 'manage_page_posts_columns'						, array(&$this,'add_multilingual_posts_columns' ));
			add_action( 'manage_posts_custom_column'					, array(&$this,'add_multilingual_scompt_custom_column'), 10, 2);
			add_action( 'manage_page_posts_custom_column'				, array(&$this,'add_multilingual_scompt_custom_column'), 10, 2);    	
	    	
		endif;
	}
	
	
	/**
	* ファンクション名：add_multilingual_meta_boxes
	* 機能概要：各多国語のメターボックスを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $post_type
	* @param Object $post
	* @return 
	*/	
	function insert_multilingual_custom_field( $post_type = 'post', $post = NULL ){
		add_meta_box('insert_multilingual_custom_field', "Multilingual Settings", array(&$this,'add_multilingual_meta_boxes'), $post_type, 'normal', 'high');
	}
	
	/**
	* ファンクション名：add_admin_print_styles
	* 機能概要：JSとCSSを読み込み
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param 
	* @return 
	*/	
	function add_admin_print_styles( ){

		//wp_enqueue_script( 'prefix-js-tinyMCEID' , plugins_url('js/tinyMCEID.js', __FILE__) );
				
		wp_enqueue_script( 'prefix-js-' . strtolower(__CLASS__) , plugins_url('js/edit-post-prefix-js.js', __FILE__) );
		
		wp_enqueue_style( 'prefix-style-' . strtolower(__CLASS__), plugins_url('css/edit-post-prefix-style.css', __FILE__)  );

	}
	
	/**
	* ファンクション名：
	* 機能概要：一覧項目のカスタマイズ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_multilingual_posts_columns( $columns ){

		$columns['title'] = $columns['title'] . '&nbsp;<img src="' .  $this->flags_dir . $this->WPLANGKEY . '.png' . '">';
		foreach ( $this->_items as $key => $val ){
			$count = $count + 1 ;
			if ( $count > $this->max_count ){
				break;
			}
			$columns['post_title_'.$key] = __('Title').'&nbsp;<img src="' . $this->flags_dir .  $key . '.png">';
		}
	        return $columns;
	}
	
	/**
	* ファンクション名：add_multilingual_scompt_custom_column
	* 機能概要：一覧項目のカスタマイズ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_multilingual_scompt_custom_column( $column_name, $id ){
		foreach ( $this->_items as $key => $val ){
			if( $column_name == 'post_title_' . $key ) {
				$multilingual = get_post_meta( $id , 'post_title_' . $key , true );
				if ( $multilingual ){
					$edit_link = get_edit_post_link( $id );
					echo '<a title="'.sprintf(__('Edit &#8220;%s&#8221;'), $multilingual) .'" href="'.$edit_link.'" class="row-title">'.$multilingual.'</a>';
				}
			}
		}
	}	
	
	/**
	* ファンクション名：add_multilingual_meta_boxes
	* 機能概要：各多国語のメターボックスを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param Object $post
	* @return 
	*/	
	function add_multilingual_meta_boxes( $post ){
		//
		$this->add_multilingual_postbox_title( );
		
		foreach ( $this->_items as $key => $val ):
			//hrのHTMLタグを生成する
			$this->add_multilingual_hr($key);
			//TitleのHTMLタグを生成する
			$this->add_multilingual_title($key,$post);
			//ContentのHTMLタグを生成する
			$this->add_multilingual_content($key,$post );
		endforeach;
		
	}

	/**
	* ファンクション名：add_multilingual_postbox_title
	* 機能概要：各多国語のメターボックスのタイトル
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param 
	* @return 
	*/	
	function add_multilingual_postbox_title( ){
		echo '<h5 class="postbox_hr post" id="postbox_hr_postbox_title" >Sections Setting</h5>';
		
		$flag_icon_path = $this->flags_dir . $this->WPLANGKEY . '.png';
		if ( $this->url_exists( $flag_icon )):
			$flag_icon = $flag_icon_path;	
		endif;
		$hidden = '<input type="hidden" name="default_language" id="default_language" value="'. $flag_icon .'" />';
		$hidden .= '<input type="hidden" name="multilingual-verify-key" id="multilingual-verify-key" value="'. wp_create_nonce('multilingual') .'" />';
		
		echo $hidden;	

	}

	/**
	* ファンクション名：add_multilingual_hr
	* 機能概要：各多国語の設定セクション表題
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $lang
	* @return 
	*/	
	function add_multilingual_hr( $lang ){
		$lang_name = $this->_items[$lang];
		$flag_icon = $this->flags_dir . $lang . '.png';
		if ( $this->url_exists( $flag_icon )):
			$flag_icon = '&nbsp; <img src="' .  $flag_icon. '">';	
		endif;
		echo '<h5 class="postbox_hr post page" id="postbox_hr2">Setting Information for ' . $lang_name . $flag_icon . '</h5>';
		
	}
	
	/**
	* ファンクション名：add_multilingual_title
	* 機能概要：各多国語のタイトルの表示と設定
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $lang
	* @param Object $post
	* @return 
	*/	
	function add_multilingual_title( $lang , $post = null ){
		$Title = __( 'Title' );
		$post_ml_title = get_post_meta($post->ID,'post_title_' . $lang , true );
		$inside = <<< EOF
			<div id="ml_post_title_{$lang}_box" class="postbox textfield post page">
	            <h4 class="cf_title">{$this->_items[$lang]} {$Title}</h4>
	            <div class="inside">
	            	<p class="ml_input">
	            		<input type="text" size="138" value="{$post_ml_title}" name="ml_post_title_{$lang}" id="ml_post_title_{$lang}" class="data errPosRight">
	            	</p>
	            	<p class="ml_sample">Please enter the title.</p>
	            </div>
	        </div>
EOF;

		echo $inside;
		
	}
	
	/**
	* ファンクション名：add_multilingual_content
	* 機能概要：各多国語の本文内容の表示と設定
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param String $lang
	* @param Object $post
	* @return 
	*/	
	function add_multilingual_content( $lang, $post = null ){

        $defaults = array(    
            'tabindex'      => 1,
            'wpautop'       => true,
            'media_buttons' => true,
            'textarea_name' => 'content',
            'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."                
            'tabindex'      => '',         
            'editor_css'    => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".        
            'editor_class'  => '', // add extra class(es) to the editor textarea        
            'teeny'         => false, // output the minimal editor config used in Press This        
            'dfw'           => false, // replace the default fullscreen with DFW (needs specific DOM elements and css)        
            'tinymce'       => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()        
            'quicktags'     => true, // load Quicktags, can be used to pass settings directly to Quicktags using an array()        
            'textarea_name' => "ml_post_content_{$lang}"
        );  

        $atts = wp_parse_args( $args, $defaults );
        $post_ml_content = get_post_meta($post->ID,'post_content_' . $lang , true );

		$Content = __('Content');

		$inside = <<< EOF
		<h4 class="cf_title" >{$this->_items[$lang]} {$Content}</h4>
EOF;
		echo $inside;
        wp_editor( $post_ml_content, "ml_post_content_{$lang}" , $atts );
        return;

		/*$Add_Media = __('Add Media');
		$Upload_Insert = sprintf(__('Upload/Insert %s'),'');
		$Visual = __('Visual');
		$Content = __('Content');
		$post_ml_content = get_post_meta($post->ID,'post_content_' . $lang , true );
		$inside = <<< EOF
		<div class="postbox textarea post page news faq" id="ml_post_content_{$lang}_box">
		<h4 class="cf_title" >{$this->_items[$lang]} {$Content}</h4>
		<div class="inside">
		<script type="text/javascript">
			// <![CDATA[
			jQuery(document).ready(function() {
				if ( typeof tinyMCE != "undefined" ) {
					document.getElementById("ml_post_content_{$lang}").value = switchEditors.wpautop(document.getElementById("ml_post_content_{$lang}").value); 
					var ed = new tinyMCE.Editor("ml_post_content_{$lang}", tinyMCEPreInit.mceInit["content"]); 
					ed.render(); 
					tinyMCEID.push("ml_post_content_{$lang}");
				}
			}
			);
			// ]]>
		</script>
		
		<div>
			<span><label for="add_media_ml_post_content_{$lang}">{$Upload_Insert}</label></span>
			<a href="media-upload.php?TB_iframe=true" id="add_media_ml_post_content_{$lang}" title='{$Add_Media}' onclick="focusTextArea('ml_post_content_{$lang}'); jQuery(this).attr('href',jQuery(this).attr('href').replace('\?','?post_id='+jQuery('#post_ID').val())); return thickbox(this);">
				<img src='images/media-button.png' alt="{$Add_Media}" />
			</a>
		
			<div id="ml_post_content_{$lang}_div_visual" class="wp-editor-tools tmce-active">
				<a href="#" id="ml_post_content_{$lang}_a_html"  class="hide-if-no-js wp-switch-editor switch-html" onclick="switchMode(jQuery(this).parent().parent().parent().find('textarea').attr('id'),'html'); return false;">HTML
				</a>
				<a href="#" id="ml_post_content_{$lang}_a_visual" class="hide-if-no-js wp-switch-editor switch-tmce"  onclick="switchMode(jQuery(this).parent().parent().parent().find('textarea').attr('id'),'visual'); return false;">{$Visual}
				</a>
			</div>
		
			<script type="text/javascript">
				// <![CDATA[
						jQuery(document).ready(function() { new QTags("ml_post_content_{$lang}"); QTags._buttonsInit();  jQuery("#qt_ml_post_content_{$lang}_toolbar").hide(); }); 
				// ]]>
			</script>
		
			<div class="wp-editor-container" id="editorcontainer_ml_post_content_{$lang}">
			    <textarea class="data" id="ml_post_content_{$lang}" name="ml_post_content_{$lang}" type="textfield" rows="20" cols="137">{$post_ml_content}</textarea>
			</div>
			</div>
				<p class="ml_sample">Please enter the content.</p>
			</div>
		</div>
EOF;
		echo $inside;*/
	}
	
	/**
	* ファンクション名：edit_meta_value
	* 機能概要：多国語のタイトルと内容をカスタムフィールドに登録する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param Int $post_id
	* @param Object $post
	* @return 
	*/	
	function edit_meta_value( $post_id ,$post=null){   		
	
	    if ( $post_id == 0) {
        	return $post_id;
	    }
	    	    
	    global $wpdb;
	    if (!current_user_can('edit_post', $post_id)) {
	        return $post_id;
	    }
  
	    $nonce = isset($_REQUEST['multilingual-verify-key']) ? $_REQUEST['multilingual-verify-key']: '';

	    if (!wp_verify_nonce($nonce, 'multilingual')) {
       		return $post_id;
		}
    
	   	foreach ( $this->_items as $key => $val ):
	   		foreach ($this->prefix_meta_keys as $prefix_meta_key) :
		   		$name = 'ml_' . $prefix_meta_key . $key;
		   		
		   		$meta_key = $prefix_meta_key . $key;

	        	$meta_value = isset($_REQUEST["{$name}"]) ? stripslashes(trim($_REQUEST["{$name}"])): '';	   		

	        	if (isset($meta_value) && !empty($meta_value)) {
	        	
		        	$_meta_value = get_post_meta($post_id, $meta_key, true);
	
		        	if ( $_meta_value == $meta_value && $meta_value != "" ){
		        		continue;
		        	}elseif( $meta_value == '' ){
		        		if ( !isset($_REQUEST["$name"]) )
		        		delete_post_meta($post_id, $meta_key);
		        	}else{
		        		update_post_meta($post_id, $meta_key, $meta_value);
		        	}
	        	} elseif (isset($meta_value) && strval($meta_value) === '0') {
            		add_post_meta($post_id, $meta_key, '0');
		        } else {
		            delete_post_meta($post_id, $meta_key);
		        }
        	endforeach;
	   	endforeach;
	    
	}

	/**
	* ファンクション名：url_exists
	* 機能概要：URLのありなし確認
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $url
	* @return Boolean true/false
	*/	
	function url_exists($url) {
	    if (!$fp = curl_init($url)) return false;
	    return true;
	}
	
}//class end

$ps_multilingual_edit_post = new ps_multilingual_edit_post( );

?>
