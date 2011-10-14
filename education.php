<?php
    add_action( 'init', 'register_cpt_education' );
		function register_cpt_education() {
			$labels = array(
				'name' => _x( 'Education', 'education' ),
				'singular_name' => _x( 'Education', 'education' ),
				'add_new' => _x( 'Add New', 'education' ),
				'add_new_item' => _x( 'Add New Education', 'education' ),
				'edit_item' => _x( 'Edit Education', 'education' ),
				'new_item' => _x( 'New Education', 'education' ),
				'view_item' => _x( 'View Education', 'education' ),
				'search_items' => _x( 'Search Education', 'education' ),
				'not_found' => _x( 'No education found', 'education' ),
				'not_found_in_trash' => _x( 'No education found in Trash', 'education' ),
				'parent_item_colon' => _x( 'Parent Education:', 'education' ),
				'menu_name' => _x( 'Education', 'education' ),
    	);
			$args = array(
				'labels' => $labels,
				'hierarchical' => true,
				'supports' => array( 'title', 'editor', 'thumbnail' ),
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => true,
				'menu_icon' => 'education.png',
				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => true,
				'capability_type' => 'post'
		);
    register_post_type( 'education', $args );
    }


function add_edu_info_meta_boxes() {
    add_meta_box('my-edu-info-box', 'Education Details', 'show_my_edu-info_box', 'education', 'normal', 'high');
}
add_edu_meta_box('my-edu-info-box', array(
    'title'     => 'Education Details',
    'pages'     => array('education'),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
        array(
            'name' => 'Graduation Year',
            'id' => 'edu-grad',
            'default' => 'default',
            'desc' => 'Please list the time period in which you worked in this position, if this is your current job please check the checkbox.',
            'type' => 'mini',
        ),
        array(
            'name' => 'Major',
            'id' => 'edu-major',
            'default' => 'default',
            'desc' => 'Company Name.',
            'type' => 'text',
        ),	
        array(
            'name' => 'Minor',
            'id' => 'edu-minor',
            'default' => 'default',
            'desc' => 'List the City and State of where you held your job.',
            'type' => 'text',
        ),			
        array(
            'name' => 'Degree Type',
            'id' => 'edu-degree',
            'default' => 'default',
            'desc' => 'If you had an additional job title in the same capacity please list it here.',
            'type' => 'select',
            'options' => array(
                'B.A.' => 'B.A.',
                'B.S.' => 'B.S.',
                'M.A.' => 'M.A.',
                'M.S.' => 'M.S.',
                'PhD' => 'PhD',
                'MeD' => 'MeD',
                'MD' => 'MD',
                'DDS' => 'DDS',
            )
        ),		
        array(
            'name' => 'Awards & Honors',
            'id' => 'edu-awards',
            'desc' => 'Description',
            'type' => 'textarea',
        ),
        
   )
   ));
   

class SmartEduMetaBox {
	
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
function add_edu_meta_box($id, $opts) {
	new SmartEduMetaBox($id, $opts);
}



function education_shortcode()
{
    //The Query
    query_posts('post_type=education');
echo '<img src="../wp-content/plugins/CV-Resume/img/education.png" width="50" style="float:left; margin: 5px 6px 0pt -17px" />';
echo '<h2 class="profile_title">Education</h2>';
    //The Loop
	global $post;
	
   if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    
<div class="education">
    	<h3><?php echo the_title(); ?></h3>
        <p class="edu-title"><strong>Graduation: </strong>
		<?php echo SmartEduMetaBox::get('edu-grad'); ?>
        </p>
        <p class="edu-title"><strong>Major: </strong>
		<?php echo SmartEduMetaBox::get('edu-major');?>
        </p>
        <?php if (SmartEduMetaBox::get('edu-minor') ) { ?>
        <p class="edu-title"><strong>Minor: </strong>
		<?php echo SmartEduMetaBox::get('edu-minor');?>
        </p>
        <?php } ?>
        <p class="edu-title"><strong>Type of Degree: </strong>
		<?php echo SmartEduMetaBox::get('edu-degree');?>
        </p>
        <?php if (SmartEduMetaBox::get('edu-awards') ) { ?>
        <p class="edu-title"><strong>Awards & Honors: </strong>
		<?php echo SmartEduMetaBox::get('edu-awards');	?>		
        <?php } ?>
		</p>
        </div>
        <style type="text/css">
		 h3 {font-family: Droid Sans, arial, san-serif; font-size:18px !important; font-weight: normal !important; background:none;}
		 h2.profile_title {font-family: Droid Sans, arial, san-serif; font-size:24px !important; background:none; margin: 25px 0 -34px;}
.education {float:right; width:70%; padding-top:20px;}
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
</style>
        
	<?php /*	echo '<div class="education">';
		echo '<h3>'; echo the_title(); echo '</h3>';
		echo '<p class="edu-title"><strong>Graduation: </strong>';
		echo SmartEduMetaBox::get('edu-grad');
		echo '</p>';
        echo '<p class="edu-title"><strong>Major: </strong>';
		echo SmartEduMetaBox::get('edu-major');
		echo '</p>';
		echo '<p class="edu-title"><strong>';
		echo $minortitle;
		echo $msg;
		echo '</strong>';
		echo SmartEduMetaBox::get($eduminor);
		echo $minorend;
		echo '<p class="edu-title"><strong>Type of Degree: </strong>';
		echo SmartEduMetaBox::get('edu-degree');		
		echo '</p>';
		echo '<p class="edu-title"><strong>Awards & Honors: </strong>';
		echo SmartEduMetaBox::get('edu-awards');			
		echo '</p>';
		echo '</div>';
		echo '<div class="clearfix"></div>';
		 echo '<style type="text/css">
		 h3 {font-family: Droid Sans, arial, san-serif; font-size:18px !important; font-weight: normal !important; background:none;}
		 h2.profile_title {font-family: Droid Sans, arial, san-serif; font-size:24px !important; background:none; margin: 25px 0 -34px;}
.education {float:right; width:70%; padding-top:20px;}
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
</style>'; */?>
<?php
    endwhile; else:
    endif;

        //Reset Query
    wp_reset_query();
}

add_shortcode('education', 'education_shortcode');
	
function dw_change_default_school_name( $title ){
     $screen = get_current_screen();
 
     if  ( 'education' == $screen->post_type ) {
          $title = 'Enter School Name';
     }
 
     return $title;
}
 
add_filter( 'enter_title_here', 'dw_change_default_school_name' ); 
?>