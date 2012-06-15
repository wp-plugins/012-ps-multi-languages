<?php
/*
 * Description: Add Meta Box Manager Config
 * Author: Wangbin
*/
	/*Sample 例子 サンプル*/
	/*
	 * Setting multilingual
	 * マルチ言語の設定
	 * 多国語的設定
	 */
	$this->multilingual = array(
							'en' => 'English', 		
							'it' => 'Germany',
	
	);
		
	/*
	 * Setting multilingual code
	 * マルチ言語コード multilingual_code.txtまで参考
	 * 各国語言編码
	 */
	$this->multilingual_code = array(
							'en' => 'en_US',
							'it' => 'it',
	);

	/*
	 * Search Title
	 * 検索のタイトル
	 * 査詢的表題
	 */
	$this->search_multilingual_title = array(
							'en' => 'Search Results For', 		 
							'it' => 'risultato della ricerca',
	);

	/* system(site) default language
	 * システムデフォルトの言語を設定する
	 * 系統（網站）默認的語言
	 */
	//$this->WPLANGKEY = 'ja';
	define( 'MULTILINGUAL_WPLANG' , 'en' );
	
	/*
	 * Taxonomy management screen display language
	 * 分类管理屏幕显示语言
	 * タクソノミー管理画面表示する文言
	 */
	$this->explain_text = array(
			'title' 			=> '<h3>Sample Title</h3>',
			'p'					=> '<p>Sample Description</p>',
			'taxonomy' 			=> array('category','post_tag'),
	);
	
	/*
	 *The number of multi-lingual information management screen to display the list of
	 *多语种信息管理屏幕显示列表
	 *管理画面の一覧に表示する多国語情報の個数 
	 */
	$this->max_count = 5 ; //一覧に多国語を表示する個数	

?>
