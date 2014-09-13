<?php
/*
Plugin Name: Calendar for WordPress by CasePress
Plugin URI: 
Description: 
Version: 1.0
Author: CasePress Studio
Author URI: http://casepress.org/
License: GPLv2
*/
class Event_cp {
	public $duration = "";
	public $url = "";
	public function get_event_cp($event_id, $output = 'OBJECT', $filter = 'raw') {
		$post=get_post( $event_id, $output, $filter );
		$this->url = get_post_meta( $event_id, 'url_cp', true );
		switch ($output){
			case 'OBJECT':
				$this->duration = $post->menu_order;
				$merged = (object) array_merge((array) $post, (array) $this);
				return $merged;
			break;
			case 'ARRAY_A':
				$merged = array_merge($post, array('url'=>$this->url,'duration'=>$post['menu_order']));
				return $merged;
			break;
			case 'ARRAY_N':
				array_push($post,$this->url,$post[19]);
				return $post;
			break;
		}
	}
}
add_action( 'init', 'event_cp_init',0);
function event_cp_init(){
    register_post_type( 'event_cp',
        array(
            'labels' => array(
                'name' => 'Events',
                'singular_name' => 'Event',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Event',
                'edit' => 'Edit',
                'edit_item' => 'Edit Event',
                'new_item' => 'New Event',
                'view' => 'View',
                'view_item' => 'View Event',
                'search_items' => 'Search Event',
                'not_found' => 'No Event found',
                'not_found_in_trash' => 'No Event found in Trash',
                'parent' => 'Parent Event'
            ),
            'public' => true,
            'menu_position' => 15,
            'rewrite' => array( 'slug' => 'events' ),
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'has_archive' => true
        )
    );
}
add_action( 'init', 'add_event_taxonomies',0);
function add_event_taxonomies(){
	$labels = array(
		'name' => _x( 'Event Categories', 'taxonomy general name' ),
		'singular_name' => _x( 'Event Category', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Event Categories' ),
		'popular_items' => __( 'Popular Event Categories' ),
		'all_items' => __( 'All Event Categories' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Event Categories' ),
		'update_item' => __( 'Update Event Categories' ),
		'add_new_item' => __( 'Add New Event Categories' ),
		'new_item_name' => __( 'New Event Categories Name' ),
		'separate_items_with_commas' => __( 'Separate Event Categories with commas' ),
		'add_or_remove_items' => __( 'Add or remove Event Categories' ),
		'choose_from_most_used' => __( 'Choose from the most used Event Categories' ),
		'menu_name' => __( 'Event Categories' ),
	);
	register_taxonomy('event_category', 'event_cp',array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
	));
}
add_action('post_submitbox_misc_actions', 'add_extra_fields_cp', 1);

function add_extra_fields_cp($post_id) {
	$screen=get_current_screen();
	if ($screen->post_type == 'event_cp'){
	?>
		<div class="misc-pub-section ">
			<span><label for="_start_date">Начало события</label></span>
			<input type="text" name="_start_date" id="_start_date" style="float:right;width:150px;margin-top: -5px;" value="<?$start_date=get_the_date( "Y-m-d H:i:s", $post_id );echo $start_date;?>">
		</div>
		<div class="misc-pub-section ">
			<span><label for="_end_date">Конец события</label></span>
			<input type="text" name="_end_date" id="_end_date" style="width:150px;float:right;margin-top: -5px;" value="<?global $post;$end_date=date( "Y-m-d H:i:s",strtotime($start_date)+$post->menu_order);echo $end_date;?>">
		</div>
		<div class="misc-pub-section ">
			<span><label for="_url">URL</label></span>
			<input type="text" name="_url" id="_url" style="width:150px;float:right;margin-top: -5px;" value="<?$url=get_post_meta( get_the_ID(), 'url_cp', true );echo $url;?>">
		</div>
		<script>rome(_start_date,{dateValidator: rome.val.beforeEq(_end_date),"timeFormat": "HH:mm:ss","inputFormat": "YYYY-MM-DD HH:mm:ss"})</script>
		<script>rome(_end_date,{dateValidator: rome.val.afterEq(_start_date),"timeFormat": "HH:mm:ss","inputFormat": "YYYY-MM-DD HH:mm:ss"})</script>
	<?
	}
}
add_action( 'wp_enqueue_scripts', 'cp_calendar_enqueue' );
add_action( 'admin_enqueue_scripts', 'cp_calendar_enqueue' );
function cp_calendar_enqueue(){
	wp_register_script('rome_date_picker_script', WP_PLUGIN_URL .'/cp-calendar/js/rome/rome.min.js');
	wp_enqueue_script('rome_date_picker_script');
	wp_register_style('rome_date_picker_style', WP_PLUGIN_URL .'/cp-calendar/js/rome/rome.min.css');
	wp_enqueue_style( 'rome_date_picker_style');
	wp_register_script('moment_calendar_script',WP_PLUGIN_URL . '/cp-calendar/js/fullcalendar/moment.min.js',array('jquery'));
	wp_enqueue_script('moment_calendar_script');
	wp_register_script('full_calendar_script',WP_PLUGIN_URL . '/cp-calendar/js/fullcalendar/fullcalendar.min.js',array('jquery'));
	wp_enqueue_script('full_calendar_script');
	wp_register_style('full_calendar_style', WP_PLUGIN_URL . '/cp-calendar/js/fullcalendar/fullcalendar.min.css');
	wp_enqueue_style( 'full_calendar_style');
}
add_action('save_post', 'extra_fields_update_cp');
function extra_fields_update_cp($post_id) {
	if ( !wp_verify_nonce(@$_POST['extra_fields_nonce'], __FILE__) ) return false;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	if ( !current_user_can('edit_post', $post_id) ) return false;
	if ( isset($_POST['_start_date'])){
		$post_date=$_POST['_start_date'];
		$post_date_gmt = get_gmt_from_date( $post_date );
		$post = array(
			'ID'           => $post_id,
			'post_date' => $post_date,
			'post_date_gmt' => $post_date_gmt
		);
		remove_action( 'save_post', 'my_extra_fields_update' );
		wp_update_post( $post );
		add_action( 'save_post', 'my_extra_fields_update' );
	}
	if (isset($_POST['_end_date'])){
		$end_date=$_POST['_end_date'];
		$duration=strtotime($end_date)-strtotime($post_date);
		$post = array(
			'ID'           => $post_id,
			'menu_order' => $duration
		);
				remove_action( 'save_post', 'my_extra_fields_update' );
		wp_update_post( $post );
		add_action( 'save_post', 'my_extra_fields_update' );
	}
	if(isset($_POST['_url'])){
		update_post_meta( $post_id, 'url_cp', $_POST['_url'] );
	}
	// echo '<pre>';
	// var_dump($_POST);
	// echo '</pre>';
	return $post_id;
}
if(!function_exists('validate_date')){
	function validate_date($date){
		$d = DateTime::createFromFormat('Y-m-d H:i:s', $date);
		return $d && $d->format('Y-m-d H:i:s') == $date;
	}
}
if( ! function_exists('add_event_cp') ) {
	function add_event_cp($start, $end, $title, $description, $event_key, $url) {
		if (is_int($start)){
			$start=date( "Y-m-d H:i:s",$start);
		} elseif (!validate_date($start)){
			return false;
		}
		if (is_int($end)){
			$duration=$end-strtotime($start);
		} elseif(validate_date($end)){
			$duration=strtotime($end)-strtotime($start);
		} else {
			return false;
		}
		$terms=get_terms( 'event_category');
		var_dump($terms);
		// if (is_int($event_key)){
			
		// }
		$post = array(
			'menu_order' => $duration,
			'post_content' => $description,//The full text of the post.
			'post_date' => $start, //The time post was made.
			'post_date_gmt' => get_gmt_from_date( $start ), //The time post was made, in GMT.
			'post_status' => 'private',
			'post_title' => $title, //The title of your post.
			'post_type' => 'event_cp' //You may want to insert a regular post, page, link, a menu item or some custom post type
		); 
		$id=wp_insert_post( $post );
		if ($id){
			update_post_meta( $id, 'url_cp', $url );
			wp_set_object_terms( $id, $event_key, 'event_category' );
		}
		return $id;
	}
}
if( ! function_exists('update_event_cp') ) {
	function update_event_cp($event_id, $start, $end, $title, $description, $event_key, $url) {
		if (is_int($start)){
			$start=date( "Y-m-d H:i:s",$start);
		} elseif (!validate_date($start)){
			return false;
		}
		if (is_int($end)){
			$duration=$end-strtotime($start);
		} elseif(validate_date($end)){
			$duration=strtotime($end)-strtotime($start);
		} else {
			return false;
		}
		$post = array(
			'ID'           => $event_id,
			'menu_order' => $duration,
			'post_content' => $description,//The full text of the post.
			'post_date' => $start, //The time post was made.
			'post_date_gmt' => get_gmt_from_date( $start ), //The time post was made, in GMT.
			'post_status' => 'private',
			'post_title' => $title, //The title of your post.
			'post_type' => 'event_cp' //You may want to insert a regular post, page, link, a menu item or some custom post type
		); 
		$p=wp_update_post( $post );
		if ($p){
			update_post_meta( $event_id, 'url_cp', $url );
			wp_set_object_terms( $event_id, $event_key, 'event_category' );
		}
		return $p;
	}
}
if( ! function_exists('delete_event_cp') ) {
	function delete_event_cp($event_id, $force_delete = false) {
		return wp_delete_post($event_id, $force_delete);
	}
}
if( ! function_exists('get_event_cp') ) {
	function get_event_cp($event_id, $output = 'OBJECT', $filter = 'raw') {
		$event = new Event_cp;
		return $event->get_event_cp($event_id, $output, $filter);
	}
}

add_action('template_redirect', 'template_calendar_load_cp');

function template_calendar_load_cp(){

	if(is_post_type_archive('event_cp')) {
		$tmpl_file_name = 'calendar-cp-list.php';
		if ( $overridden_template = locate_template( array($tmpl_file_name, 'templates/'.$tmpl_file_name) ) ) {
			load_template( $overridden_template );
		} else {
			load_template( plugin_dir_path(__FILE__).$tmpl_file_name, true );
		}
		exit;
	}

}
?>