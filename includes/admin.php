<?php

/* helpful code credits
http://codex.wordpress.org/AJAX_in_Plugins


*/




class RelatedLinksBlender_admin extends RelatedLinksBlender_core {
	

	function __construct() {

		parent::__construct();
		
		
		// Register admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'register_admin_styles' ) );

		//ajax hander
		add_action('wp_ajax_adding_a_link', array( $this, 'adding_a_link'));
		add_action('wp_ajax_deleting_a_link', array( $this, 'deleting_a_link'));
		add_action('wp_ajax_get_cached_links', array( $this, 'get_cached_links'));
		add_action('wp_ajax_get_the_links', array( $this, 'get_the_links'));
		add_action('wp_ajax_get_similar_links', array( $this, 'get_similar_links'));
		add_action('wp_ajax_sort_links', array( $this, 'sort_links'));


		//admin options page init
		add_action('admin_menu', array($this,'rlb_plugin_admin_add_page'));
		add_action('admin_init', array($this,'rlb_plugin_admin_init'));
	
		//post edit panel init
		$this->editor_page_panel_init();
		

		
	}

	 // Registers and enqueues admin-specific styles.
	public function register_admin_styles() {
	
		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_style( 'plugin-name-admin-styles', plugins_url( 'related-links-blender/assets/css/admin.css' ) );
	
	} // end register_admin_styles


