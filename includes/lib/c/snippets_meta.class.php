<?php defined('ABSPATH') or die("No direct access allowed");

/* Snippet Metabox Controller class */

class UscCronblocks_snippets_meta extends UscCronblocks
{

	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();

		add_action('load-post.php', array(&$this, 'add_meta_boxes'));
		add_action('load-post-new.php', array(&$this, 'add_meta_boxes'));
		
		
		$save_func = apply_filters('usc_cb_save_geosched_meta_func', array(&$this, 'handle_save_geosched_meta'));
		add_action('save_post', $save_func);
	}

	/**
	 * @method add_meta_boxes
	 * @desc Sets actions to add snippet meta boxes 
	 */
	public function add_meta_boxes()
	{
		add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_metabox_js'), 10, 1 );
		add_action( 'add_meta_boxes', array(&$this,'add_geosched_meta'), 10 );
	}
	
	/**
	 * @method add_geosched_meta
	 * @desc Adds the geoscheduling meta boxes. Filter used for pro function
	 */
	public function add_geosched_meta()
	{
		$this->smb = $this->load_lib('v/snippet_meta_box');
		
		$func_array = apply_filters('usc_cb_add_geosched_meta', array(&$this->smb,'render_geosched_box'));
		
		add_meta_box( 'usc-cb-snippet-meta', __( 'Snippet Controls', $this->domain ),
					  $func_array, $this->post_type , 'normal','high' );
	}
	
	/**
	 * @method enqueue_metabox_js
	 * @desc  Adds Javascript for geosched metabox
	 * @param string $hook
	 */
  public function enqueue_metabox_js($hook)
  {
    global $post;
    if ($hook === 'post-new.php' || $hook === 'post.php') {
      if ($this->post_type === $post->post_type) {
        $js_url = apply_filters('usc_cb_metabox_js_url', $this->env->js_url . 'admin/meta_box.js');
        $localized_vars = apply_filters('usc_cb_metabox_localized_vars', array('domain' => $this->domain, 'any' => __('Any', $this->domain)));

        wp_enqueue_script(
          'select2',
          apply_filters('select2', $this->env->components . 'select2/select2.min.js'),
          array('jquery')
        );

        wp_enqueue_script(
          'jquery-knob',
          apply_filters('jquery-knob', $this->env->components . 'jquery-knob/js/jquery.knob.min.js'),
          array('jquery')
        );

        wp_localize_script(
          'usc_cb_metabox_js',
          'usc_cb_metabox_js',
          $localized_vars
        );

        wp_enqueue_script(
          'usc_cb_metabox_js',
          $js_url
        );
      }
    }
  }
	
	/**
	 * @method handle_save_geosched_meta
	 * @desc Hooked to save_post, validates and calls the saving model
	 * @param int $post_id
	 */
	public function handle_save_geosched_meta($post_id)
	{
		if ( ! isset($_POST['save_geosched_meta_nonce']) )
			return $post_id;
		
		$nonce = $_POST['save_geosched_meta_nonce'];
		$v_nonce = wp_verify_nonce( $nonce, 'save_geosched_meta_nonce' );

		// Verify that the nonce is valid.
		if ( ! $v_nonce )
			return $post_id;
		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		// Check the post type
		if ( $this->post_type !== $_POST['post_type'] )
			return $post_id;

		// Sanitize user input.
		$this->m = $this->load_lib('m/geosched_meta');
		$v_data = $this->validate_geosched_data($post_id);

		// Update the meta field in the database.
		$this->m->save_geosched_meta($post_id, $v_data);
		
		// Save the category
		$group_ids = array();
		if ( 1 === count($_POST['tax_input']['usc_cb_snippet_group']) &&  0 === (int) $_POST['tax_input']['usc_cb_snippet_group'][0])
		{
			$group_ids = array( (int) apply_filters('usc_cb_default_group_id') );
		}
		else 
		{
			if (0 === (int) $_POST['tax_input']['usc_cb_snippet_group'])
				array_shift($_POST['tax_input']['usc_cb_snippet_group']);
			
			$group_ids = array_values($_POST['tax_input']['usc_cb_snippet_group']);
			$group_ids = array_map('intval', $group_ids);
			$group_ids = array_unique($group_ids);
		}
				
		wp_set_object_terms( $post_id, $group_ids, $this->snippet_group );
	}

	
	/**
	 * @method validate_geosched_meta
	 * @desc Validation for snippet geosched values
	 * @param unknown $post_id
	 */
	private function validate_geosched_data($post_id)
	{
		isset($this->m) or $this->m = $this->load_lib('m/geosched_meta');
		
		$defaults = (object) $this->m->geosched_defaults();
		
		if ( ! isset($_POST['usc_cb_ctl_type']) )
		{
			$this->notices->set_error( __('Please select a Control Type!', $this->domain) );
		}
		elseif ( 'use_geolocation' !== $_POST['usc_cb_ctl_type'] && 
				 'use_scheduling'  !== $_POST['usc_cb_ctl_type'] )
		{
			$this->notices->set_error( __('Invalid value for Control Type!', $this->domain) ); 	
		}
		else
		{
			$defaults->ctl_type = $_POST['usc_cb_ctl_type'];
		}
		
		
		if (! isset($_POST['usc_cb_weight']) || (int) $_POST['usc_cb_weight'] < 1 || (int) $_POST['usc_cb_weight'] > 10 )
			$this->notices->set_error( __('Invalid value for Weight!', $this->domain) );
		else
			$defaults->weight = (int) $_POST['usc_cb_weight'];
		
		
		$defaults->geo->country = $_POST['usc_cb_country'];
		$defaults->geo->region  = $_POST['usc_cb_region'];
		$defaults->sched_type   = $_POST['usc_cb_sched_type'];

		if (preg_match('/^\d{1,2}$/', $_POST['start_hour']) )
			$defaults->time->start_hour = $_POST['start_hour'];
		
		if (preg_match('/^\d{1,2}$/', $_POST['start_minute']) )
			$defaults->time->start_minute = $_POST['start_minute'];
		
		if (preg_match('/^\d{1,2}$/', $_POST['end_hour']) )
			$defaults->time->end_hour = $_POST['end_hour'];
		
		if (preg_match('/^\d{1,2}$/', $_POST['end_minute']) )
			$defaults->time->end_minute = $_POST['end_minute'];

		$start_time = strtotime($_POST['start_hour'] . ':' . $_POST['start_minute']);
		
		if(false === $start_time)
			$this->notices->set_error( __('Invalid Start Time!', $this->domain) );
		
		$end_time = strtotime($_POST['end_hour'] . ':' . $_POST['end_minute']);

		if(false === $end_time)
			$this->notices->set_error( __('Invalid End Time!', $this->domain) );
		
		if (false !== $start_time && false !== $end_time && $start_time > $end_time)
			$this->notices->set_error( __('End Time must be *after* Start Time!', $this->domain) );
		
		
		
		foreach ( (array) $_POST['usc_cb_weekdays'] as $weekday)
		{
			foreach (array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun') as $wd)
			{
				if ( $weekday === $wd)
				{
					$defaults->weekdays->$wd = true;
					break;
				}
			}
		}
		
		
		foreach ( (array) ($_POST['usc_cb_days']) as $day )
		{
			$i = str_replace('day_','',$day);
			
			if ( is_numeric($i) && 
				 (int) $i >= 1 && 
				 (int) $i <= 31)
			{
				$defaults->days->$day = true;
			}
		}
		
		
		foreach ( (array) $_POST['usc_cb_months'] as $month)
		{
			foreach (array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 
						   'jul', 'aug', 'sep', 'oct', 'nov', 'dec') as $m)
			{
				if ( $month === $m)
				{
					$defaults->months->$m = true;
					break;
				}
			}
		}
		
		return (array) $defaults;
	}
		
}

/* End of file snippets_meta.class.php */
/* Location: cronblocks/includes/lib/c/snippets_meta.class.php */