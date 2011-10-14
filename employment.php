<?php

    add_action( 'init', 'register_cpt_employment' );
		function register_cpt_employment() {
			$labels = array(
				'name' => _x( 'Employment', 'employment' ),
				'singular_name' => _x( 'Employment', 'employment' ),
				'add_new' => _x( 'Add New', 'employment' ),
				'add_new_item' => _x( 'Add New Employment', 'employment' ),
				'edit_item' => _x( 'Edit Employment', 'employment' ),
				'new_item' => _x( 'New Employment', 'employment' ),
				'view_item' => _x( 'View Employment', 'employment' ),
				'search_items' => _x( 'Search Employment', 'employment' ),
				'not_found' => _x( 'No employment found', 'employment' ),
				'not_found_in_trash' => _x( 'No employment found in Trash', 'employment' ),
				'parent_item_colon' => _x( 'Parent Employment:', 'employment' ),
				'menu_name' => _x( 'Employment', 'employment' ),
		);
			$args = array(
				'labels' => $labels,
				'hierarchical' => true,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_icon' => 'employment.png',
				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => true,
				'capability_type' => 'post'
		);
    register_post_type( 'employment', $args );
    } 


function add_my_meta_boxes() {
    add_meta_box('my-meta-box', 'Employment Shortcode', 'show_my_meta_box', 'employment', 'side');
}
add_action('add_meta_boxes', 'add_my_meta_boxes');
function show_my_meta_box($post) {
    echo "To list all Work Experience in a post or page used the shortcode [employment].";
}
function add_work_info_meta_boxes() {
    add_meta_box('my-job-info-box', 'Employment Shortcode', 'show_my_job-info_box', 'employment', 'normal', 'high');
}
add_smart_meta_box('my-job-info-box', array(
    'title'     => 'Position Details',
    'pages'     => array('employment'),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
        array(
            'name' => 'Start date of Employment',
            'id' => 'work-date-start',
            'default' => 'default',
            'desc' => 'Please list the time period in which you worked in this position, if this is your current job please check the checkbox.',
            'type' => 'mini',
        ),
        array(
            'name' => 'End date of Employment',
            'id' => 'work-date-end',
            'default' => 'default',
            'desc' => 'Please list the time period in which you worked in this position, if this is your current job please check the checkbox.',
            'type' => 'mini',
        ),		
        array(
            'name' => 'Company Name',
            'id' => 'work-company',
            'default' => 'default',
            'desc' => 'Company Name.',
            'type' => 'text',
        ),	
        array(
            'name' => 'Location',
            'id' => 'work-location',
            'default' => 'default',
            'desc' => 'List the City and State of where you held your job.',
            'type' => 'text',
        ),			
        array(
            'name' => 'Additional title',
            'id' => 'work-subtitle',
            'default' => 'default',
            'desc' => 'If you had an additional job title in the same capacity please list it here.',
            'type' => 'text',
        ),		
        array(
            'name' => 'Textarea',
            'id' => 'smb_textarea',
            'default' => 'default',
            'desc' => 'Description',
            'type' => 'textarea',
        ),
        
   )
   ));
   

class SmartMetaBox {
	
	protected $meta_box;
	
	protected $id;
	static $prefix = '_smartmeta_';

	// create meta box based on given data
	
	public function __construct($id, $opts) {
		if (!is_admin()) return;
		$this->meta_box = $opts;
		$this->id = $id;
		add_action('add_meta_boxes', array(&$this,
			'add'
		));
		add_action('save_post', array(&$this,
			'save'
		));
	}

	// Add meta box for multiple post types
	
	public function add() {
		foreach ($this->meta_box['pages'] as $page) {
			add_meta_box($this->id, $this->meta_box['title'], array(&$this,
				'show'
			) , $page, $this->meta_box['context'], $this->meta_box['priority']);
		}
	}

	// Callback function to show fields in meta box
	
