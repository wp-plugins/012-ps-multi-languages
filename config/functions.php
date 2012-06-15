<?php
/*
 * Description: Custom post type funxtions
 * Author: Wangbin
*/
	

	function get_multi_lang_snippet( $lang = null , $echo = false ){
		global $ps_multi_languages;
		
	 	$multilingual_snippet = $ps_multi_languages->get_multilingual_snippet( $lang );
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

?>
