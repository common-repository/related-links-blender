<?php
/**
 * Main functionality
 **/

/*
helpful code credits:
http://pippinsplugins.com/a-better-wordpress-excerpt-by-id-function/



*/

class RelatedLinksBlender_core extends RelatedLinksBlender {


	function __construct() {
		
		parent::__construct();
		
		
		add_action("the_content", array( $this,"rlb_get_ur_done") ); 
		add_action("wp_head", array( $this,"rlb_style_it") );  

	
	}

// Gets the excerpt of a specific post ID or object
// @param - $post - object/int - the ID or object of the post to get the excerpt of
// @param - $length - int - the length of the excerpt in words
// @param - $tags - string - the allowed HTML tags. These will not be stripped out
// @param - $extra - string - text to append to the end of the excerpt
protected function excerpt_by_id($post, $length = 10, $tags = '<a><em><strong>', $extra = ' . . .') {
 
	// get the post object of the passed ID
	$rlb_my_post = get_post($post);
	if (!is_object($rlb_my_post)) {
		return 'not object'.$post;
	}
	if(has_excerpt($rlb_my_post->ID)) {
		$rlb_the_excerpt = $rlb_my_post->post_excerpt;
	} else {
		$rlb_the_excerpt = $rlb_my_post->post_content;
	}

	$rlb_the_excerpt = strip_shortcodes(strip_tags($rlb_the_excerpt), $tags);
	$rlb_the_excerpt = preg_split('/\b/', $rlb_the_excerpt, $length * 2+1);
	$excerpt_waste = array_pop($rlb_the_excerpt);
	$rlb_the_excerpt = implode($rlb_the_excerpt);
	$rlb_the_excerpt .= $extra;
 
	return $rlb_the_excerpt;
}




public function rlb_style_it() {
		$options = get_option('rlb_plugin_options');
		echo "<style type='text/css'>".$options['rlb_styles']."</style>";
}



protected  function rlb_get_links_by_id() {
	$rlb_meta = get_post_meta( get_the_ID(), 'rlb_meta', TRUE );
	$rlb_meta = "";
	$theres_a_link=false;
	if (is_array($rlb_meta)) {
		$list_of_ids=explode(',',$rlb_meta['related_links_by_id']);
		
		for ($x=0;$x<count($list_of_ids);$x++) {
			if (is_numeric($list_of_ids[$x])) {
				$list_of_ids[$x]= intval($list_of_ids[$x]);
				$theres_a_link=true;
			} else {
				$list_of_ids[$x]=0;
			}
		}
	}

	return $theres_a_link ? $list_of_ids : 0;
}

protected function rlb_insert_link($id) {
	$options = get_option('rlb_plugin_options');
	$anchor_tag='<a href="'.get_permalink( $id ).'">';
	
	$a_link=$options[rlb_link_wrapper_prefix];
	$a_link.=$anchor_tag.get_the_post_thumbnail( $id, "thumbnail", $attr ).'<strong>'.get_the_title($id).'</strong></a> - '.$this->excerpt_by_id($id,20);
	$a_link.=$options[rlb_link_wrapper_suffix];
	

	return $a_link;
}


public function rlb_get_ur_done($content) {
	//if (is_admin_bar_showing()) { //use to restrict to admin
	$options = get_option('rlb_plugin_options');
	
	$should_i_insert=false;
	if (is_single() && $options['rlb_controls_post']==true) $should_i_insert=true;
	if (is_page() && $options['rlb_controls_page']==true) $should_i_insert=true;
	if ($should_i_insert) {
		
		$link_content="";
		$has_links=false;
		
		$some_links_to_ids=$this->rlb_get_links_by_id();
		if (count($some_links_to_ids[0])>0) {
			foreach ($some_links_to_ids as $a_link) {
				//insert a link for all non-zero numbers in the array
				if ($a_link) $link_content.=$this->rlb_insert_link($a_link);
			}
			$has_links=true;
		}

		$some_links_cached=get_post_meta(get_the_ID(),'related_links_blender_links_cached',true);
		if (strlen($some_links_cached)>0) {
			$link_content.=	$some_links_cached;	
			$has_links=true;
		}


		if ( $has_links ) { 
			$content.=$options['rlb_wrapper_prefix'];

			$content.=$link_content;

			$content.=$options['rlb_wrapper_suffix'];

			return html_entity_decode($content);

		}  else {
			return $content;
		}
		
	} else { //is_single
			return $content;
	}
}


public function insert_the_links() {
	$options = get_option('rlb_plugin_options');
		
		$link_content="";
		$has_links=false;
		
		$some_links_to_ids=$this->rlb_get_links_by_id();
		if (count($some_links_to_ids[0])>0) {
			foreach ($some_links_to_ids as $a_link) {
				//insert a link for all non-zero numbers in the array
				if ($a_link) $link_content.=$this->rlb_insert_link($a_link);
			}
			$has_links=true;
		}

		$some_links_cached=get_post_meta(get_the_ID(),'related_links_blender_links_cached',true);
		if (strlen($some_links_cached)>0) {
			$link_content.=	$some_links_cached;	
			$has_links=true;
		}

		if ( $has_links ) { 
			echo html_entity_decode($options[rlb_wrapper_prefix] . $link_content . $options[rlb_wrapper_suffix]);
		} else {
			echo "<!--no-blender-related-links-->";
		}
		
}



} //end class


?>