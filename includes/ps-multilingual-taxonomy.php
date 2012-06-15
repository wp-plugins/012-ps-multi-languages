<?php
/**
 * ps_multilingual_taxonomy
 *
 * Main Custom Multi Languages for Taxonomy include Class
 *
 * Author: Ouhinit(Wangbin)
 *
 * @package ps_multilingual_taxonomy
 */

/*
 * カテゴリー、タグ、タクソノミーなどの多国語データの表示、追加、編集、削除など。
 */
class ps_multilingual_taxonomy{
	/*
	 * タクソノミーに追加する項目
	 */
	var $_items;
	
	/*
	 * ヘッダの文言
	 */
	var $explain_text;
	
	/*
	*	Start Manager Custom  on plugins loaded
	*/
	function ps_multilingual_taxonomy( ){
		$this->__construct( );
	}
	
	/*
	 * コンストラクタ.
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
   		//タクソノミー管理画面の表示文言
   		$this->explain_text = $ps_multi_languages->explain_text;
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

		if ( is_admin( ) ){

			//管理画面カスタマイズ
			add_action( 'admin_init'									, array( &$this , 'admin_init_custom_taxonomy_edit' ) );
			
			//タクソノミーの編集画面のカスタマイズ（表示する場合）
			add_action( $_GET['taxonomy'] . '_add_form_fields'  		, array( &$this, 'admin_custom_taxonomy_add' ) );
			add_action( $_GET['taxonomy'] . '_edit_form_fields'  		, array( &$this, 'admin_custom_taxonomy_edit' ) );
			add_action( $_GET['taxonomy'] . '_pre_add_form'				, array( &$this, 'taxonomy_explain_text' ) );

			//タクソノミーの編集、追加、削除
			add_action( 'created_term' 									, array( &$this, 'update_taxonomy_item' ) );
			add_action( 'edited_terms' 									, array( &$this, 'update_taxonomy_item' ) );//edited_term_taxonomy
			add_action( 'delete_term'									, array( &$this, 'delete_taxonomy_item' ) );//delete_term_taxonomy

			//表示する項目（item）
			add_filter( 'manage_edit-' . $_GET['taxonomy'] . '_columns'	, array( &$this, 'custom_taxonomy_columns' ) );
			add_filter( 'manage_'. $_GET['taxonomy'] .'_custom_column'	, array( &$this, 'display_custom_taxonomy_column' ), 10, 3 );
			
			add_action( 'admin_print_styles-edit-tags.php'				, array( &$this, 'add_admin_footer_scripts' ) );
		}	
	}
		
	/**
	* ファンクション名：admin_init_custom_taxonomy_edit
	* 機能概要：管理画面のカスタマイズ処理
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function admin_init_custom_taxonomy_edit( ){
		
	}
	
	/**
	* ファンクション名：add_admin_footer_scripts
	* 機能概要：管理画面のカスタマイズ処理scriptsなどの追加
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_admin_footer_scripts( ){

	}
	
	/**
	* ファンクション名：admin_custom_taxonomy_add
	* 機能概要：タクソノミーを追加する時、カスタマイズ項目を表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @return なし
	*/
	function admin_custom_taxonomy_add( $taxonomy ){
		
		if ( $this->_items ):
			foreach ( $this->_items as $key => $field ):
				$func = 'make_' .  $field['type'];
				if ( !method_exists( $this ,  $func ) ){
					$func = 'make_textfield';
				}
				$this->$func($taxonomy, $key , null );	
			endforeach;
			$this->make_hidden_default_language( );
		endif;

		
	}
	
	/**
	* ファンクション名：admin_custom_taxonomy_edit
	* 機能概要：タクソノミーを編集する場合、カスタマイズ項目を表示する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @return
	*/
	function admin_custom_taxonomy_edit( $taxonomy ){

		if ( $this->_items ):
			foreach ( $this->_items as $key => $field ):

				$func = 'make_' . $field['type'];
				if ( !method_exists( $this ,  $func ) ){
					$func = 'make_textfield';
				}

				$value = get_option( $taxonomy->taxonomy . '-' . $key );

				$term_id = $taxonomy->term_id;
				
				$this->$func( $taxonomy->taxonomy, $key , $value[$term_id] , true );	
					
			endforeach;
			$this->make_hidden_default_language( );
		endif;
	}
	
