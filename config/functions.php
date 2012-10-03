<?php
/*
 * Description: Custom post type funxtions
 * Referring to the 「Prime Strategy Bread Crumb」
 * Author: Wangbin
*/
	
	/**
	* ファンクション名：ps_012_multilingual_list
	* 機能概要：言語一覧を取得する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param string $echo
	* @param string $lang
	* @param string $html/$multilingual_snippet
	* @return
	*/
	function ps_012_multilingual_list( $echo = false, $lang = null ){
		global $ps_multi_languages;

	 	$multilingual_snippet = $ps_multi_languages->ps_012_multilingual_list( $lang );
		if ( !$echo ):
			return $multilingual_snippet;
		endif;
		
		$html .= '<ul class="snippet">'. "\n";
		
		foreach ( $multilingual_snippet as $key => $val ){
			$current = $val['current'] ? ' class="'.$val['current'].'" ' : ''; 
			$html .= '<li'. $current .'>' . "\n";
			$html .= '<a href="'.$val['url'].'">'.$val['name'].'</a>' . "\n";
			$html .= '</li>' . "\n";
		}
		$html .= '</ul>'. "\n";
		
		echo $html;
	}
	
	/**
	* ファンクション名：get_load_language
	* 機能概要：ロード言語を取得する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param void
	* @return $lang
	*/	
	function get_load_language( ){
		global $ps_multi_languages;
		
		if ( $ps_multi_languages ){
			$lang = $ps_multi_languages->get_load_lang( );
		}
		
		return 	$lang;	
	}
	
	/**
	* ファンクション名：url_exists
	* 機能概要：URLのありなし確認
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	* @param string $url
	* @return Boolean true/false
	*/	
	function ps_url_exists($url) {
	    if (!$fp = curl_init($url)) return false;
	    return true;
	}
	
	/**
	* ファンクション名：ps_012_m17n_bread_crumb
	* 機能概要：パンくずナビの取得
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param String $type
	* @param Boolean $echo
	* @param string $bread_crumb
	* @return
	*/
	function ps_012_m17n_bread_crumb( $type = 'list', $echo = false ) {
		global $ps_multi_languages;
		$bread_crumb = $ps_multi_languages->bread_crumb( $type ,  $echo );
		return $bread_crumb;
	}

	
?>
