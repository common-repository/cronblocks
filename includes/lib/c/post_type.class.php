<?php defined('ABSPATH') or die("No direct access allowed");
/* Snippet Post Type class */

class UscCronblocks_post_type extends UscCronblocks
{
	static private $ungrouped_slug = 'usc_cb_ungrouped';
	
	function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	/**
	 * @package UscCronblocksPostType
	 * @method create_post_type
	 * @desc Creates snippet post type 
	 */
	public function create_post_type()
	{
		$snippet_labels = array(
				'name'                => _x( 'Snippets', 'Post Type General Name', $this->domain ),
				'singular_name'       => _x( 'Snippet', 'Post Type Singular Name', $this->domain ),
				'menu_name'           => __( 'Cronblocks', $this->domain ),
				'all_items'           => __( 'All Snippets', $this->domain ),
				'view_item'           => __( 'View Snippet', $this->domain ),
				'add_new_item'        => _x( 'Add New', $this->post_type ),
				'add_new'             => __( 'New Snippet', $this->domain ),
				'edit_item'           => __( 'Edit Snippet', $this->domain ),
				'update_item'         => __( 'Update Snippet', $this->domain ),
				'search_items'        => __( 'Search Snippets', $this->domain ),
				'not_found'           => __( 'No Snippets Found', $this->domain ),
				'not_found_in_trash'  => __( 'No Snippets found in Trash', $this->domain ),
		);
		
		$snippet_args = array(
				'label'               => __( 'snippets', $this->domain ),
				'description'         => __( 'Snippets', $this->domain ),
				'labels'              => $snippet_labels,
				'supports'            => array( 'title', 'editor', 'revisions' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => 5,
				'menu_icon'           => $this->env->img_url . 'cblocks-icon-16.png',
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'taxonomies'          => array($this->snippet_group),
		);
		
		$snippet_args = apply_filters('usc_cb_snippet_args', $snippet_args);

		register_post_type($this->post_type, $snippet_args);
	}
	
	/**
	 * @package UscCronblocksPostType
	 * @method create_group_taxonomy
	 * @desc Creates the groups associated with the snippets
	 */
	function create_group_taxonomy() {

		$labels = array(
				'name'              => _x( 'Groups', 'taxonomy general name', $this->domain ),
				'singular_name'     => _x( 'Group', 'taxonomy singular name', $this->domain ),
				'search_items'      => __( 'Search Snippet Groups', $this->domain ),
				'all_items'         => __( 'All Snippet Groups', $this->domain ),
				'parent_item'       => __( 'Parent Snippet Group', $this->domain ),
				'parent_item_colon' => __( 'Parent Snippet Group:', $this->domain ),
				'edit_item'         => __( 'Edit Snippet Group', $this->domain ),
				'update_item'       => __( 'Update Snippet Group', $this->domain ),
				'add_new_item'      => __( 'Add New Snippet Group', $this->domain ),
				'new_item_name'     => __( 'New Snippet Group Name', $this->domain ),
				'menu_name'         => __( 'Snippet Groups', $this->domain ),
		);
	
		$args = array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => false,
				'update_count_callback' => '_update_post_term_count',
				'show_in_nav_menus' => false,
				'show_tagcloud'     => false,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => $this->snippet_group ),
		);
	
		$args = apply_filters('usc_cb_snippet_group_args', $args);
		
		register_taxonomy( $this->snippet_group, $this->post_type, $args );
	}
	
	
	/**
	 * @method handle_tax_actions
	 * @desc Remove the 'delete' link from the default group
	 * @param array $actions
	 * @param string $tag
	 * @return array $actions
	 */
	public function handle_tax_actions($actions, $tag)
	{
		if ($tag->slug === self::$ungrouped_slug)
			unset($actions['delete']);
		
		return $actions;
	}
	
	
	/**
	 * @method disallow_default_group_delete
	 * @param string $action
	 * @param int $result
	 * @return int $result
	 */
	public function disallow_default_group_delete($action, $result=1)
	{
		if (empty($_REQUEST['taxonomy'])
			|| empty($_REQUEST['action'])
			|| $_REQUEST['taxonomy'] !== $this->snippet_group
			|| !( $_REQUEST['action'] === 'delete' || $_REQUEST['action'] === 'delete-tag'))
		{
			return;
		}
		
		$term_id = $this->get_default_group_id();

		$prefix = 'delete-tag_';
		if (strpos($action, $prefix) !== 0)
			return;
		
		$action_id = substr($action, strlen($prefix));
		$group_id = max(0, (int) $action_id);
		
		if ($group_id == $term_id)
		{
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
				wp_die( -1 );
			else 
				die( '-1' );
		}
		
		return $result;
	}
	
	
	/**
	 * @method create_default_term()
	 * @desc Called on register_activation_hook(), creates the default term to be used with snippets
	 */
	public function create_default_term()
	{
		// Need to register the post_type and taxonomy here
		$this->create_post_type();
		$this->create_group_taxonomy();
		
		$term_exists = $this->get_default_group_id();

		if (! $term_exists )
		{
			wp_insert_term(
				__('Ungrouped',$this->domain), // the term
				$this->snippet_group, // the taxonomy
				array(
					'description'=> __('Term for ungrouped snippets',$this->domain),
					'slug' => self::$ungrouped_slug,
				)
			);
		}
	}
	
	/**
	 * @method get_default_group_id()
	 * @desc Returns the default term ID to be used with snippets
	 */
	public function get_default_group_id()
	{
		$default = get_term_by('slug', self::$ungrouped_slug, $this->snippet_group);
		
		return isset($default) ? $default->term_id : null;
	}
	
	
	/**
	 * @method add_snippet_columns
	 * @desc Adds the weight to our Snippet Library list
	 * @param array $columns
	 * @return array
	 */
	public function add_snippet_columns($columns)
	{
		if (! isset($columns['usc_cb_weight']))
		{
			$offset = 3;
			$columns = array_slice($columns, 0, $offset, true) +
					array('usc_cb_weight' => __('Weight', $this->domain)) +
					array_slice($columns, $offset, NULL, true);
		}
		
		return $columns;
	}
	
	
	/**
	 * @method populate_snippet_columns
	 * @param array $column
	 * @param int $post_id
	 */
	public function populate_snippet_columns($column, $post_id)
	{
		if ('usc_cb_weight' === $column)
		{
			$data = (object) get_post_meta($post_id, $this->geosched_meta_name, true);
			
			echo $data->weight;
		}
	}
	
	
	/**
	 * @method add_snippet_group_columns
	 * @desc Adds the weight to our Snippet Library list
	 * @param array $columns
	 * @return array
	 */
	public function add_snippet_group_columns($columns)
	{
		if (! isset($columns['usc_cb_shortcode']))
		{
			$offset = 4;
			$columns = array_slice($columns, 0, $offset, true) +
			array('usc_cb_shortcode' => __('Shortcode')) +
			array_slice($columns, $offset, NULL, true);
		}
	
		return $columns;
	}
	
	
	/**
	 * @method populate_snippet_group_columns
	 * @param array $column
	 * @param int $term_id
	 */
	public function populate_snippet_group_columns($undef, $column, $term_id)
	{
		$sc = $this->load_lib('c/shortcodes');
		
		$this->sc = apply_filters('usc_cb_get_shortcode_obj', $sc);
		
		if ($this->sc->shortcode_column_name === $column)
			printf($this->sc->shortcode_tag_tmpl,$term_id);
	}
}

/* End of file post_type_class.php */
/* Location: cronblocks/lib/post_type_class.php */