	function make_hidden_default_language(  ){
		$flag_icon_path = $this->flags_dir . $this->WPLANGKEY . '.png';
		if ( $this->url_exists( $flag_icon )):
			$flag_icon = $flag_icon_path;	
		endif;
		$hidden .= '<div><input type="hidden" name="default_language" id="default_language" value="'. $flag_icon .'" />';
		$hidden .= '<input type="hidden" name="multilingual-taxonomy-verify-key" id="multilingual-taxonomy-verify-key" value="'. wp_create_nonce('multilingual_taxonomy') .'" /></div>';
		echo $hidden;	
	}
	/**
	* ファンクション名：make_textfield
	* 機能概要：textfieldの項目を作成
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $taxonomy(category)
	* @param string $key(item key)
	* @param string $value
	* @return
	*/
	function make_textfield($taxonomy, $key, $value , $edit = false ){
		$flag_icon = $this->flags_dir . $key . '.png';
		if ( $this->url_exists( $flag_icon )):
			$flag_icon = '&nbsp; <img src="' .  $flag_icon. '">';	
		endif;
		if ( $edit === true ):
?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $this->_items[$key] . __('Name') . $flag_icon; ?></label>
				</th>
				<td>
					<input type="text" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" size=40 value="<?php echo isset( $value ) ? esc_attr( $value ) : ''; ?>" />
				</td> 
			</tr>
		<?php else:?>
			<div class="form-field">
				<label for="<?php echo $taxonomy . '-' . $key ?>"><?php echo $this->_items[$key] . __('Name') . $flag_icon; ?></label>
				<input type="text" name="<?php echo $key;?>" id="<?php echo $taxonomy . '-' . $key ?>" size=40 value="" />				 
			</div>
<?php
		endif;
	}

	/**
	* ファンクション名：taxonomy_explain_text
	* 機能概要：説明を追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param なし
	* @return なし
	*/
	function taxonomy_explain_text( ){
		if ( $this->explain_text ){
			if ( $this->explain_text['title'] ):
				echo $this->explain_text['title'];
			endif;
			if ( $this->explain_text['p'] ):
				echo $this->explain_text['p'];
			endif;
		}
	}
	/**
	* ファンクション名：update_taxonomy_item
	* 機能概要：タクソノミーの追加する項目を編集する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param int $term_id
	* @return なし
	*/
	function update_taxonomy_item( $term_id ){
		$nonce = isset($_REQUEST['multilingual-taxonomy-verify-key']) ? $_REQUEST['multilingual-taxonomy-verify-key']: '';
	    if (!wp_verify_nonce($nonce, 'multilingual_taxonomy')) {
       		return false;
		}
		foreach ( $this->_items as $key => $val ):
			$post_option = stripslashes_deep( $_POST[$key] );

			$current_option = get_option( $_POST['taxonomy'] . '-' . $key );
		
			if ( ! isset( $current_option[$term_id] ) || $current_option[$term_id] != $post_option ):
			
				$current_option[$term_id] = $post_option;

				update_option( $_POST['taxonomy'] . '-' . $key, $current_option );
				
			endif;
		endforeach;
	}	
	
	/**
	* ファンクション名： delete_taxonomy_item
	* 機能概要：タクソノミーの削除とおともに、追加す項目を削除する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param int $term_id
	* @return なし
	*/
	function delete_taxonomy_item( $term_id ){

		foreach ( $this->_items as $key => $val ):
				
			$current_option = get_option( $_POST['taxonomy'] . '-' . $key );
			
			unset( $current_option[$term_id] );
			
			update_option( $_POST['taxonomy'] . '-' . $key, $current_option );
			
		endforeach;
	}
	
