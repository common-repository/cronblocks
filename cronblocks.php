<?php defined('ABSPATH') or die("No direct access allowed");
/*
* Plugin Name:   Cronblocks
* Plugin URI:	 http://usestrict.net/2013/11/cronblocks-for-wordpress/ 
* Description:   Scheduling and Geo-location for post snippets
* Version:       1.0.1
* Author:        Vinny Alves
* Author URI:    http://www.usestrict.net
*
* Copyright (C) 2013 www.usestrict.net
*/
class UscCronblocks 
{
	const VERSION = '1.0.1';
	
	static $instance;
	
	public $domain        = 'cronblocks';
	public $post_type     = 'usc_cb_snippet';
	public $snippet_group = 'usc_cb_snippet_group';
	public $notices;
	
	protected $geosched_meta_name = '_usc_cb_geosched_meta_lite';
	
	private $env_is_set = false;
	private $table_names_are_set = false;
	
	public function __construct()
	{
		$this->set_env();
		
		$this->common_init();
		
		if (is_admin())
			$this->admin_init();
		else 
			$this->front_end_init();
		
		$this->version_update_actions();
		
		register_activation_hook(__FILE__, array(&$this, 'do_install'));
	}
	
	
	/**
	 * @method bootstrap
	 * @desc Singleton
	 * @return UscCronblocks
	 */
	static public function bootstrap()
	{
		if (! self::$instance )
			self::$instance = new self();
		
		return self::$instance;
	}
	