	public function show($post) {

		// Use nonce for verification
		echo '<input type="hidden" name="' . $this->id . '_meta_box_nonce" value="', wp_create_nonce('smartmetabox' . $this->id) , '" />';
		echo '<table class="form-table">';
		foreach ($this->meta_box['fields'] as $field) {
			extract($field);
			$id = self::$prefix . $id;
			$value = self::get($field['id']);
			if (empty($value) && !sizeof(self::get($field['id'], false))) {
				$value = isset($field['default']) ? $default : '';
			}
			echo '<tr>', '<th style="width:20%"><label for="', $id, '">', $name, '</label></th>', '<td>';
			include "fields/$type.php";
			if (isset($desc)) {
				echo '&nbsp;<span class="description">' . $desc . '</span>';
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}

	// Save data from meta box
	
	public function save($post_id) {

		// verify nonce
		if (!isset($_POST[$this->id . '_meta_box_nonce']) || !wp_verify_nonce($_POST[$this->id . '_meta_box_nonce'], 'smartmetabox' . $this->id)) {
			return $post_id;
		}

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ('post' == $_POST['post_type']) {
			if (!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
		foreach ($this->meta_box['fields'] as $field) {
			$name = self::$prefix . $field['id'];
			if (isset($_POST[$name]) || isset($_FILES[$name])) {
				$old = self::get($field['id'], true, $post_id);
				$new = $_POST[$name];
				if ($new != $old) {
					self::set($field['id'], $new, $post_id);
				}
			} elseif ($field['type'] == 'checkbox') {
				self::set($field['id'], 'false', $post_id);
			} else {
				self::delete($field['id'], $name);
			}
		}
	}
	static function get($name, $single = true, $post_id = null) {
		global $post;
		return get_post_meta(isset($post_id) ? $post_id : $post->ID, self::$prefix . $name, $single);
	}
	static function set($name, $new, $post_id = null) {
		global $post;
		return update_post_meta(isset($post_id) ? $post_id : $post->ID, self::$prefix . $name, $new);
	}
	static function delete($name, $post_id = null) {
		global $post;
		return delete_post_meta(isset($post_id) ? $post_id : $post->ID, self::$prefix . $name);
	}
};
function add_smart_meta_box($id, $opts) {
	new SmartMetaBox($id, $opts);
}



function employment_shortcode()
{
	$droidsans = 'Droid Sans';
    //The Query
    query_posts('post_type=employment');
echo '<img src="../wp-content/plugins/CV-Resume/img/employment.png" width="50" style="float:left; margin: 5px 6px 0pt -17px" />';
echo '<h2 class="profile_title">Employment</h2>';
    //The Loop
	global $post;
    if ( have_posts() ) : while ( have_posts() ) : the_post();
		echo '<div class="employment">';
		echo '<h3>'; echo the_title(); echo ' / '; echo '<span style="color:rgba(0,204,51,.5)">'; echo SmartMetaBox::get('work-company'); echo '</span></h3>';
		echo '<h4 class="profile_subtitle">';
		echo SmartMetaBox::get('work-subtitle');
		echo '</h4>';
		echo '<p class="job_date">';
		echo SmartMetaBox::get('work-date-start');
		echo ' ~ ';
		echo SmartMetaBox::get('work-date-end');
		echo '</p>';
        echo the_content();
		
		echo '</div>';
		echo '<div class="clearfix"></div>';
		
		 echo '<style type="text/css">
		 .employment h3 {font-family: Droid Sans, arial, san-serif; font-size:18px !important; font-weight: normal !important; background:none; width:700px; padding-bottom:20px}
		 h2.profile_title {font-family: Droid Sans, arial, san-serif; font-size:24px !important; background:none; margin: 25px 0 -34px;}
		 h4.profile_subtitle {font-family: Droid Sans, arial, san-serif; font-size:13px !important; background:none; margin: -28px 10px 10px !important;}
		 .employment p.job_date {float:right; margin-top: -28px;  border-bottom: 1px solid rgba(0, 0, 0, 0.2); border-top: 1px solid rgba(0, 0, 0, 0.2); width: 115px; line-height:13px;text-align:center;}
.employment {float:right; width:70%; padding-top:20px}
.clear {
  clear: both;
  display: block;
  overflow: hidden;
  visibility: hidden;
  width: 0;
  height: 0;
}
.clearfix:before,
.clearfix:after {
  display: block;
  overflow: hidden;
  visibility: hidden;
  width: 0;
  height: 0;
}
.clearfix:after {
  clear: both;
}
.clearfix {
  zoom: 1;
}
</style>';
    endwhile; else:
    endif;

        //Reset Query
    wp_reset_query();
	echo '<hr style="float:right; width:80%" /><br />
<br />';
}
add_shortcode('employment', 'employment_shortcode');

function dw_change_default_job_title( $title ){
     $screen = get_current_screen();
 
     if  ( 'employment' == $screen->post_type ) {
          $title = 'Enter Position/Job Title';
     }
 
     return $title;
}
 
add_filter( 'enter_title_here', 'dw_change_default_job_title' );

?>