	/**
	* ファンクション名：custom_taxonomy_columns
	* 機能概要：タクソノミー一覧に表示を追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param array $columns
	* @return
	*/
	function custom_taxonomy_columns( $columns ){

		foreach ( $columns as $key => $column ):
			if ( $key == 'posts' ):
				foreach ( $this->_items as $key2 => $val):
					$count = $count + 1;
					if ( $count > $this->max_count ){
						break;
					}
					
					$flag_icon = $this->flags_dir . $key2 . '.png';
					if ( $this->url_exists( $flag_icon )):
						$sort_columns[$key2] = '<img src="' .  $flag_icon. '">';
					else:
						$sort_columns[$key2] = $val;				
					endif;					
				endforeach;
			endif;
			$sort_columns[$key] = $column;
		endforeach;

		$columns = $sort_columns;

		return $columns;
		
	}
	
	/**
	* ファンクション名：display_custom_taxonomy_column
	* 機能概要：タクソノミー一覧に表示を追加する（表示データ）
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $output
	* @param string $column_name
	* @param int $term_id
	* @return
	*/
	function display_custom_taxonomy_column( $output, $column_name, $term_id ){

		foreach ( $this->_items as $key => $val):
			if ( $column_name == $key ):
			$current_option = get_option( $_GET['taxonomy'] . '-' . $key );
					
			$current_option[$term_id] = ( is_array( $current_option[$term_id] ) ) ? join(',',$current_option[$term_id] ) : $current_option[$term_id] ;

			$output = isset( $current_option[$term_id] ) ? esc_html( $current_option[$term_id] ) : '&nbsp;';

			esc_html($output);
			endif;
		endforeach;

		return $output;
	}
	
	/**
	* ファンクション名：ps_multilingual_taxonomy_terms
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param object $terms
	* @param array $taxonomies
	* @param array $args
	* @return
	*/
	function ps_multilingual_taxonomy_terms ( $terms, $taxonomies, $args ){

		foreach ( $this->_items as $key => $val):
			if ( is_array( $val['taxonomy']) && in_array( $taxonomies[0] , $val['taxonomy'] ) ):
				$current_option[$key] = get_option( $taxonomies[0] . '-' . $key);
			endif;
		endforeach;
		
		foreach ( $terms as $key => $term ):
			foreach ( $this->_items as $key2 => $val):
				$taxonomy_val = $current_option[$key2][$term->term_id];
				if ( $taxonomy_val ):
					$terms[$key]->$key2 = $taxonomy_val;
				endif;
			endforeach;
		endforeach;

		return $terms;
	}

	/**
	* ファンクション名：ps_multilingual_taxonomy_the_terms
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param resource $terms
	* @param int $id
	* @param string $taxonomy
	* @return
	*/
	function ps_multilingual_taxonomy_the_terms( $terms, $id, $taxonomy ){
		
		foreach ( $this->_items as $key => $val):
			if ( is_array( $val['taxonomy']) && in_array( $taxonomy , $val['taxonomy'] ) ):
				$current_option[$key] = get_option( $taxonomy . '-' . $key);
			endif;
		endforeach;
		
		foreach ( $terms as $key => $term ):
			foreach ( $this->_items as $key2 => $val):
				$taxonomy_val = $current_option[$key2][$term->term_id];
				if ( $taxonomy_val ):
					$terms[$key]->$key2 = $taxonomy_val;
				endif;
			endforeach;
		endforeach;

		return $terms;	
	}
	
	/**
	* ファンクション名：ps_multilingual_taxonomy_term
	* 機能概要：タクソノミーを取得するObjectにカスタマイズデータを追加する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param onject $term
	* @param string $taxonomy
	* @return
	*/	
	function ps_multilingual_taxonomy_term( $term, $taxonomy ){
		
		foreach ( $this->_items as $key => $val):
		
			if ( is_array( $val['taxonomy'] ) && in_array( $taxonomy , $val['taxonomy'] ) ):
				$current_option = get_option( $taxonomy . '-' . $key );
				if ( $current_option[$term->term_id] ){
					$term->$key = $current_option[$term->term_id];
				}			
			endif;
			
		endforeach;
		
		return $term;

	}
	
	/**
	* ファンクション名：url_exists
	* 機能概要：URLのありなし確認
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function url_exists($url) {
	    if (!$fp = curl_init($url)) return false;
	    return true;
	}
}//class end

$ps_multilingual_taxonomy = new ps_multilingual_taxonomy( );

?>