	 // Registers and enqueues admin-specific JavaScript.
	public function register_admin_scripts() {
	
		// TODO:	Change 'plugin-name' to the name of your plugin
		wp_enqueue_script( 'related-links-blender-admin-script', plugins_url( 'related-links-blender/assets/js/admin.js' ) );
		wp_localize_script(  
			'related-links-blender-admin-script',  
			'localized',  
			array('nonce'=>wp_create_nonce('my_nonce'))
			);  
	
	} // end register_admin_scripts






// add the admin options page
function rlb_plugin_admin_add_page() {
	add_options_page('Related Links Blender Page - BETA', 'Related Links Blender', 'manage_options', 'rlb_plugin_options_page', array($this,'rlb_plugin_options_page'));
}




// display the admin options page
function rlb_plugin_options_page() {
?>
        <div>
        <h2>Related Links Blender Settings</h2>
        <form action="options.php" method="post">
        <? settings_fields('rlb_plugin_options'); ?>
        <? do_settings_sections('rlb_plugin_options_page'); ?>
         
        <input name="Submit" type="submit" value="<? esc_attr_e('Save Changes'); ?>" />
        </form></div>
         
        <?
}


function rlb_plugin_section_text() {
echo '<p>These are global options for the plugin and how it will function.  Choosing links to append to Pages and Posts is done on their individual editing pages.</p>';
echo '<p>This plugin is BETA, please test carefully.  Report any problems on the <a target="new" href="http://wordpress.org/support/plugin/related-links-blender">support page</a></p>';

// Used to dump all the options for debugging
//$options = get_option('rlb_plugin_options'); echo '<p>'.var_dump($options).'</p>'; 

} 


//setup the admin options panel
function rlb_plugin_admin_init(){
	register_setting( 'rlb_plugin_options', 'rlb_plugin_options', array($this,'rlb_plugin_options_validate') );

	add_settings_section('rlb_settings', 'Main Settings', array($this,'rlb_plugin_section_text'), 'rlb_plugin_options_page');
	
	add_settings_field('rlb_controls_page', 'Add links to Pages', array($this,'rlb_plugin_setting_checkbox'), 'rlb_plugin_options_page', 'rlb_settings','rlb_controls_page');
	add_settings_field('rlb_controls_post', 'Add links to Posts', array($this,'rlb_plugin_setting_checkbox'), 'rlb_plugin_options_page', 'rlb_settings','rlb_controls_post');
	
	add_settings_field('rlb_wrapper_prefix', 'HTML opening tags before the links', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_wrapper_prefix');
	add_settings_field('rlb_link_wrapper_prefix', 'HTML tags before a link', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_link_wrapper_prefix');
	add_settings_field('rlb_link_wrapper_suffix', 'HTML closing tags after a link', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_link_wrapper_suffix');
	add_settings_field('rlb_wrapper_suffix', 'HTML closing tags after the links', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_wrapper_suffix');

	add_settings_field('rlb_thumb_width', 'Image thumb width, if included', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_thumb_width');
	add_settings_field('rlb_thumb_height', 'Image thumb height, if included', array($this,'rlb_plugin_setting_string'), 'rlb_plugin_options_page', 'rlb_settings','rlb_thumb_height');

	add_settings_field('rlb_styles', 'Styles for your links', array($this,'rlb_plugin_setting_textarea'), 'rlb_plugin_options_page', 'rlb_settings','rlb_styles');

	//add_settings_field('rlb_test_option', 'Test Option (ignore me)', 'rlb_plugin_setting_string', 'rlb_plugin_options_page', 'rlb_settings','rlb_test_option');
}

function rlb_plugin_setting_string($who_am_i) {
	$options = get_option('rlb_plugin_options');
	echo "<input id='$who_am_i' name='rlb_plugin_options[$who_am_i]' size='100' type='text' value='{$options[$who_am_i]}' />";
}

function rlb_plugin_setting_checkbox($who_am_i) {
	$options = get_option('rlb_plugin_options');
	echo "<input id='$who_am_i' name='rlb_plugin_options[$who_am_i]' type='checkbox' value='checked' ".checked( $options[$who_am_i] ,true,false)." />";
}

function rlb_plugin_setting_textarea($who_am_i) {
	$options = get_option('rlb_plugin_options');
	echo "<textarea rows='10' cols='100' id='$who_am_i' name='rlb_plugin_options[$who_am_i]'>{$options[$who_am_i]}</textarea>";
}

// validate our options
function rlb_plugin_options_validate($input) {
	
	$options = get_option('rlb_plugin_options');

	if ($options) {
		//convert these two from string to boolean

		if (array_key_exists('rlb_controls_page',$input)) {
			$input['rlb_controls_page'] = ($input['rlb_controls_page']=='checked') ? true : false;
		} else {
			$input['rlb_controls_page'] = false;
		}

		if (array_key_exists('rlb_controls_post',$input)) {
			$input['rlb_controls_post'] = ($input['rlb_controls_post']=='checked') ? true : false;
		} else {
			$input['rlb_controls_post'] = false;
		}

		//convert these two from string to integer
		$input['rlb_thumb_width'] = intval($input['rlb_thumb_width']);
		$input['rlb_thumb_height'] = intval($input['rlb_thumb_height']);
		
		//step through the input array and copy values to the output array.  Only array elements that already exist in the output array
		//array will be copied as this array is controlled
		foreach ($input as $key => $value) {
			if (array_key_exists($key,$options)) {
				if (is_string($input[$key]))  $options[$key]=esc_textarea(trim($input[$key]));
				if (is_int($input[$key]))  $options[$key]=$input[$key];
				if (is_bool($input[$key]))  $options[$key]=$input[$key];
			}
		}
		return $options;		
	}	


}










	

/***************************************************************************
 Define the custom box 
****************************************************************************/







	// ajax handler for adding a link
	function adding_a_link() {
		$new_link = json_decode(stripslashes($_POST['link_info']), true);
		$nonce_check=check_ajax_referer('my_nonce','nonce',false);
		if ( ! current_user_can('edit_post',$new_link['post_id']) )  {
			echo json_encode(array('success'=>false,'response'=>'You don\'t have permission to make this change.'));
		} else if (!$nonce_check) {
			echo json_encode(array('success'=>false,'response'=>'Illegal connection.'));
		} else {
			$current_links=get_post_meta(intval($new_link['post_id']),'related_links_blender_links',true);
			if (is_array($current_links)) { //$current_links is false if empty
				$array_length=array_push($current_links,$new_link);
			} else {
				$current_links=array(0=>$new_link);
			}
			update_post_meta(intval($new_link['post_id']),'related_links_blender_links',$current_links);
			$outcome= $this->smush_the_links(intval($new_link['post_id']),$current_links);
			echo json_encode(array('success'=>true,'response'=>$outcome));
		}
		die();
	}


	protected function isValidUrl($url) {
		return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
	}  
  
	// save all the links at page ready html
	protected function smush_the_links($post_id,$current_links) {
		wp_cache_flush();
		$options = get_option('rlb_plugin_options');
		$link_string="";
		foreach ($current_links as $link_no => $a_link) {
			$link_string.=html_entity_decode($options['rlb_link_wrapper_prefix']);

			$target_string="";
			if (array_key_exists("link_target",$a_link)) {
				$target_string=" target='_blank' ";
			} 

			$link_string.="<a href='".$a_link['link_url']."' title='".$a_link['link_title']."' ".$target_string.">";
			$link_string.= ($this->isValidUrl($a_link['thumb_url'])) ? "<img src='".$a_link['thumb_url']."' alt='".$a_link['thumb_alt']."'>" : "";
			$link_string.="<strong>".$a_link['text_title']."</strong></a> - ".$a_link['text_intro'];
			$link_string.=html_entity_decode($options['rlb_link_wrapper_suffix']);
		}
		$outcome= update_post_meta($post_id,'related_links_blender_links_cached',$link_string);
		if ($outcome) {
			$post_array=array(0=>get_post($post_id));
			update_post_caches( $post_array, false,false, true);
			return get_post_meta($post_id,'related_links_blender_links_cached',true);
		} else {
			return false;
		}
	}



	// ajax handler for adding a link
	function deleting_a_link() {
		$link_info = json_decode(stripslashes($_POST['link_info']), true);
		$nonce_check=check_ajax_referer('my_nonce','nonce',false);
		
		if ( ! current_user_can('edit_post',$link_info['post_id']) )  {
			echo json_encode(array('success'=>false,'response'=>'You don\'t have permission to make this change.'));
		} else if (!$nonce_check) {
			echo json_encode(array('success'=>false,'response'=>'Illegal connection.'));
		} else {
			$current_links=get_post_meta(intval($link_info['post_id']),'related_links_blender_links',true);
	
			if (is_array($current_links)) { //$current_links is false if empty
				unset($current_links[$link_info['link_no']]);
				update_post_meta(intval($link_info['post_id']),'related_links_blender_links',$current_links);
				$outcome=$this->smush_the_links($link_info['post_id'],$current_links);
				//echo json_encode(array('success'=>true,'link_no'=>$link_info['link_no']));
				//temt testing response below
				echo json_encode(array('success'=>true,'link_no'=>$link_info['link_no'],'response'=>$outcome));
			} else {
				echo json_encode(array('success'=>false));
			}
		}
		die();
	}

	// ajax handler for adding a link
	function sort_links() {
		$link_info = json_decode(stripslashes($_POST['link_info']), true);
		$nonce_check=check_ajax_referer('my_nonce','nonce',false);
		
		if ( ! current_user_can('edit_post',$link_info['post_id']) )  {
			echo json_encode(array('success'=>false,'response'=>'You don\'t have permission to make this change.'));
		} else if (!$nonce_check) {
			echo json_encode(array('success'=>false,'response'=>'Illegal connection.'));
		} else {
			$current_links=get_post_meta(intval($link_info['post_id']),'related_links_blender_links',true);
	
			if (is_array($current_links)) { //$current_links is false if empty

				foreach($link_info['link_order'] as $position => $old_position) {
					$new_link_array[$position] = $current_links[$old_position];
				}
				update_post_meta(intval($link_info['post_id']),'related_links_blender_links',$new_link_array);
				$outcome=$this->smush_the_links($link_info['post_id'],$new_link_array);
				//echo json_encode(array('success'=>true,'link_no'=>$link_info['link_no']));
				//temt testing response below
				echo json_encode(array('success'=>true,'response'=>$outcome));
			} else {
				echo json_encode(array('success'=>false));
			}
		}
		die();
	}

	// ajax handler for adding a link
	function get_cached_links() {
		$link_info = json_decode(stripslashes($_POST['link_info']), true);
		$nonce_check=check_ajax_referer('my_nonce','nonce',false);
		
		if ( ! current_user_can('edit_post',$link_info['post_id']) )  {
			echo json_encode(array('success'=>false,'response'=>'You don\'t have permission to make this change.'));
		} else if (!$nonce_check) {
			echo json_encode(array('success'=>false,'response'=>'Illegal connection.'));
		} else {
				echo json_encode(array('success'=>true,'link_no'=>$link_info['link_no'],'response'=>get_post_meta(intval($link_info['post_id']),'related_links_blender_links_cached',true)));
		}
		die();
	}

	// ajax handler for displaying current links
	function get_the_links() {
		$link_info = json_decode(stripslashes($_POST['link_info']), true);
		$current_links=get_post_meta(intval($link_info['post_id']),'related_links_blender_links',true);
		$rlb_options = get_option('rlb_plugin_options');
		
		if (is_array($current_links)) {

            foreach($current_links as $link_no => $link_array) {
                echo "<div class='a_current_link' style='padding-left:".($rlb_options['rlb_thumb_width']+10)."px'  data-link_no='".$link_no."' >"; 
                if (is_array($link_array)){
                    foreach($link_array as $key => $value) {
						switch ($key) {
							case "text_title":
                        		echo "<span class='a_current_link_item a_current_link_title '><span class='$key'>$value</span></span>";break;
							case "text_intro":
                        		echo "<span class='a_current_link_item a_current_link_intro '><span class='$key'>$value</span></span>";break;
							case "post_id":
                        		echo "<span class='a_current_link_item a_current_link_detail_hidden '>$key : <span class='$key'>$value</span></span>";
								break;
							case "thumb_url":
                        		echo "<div class='a_current_link_item_img_wrapper '><img  src='$value' class='a_current_link_item_img' /></div>";
                        		echo "<span class='a_current_link_item a_current_link_detail_hidden '>$key : <span class='$key'>$value</span></span>";
								break;
							case "link_target":
								echo "<span class='a_current_link_item a_current_link_detail '>$key : <span class='$key'>$value</span></span>";
								break;
							default:
                        		echo "<span class='a_current_link_item a_current_link_detail '>$key : <span class='$key'>$value</span></span>";
						}
                    }
                } else {
                    echo "array error, key: $link_no";
                }
                echo "<input type='button' class='copy_me' data-link_no='".$link_no."' value='copy' />";
                echo "<input type='button' class='delete_me' data-link_no='".$link_no."' value='delete' />";
                echo "</div>";
            } 
		} else {
			echo "No links yet.";	
		}
			
		die();
	}


	// ajax handler for displaying similar links
	function get_similar_links() {
		$search_info = json_decode(stripslashes($_POST['search_info']), true);
		
		$search_terms=$search_type="";
		switch ($search_info['search_type']) {
			case 'tag': 
				$search_type='tag';
				$search_terms_array=wp_get_post_tags(intval($search_info['post_id']));
				foreach($search_terms_array as $a_tag) {
					$search_terms.=$a_tag->slug.',';
				}
				break;
			case 'category': 
				$search_type=$search_info['search_type'];
				$search_terms=implode(",", wp_get_post_categories(intval($search_info['post_id'])));
				break;
			case 'date': ;break;
			default:;
		}
		$posts_to_skip=intval($search_info['page']);
		$args = array(
			'numberposts'  => 10,
			'offset' => $posts_to_skip,
			'post_type'       => 'post',
			'post_status'     => 'publish',
			$search_type => $search_terms,
			'suppress_filters' => true 
			);		
		$posts_array = get_posts( $args );
		
		if (is_array($posts_array)) {
            foreach($posts_array as $post_no => $post) {
				$post_featured_image_id=get_post_thumbnail_id($post->ID);
				$thumb=wp_get_attachment_image_src( $post_featured_image_id, array(50,50) );//return array url in 0
				
 				if( !strlen($thumb_alt = get_post_meta($post_featured_image_id, '_wp_attachment_image_alt', true))) $thumb_alt=$post->post_title ;

                echo "
				<a  class='a_similar_link' ".
				"data-post_id='".$post->ID."' ".
				"data-text_title='".$post->post_title."' ".
				"data-text_intro='".substr(str_replace( array( '\'', '"', "\r\n", "\r", "\n"), ' ',strip_tags($post->post_content)),0,380)."' ".
				"data-thumb_url='".$thumb[0]."' ".
				"data-link_url='".get_permalink($post->ID)."' ".
				"data-link_title='".$post->post_title."' ".
				"data-thumb_alt='".$thumb_alt."' ".
				"data-comment_count='".$post->comment_count."' ".
				">".$post->post_title."</a>"; 
            }
			if (count($posts_array) == 10) {
				$posts_to_skip+=10; 
				echo "<a class=' more_links_needed' data-more='$posts_to_skip' data-search_type='".$search_info['search_type']."' data-post_id='".$search_info['post_id']."' >more posts</a>";
			}
		} else {
			echo "No similar links.";	
		}
			
		die();
	}


protected function editor_page_panel_init() {	// WP 3.0+
	add_action( 'add_meta_boxes', array($this,'rlb_post_options_metabox') );
	
	// save our data when the post is saved
	add_action( 'save_post', array($this,'rlb_save_post_options') );
	add_action( 'save_page', array($this,'rlb_save_post_options') );
}

/**
 *  Adds a box to the main column on the Post edit screen
 * 
 */
function rlb_post_options_metabox() {
    add_meta_box( 'post_options', __( 'Related Links Blender BETA' ), array($this,'rlb_post_options_code'), 'post', 'normal', 'high' );
    add_meta_box( 'post_options', __( 'Related Links Blender BETA' ), array($this,'rlb_post_options_code'), 'page', 'normal', 'high' );
}

/**
 *  Prints the box content
 */
function rlb_post_options_code( $post ) { 
    wp_nonce_field( plugin_basename( __FILE__ ), $post->post_type . '_noncename' );
	$rlb_options = get_option('rlb_plugin_options');
    $rlb_meta = get_post_meta( $post->ID, 'rlb_meta', true) ? get_post_meta( $post->ID, 'rlb_meta', true) : 1;

	?>
	<div id="related_links_blender_admin">
	<div id="this_post_id_wrapper"><h2 >This post ID: <span id="post_id"><?=$post->ID?></span></h2></div>
    
	<div id="current_links_wrapper">
	<img src='../wp-admin/images/loading.gif' />
</div>
     <hr />
	<div id="controls_wrapper">
    
        <div id="add_a_link_wrapper">
              <input id="add_link_button" type="button"  name="add_link_button" value="ADD LINK" data-post_id="<?=$post->ID?>"/>
              <input id="clear_button" type="button"  name="clear_button" value="CLEAR" data-post_id="<?=$post->ID?>"/>
              
          <table width="100%" border="0">
              <tr>
                <td ><label for="text_title">Title</label></td>
                <td colspan="3"><input id="" class="text_title add_a_link_input" type="text"  name="text_title" /></td>
              </tr>
              <tr>
                <td><label for="text_intro">Text</label></td>
                <td colspan="3"><textarea id="" class="text_intro add_a_link_input" rows="2" type="text"  name="text_intro" ></textarea></td>
              </tr>
              <tr>
                <td><label for="link_url">Link</label></td>
                <td colspan="2"><input id="" class="link_url add_a_link_input" type="text"  name="link_url" /></td>
                <td ><label for="link_target">New Window</label><input id="" class="link_target add_a_link_input" type="checkbox"  name="link_target" /></td>	
              </tr>
              <tr>
                <td colspan="2"><label for="link_title">Link Meta Title</label></td>
                <td colspan="2"><input id="" class="link_title add_a_link_input" type="text"  name="link_title" /></td>
              </tr>
              <tr>
                <td colspan="2"><label for="thumb_alt">Thumb Alt Text</label></td>
                <td colspan="2"><input id="" class="thumb_alt add_a_link_input" type="text"  name="thumb_alt" value="" /></td>
              </tr>
              <tr>
                <td width="40px"></td>
                <td width="80px"></td>
                <td ></td>
                <td width="120px"></td>
              </tr>
            </table>
            <input id="" class="thumb_url" type="hidden" size="100" name="thumb_url" value="">
            <div id="add_a_link_thumb_wrapper">
                <div id="thumb_file_name"></div>
                <div id="add_a_link_thumb">
                    <span>Click to choose</span>
                    <img width="<?=$rlb_options['rlb_thumb_width']?>" height="<?=$rlb_options['rlb_thumb_height']?>" src="" />
                </div>
            </div>
   	  </div>
 	</div><!--controls_wrapper-->
   <hr />
                 <button id="find_posts_button" type="button"  name="find_posts_button" value="" >FIND A POST</button>
                 <span id="find_posts_hidden_controls" class="rlb-hidden">
                 <button class="find_posts_button_search " type="button"  name="find_posts_button" value="date" text="">LATEST</button>
                    <button class="find_posts_button_search " type="button"  name="find_posts_button" value="tag"  text="" >TAGS</button>
                    <button class="find_posts_button_search " type="button"  name="find_posts_button" value="category"  text="" >CATEGORIES</button>
                 <label for="find_posts_keywords" >Focus Words<input id="find_posts_keywords" type="text" size="50" name="find_posts_keywords" /></label><br />
					<div id="link_picker_wrapper"></div>
				</span>



    <hr />
    	<p>Method 1:Use the fields above to build a link, don't forget to use the thumb chooser to the right if you want.  ADD LINK when ready, it will appear above.
        If you have existing links you can COPY them into this form for editing, after which you will have to  ADD LINK and DELETE the old one. Use FIND A POST to search through your existing posts, starting with the LATEST or TAGS or CATAGORIES then refine with focus words. Click your choice to add all it's info to the ADD LINK fields then edit them. </p>
     <hr />
       <p>Legacy Method:<input id="rlb_meta[related_links_by_id]" type="text" width="10" name="rlb_meta[related_links_by_id]" value="<?=$rlb_meta[related_links_by_id]?>" />
        <label >Comma seperated list of posts to link.  These are displayed to visitors automatically, contents drawn from the posts and meta.</label></p>

    </div><!--.related_links_blender_admin-->


<?php
}

/** 
 * When the post is saved, saves our custom data 
 */
function rlb_save_post_options( $post_id ) {



  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( !wp_verify_nonce( @$_POST[$_POST['post_type'] . '_noncename'], plugin_basename( __FILE__ ) ) )
      return;

  // Check permissions
  $permission_to_edit=false;
  if ( 'post' == $_POST['post_type'] && current_user_can( 'edit_post', $post_id ) ) $permission_to_edit=true;
  if ( 'page' == $_POST['post_type'] && current_user_can( 'edit_page', $post_id ) ) $permission_to_edit=true;


  // OK, we're authenticated: we need to find and save the data
  if($permission_to_edit) {
		  $current_links=get_post_meta($post_id,'related_links_blender_links',true);
		  if ($current_links) {
		  	$outcome=$this->smush_the_links($post_id,$current_links);
		  }
          update_post_meta( $post_id, 'rlb_meta', $_POST['rlb_meta'] );
  } 


}


} //end class


?>