	/**
	 * @method common_init
	 * @desc Do stuff that is common for both admins and Front-end 
	 */
	public function common_init()
	{
		$this->pt_lib    = $this->load_lib('c/post_type');
		$this->gsm_model = $this->load_lib('m/geosched_meta');

		add_action('init', array(&$this->pt_lib,'create_post_type'), 0, 0);
		add_action('init', array(&$this->pt_lib,'create_group_taxonomy'), 1, 0);
		
		add_filter('usc_cb_get_geosched_meta', array(&$this->gsm_model, 'get_geosched_meta'), 10, 3);
		
		if (! has_filter('widget_text','do_shortcode'))
			add_filter('widget_text', 'do_shortcode');
	}
	
	
	/**
	 * @method admin_init
	 * @desc Do stuff that is needed for admin only
	 */
	public function admin_init()
	{
		global $pagenow;
		
		add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));
		
		$this->load_lib('c/snippets_meta'); // Loads snippet meta boxes if needed 
		
		add_action('admin_notices', array(&$this->notices, 'show_notices'));
		
		// Handle get_region ajax calls from the snippet UI
		$this->ajax = $this->load_lib('c/ajax');
		add_action('wp_ajax_' . $this->domain . '-get-regions', array(&$this->ajax,'get_regions'));
		
		add_filter($this->snippet_group . '_row_actions', array(&$this->pt_lib, 'handle_tax_actions'),10,2);
		add_action('check_admin_referer', array(&$this->pt_lib, 'disallow_default_group_delete') );
		add_action('check_ajax_referer', array(&$this->pt_lib, 'disallow_default_group_delete') );
		
		add_filter('usc_cb_default_group_id', array(&$this->pt_lib, 'get_default_group_id'));
		
		add_filter( 'manage_edit-'. $this->post_type .'_columns', array(&$this->pt_lib, 'add_snippet_columns') ) ;
		add_action( 'manage_' . $this->post_type . '_posts_custom_column', array(&$this->pt_lib, 'populate_snippet_columns'), 10, 2 );
		
		add_filter( 'manage_edit-'. $this->snippet_group .'_columns', array(&$this->pt_lib, 'add_snippet_group_columns') ) ;
		add_action( 'manage_' . $this->snippet_group . '_custom_column', array(&$this->pt_lib, 'populate_snippet_group_columns'), 10, 3 );
		
		// Help tabs
		$helper = $this->load_lib('c/context_help');
		add_action('load-post.php', array(&$helper, 'add_snippet_help'));
		add_action('load-post-new.php', array(&$helper, 'add_snippet_help'));
		add_action('load-edit-tags.php', array(&$helper, 'add_snippet_group_help'));
	}
	
	/**
	 * @method front_end_init
	 * @desc Do stuff that is required for Front-End only
	 */
	public function front_end_init()
	{
		$sc = $this->load_lib('c/shortcodes');
		
		$this->sc = apply_filters('usc_cb_get_shortcode_obj', $sc);
		$shortcode_method = apply_filters('usc_cb_get_group_shortcode_method', null);
		
		$func_array = apply_filters('usc_cb_short_code_func', array(&$this->sc, $shortcode_method));
		
		add_shortcode( $this->sc->shortcode_tag, $func_array );
	}
	
	/**
	 * @method admin_scripts
	 * @desc Enqueues the admin scripts
	 */
	public function admin_scripts()
	{		
		wp_enqueue_style('usc_cb_admin_css', $this->env->css_url . 'admin.css', false, self::VERSION, 'screen' );
	}
	
	
	/**
	 * @package UscCronblocks
	 * @method set_env
	 * @desc Sets the environment variables
	 */
	protected function set_env()
	{
		if (true === $this->env_is_set) return;
		
		$this->env = (object) array();
		
		$this->env->base_dir = trailingslashit(dirname(__FILE__));
        $this->env->inc_dir = $this->env->base_dir . 'includes/';
        $this->env->lib_dir = $this->env->inc_dir . 'lib/';
        $this->env->img_dir = $this->env->inc_dir . 'assets/images/';
        $this->env->t_dir   = $this->env->inc_dir . 'templates/';
        $this->env->url     = plugins_url('', __FILE__);
        $this->env->img_url = $this->env->url . '/includes/assets/images/';
        $this->env->css_url = $this->env->url . '/includes/assets/css/';
        $this->env->js_url  = $this->env->url . '/includes/assets/js/';
        $this->env->components = $this->env->url . '/includes/assets/bower_components/';

        if ( ! isset( $this->notices ) )
		{
			require_once($this->env->lib_dir . 'v/notices.class.php');			
			$this->notices = UscCronblocks_notices::bootstrap();
		}
		
		$this->env_is_set = true;
	}
	
	/**
	 * @method load_lib
	 * @desc Includes the library file
	 * @param string $name - the name of the lib file
	 * @param string $alias - the alias given to the lib
	 * @param bool $force_reload - whether to force reloading of the file
	 * @param string $classname - Passed if desire classname is not UscCronblocks$name 
	 */
	protected function load_lib($name, $classname=null, $force_reload=false)
	{
		$this->set_env();
		
		$alias = $name;
		
		if (true === $force_reload || ! isset($this->libs) || ! isset($this->libs->$alias))
		{
			$filename = $this->env->lib_dir . $name . '.class.php';
			if (! file_exists($filename))
			{
				wp_die('Cannot find Lib file: ' . $filename);
			}
			
			require_once($filename);
			
			if (preg_match(',/,',$name))
			{
				$name = substr($name, strpos($name, '/')+1);
			}
			
			if (! $classname) $classname = 'UscCronblocks_' . $name;
			
			if (!isset($this->libs))
				$this->libs = (object) array();
			
			$this->libs->$alias = new $classname($this->env);
		}
		
		return $this->libs->$alias;
	}
	
	
	/**
	 * @method load_template
	 * @desc Includes or returns a template file
	 */
	protected function load_template($name, $params=array(), $want_return=false, $debug=false)
	{
		if ('.tmpl.php' !== substr($name,-9))
			$name .= '.tmpl.php';
		
		foreach($params as $key => $val)
		{
			$$key = $params[$key];
		}

		if ($debug)
			echo $this->env->t_dir . $name;
		
		if ($want_return)
			ob_start();
			
		include ($this->env->t_dir . $name);
		
		if ($want_return)
			return ob_get_clean();
	}
	
	/**
	 * @method do_install
	 * @desc Initial install steps, triggered 
	 */
	public function do_install()
	{
		add_action('activated_plugin', array(&$this->pt_lib, 'create_default_term') );
		update_option('_usc_cb_version', self::VERSION);
	}
	
	/**
	 * @method version_update_actions
	 * @desc Do stuff for a given upgrade action
	 */
	private function version_update_actions()
	{
		$last_version = get_option('_usc_cb_version');
		
		if (!$last_version || version_compare(self::VERSION, $last_version) > 0)
		{
			do_action('usc_cb_upgrade_actions', self::VERSION);
			
			update_option('_usc_cb_version', self::VERSION);
		}
	}
}

$usc_cronblocks = UscCronblocks::bootstrap();

/* End of file cronblocks.php */
/* Location: cronblocks/cronblocks.php */