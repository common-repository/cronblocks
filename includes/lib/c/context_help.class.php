<?php defined('ABSPATH') or die("No direct access allowed");

/* Snippet Context Help Controller class */

class UscCronblocks_context_help extends UscCronblocks
{
	function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	public function add_snippet_help()
	{
		$screen = get_current_screen();
		
		if ($screen->id !== $this->post_type)
			return;
		
		$content = $this->load_template('help/snippet_post_type', array(), true);
		$screen->add_help_tab( array(
	        'id'		=> 'usc_cb_snippet_help_tab',
	        'title'		=> __('Snippet Help', $this->domain),
	        'content'	=> apply_filters('usc_cb_snippet_help', $content),
    	) ); 
	}
	
	public function add_snippet_group_help()
	{
		$screen = get_current_screen();
		
		if ('edit-' . $this->snippet_group !== $screen->id)
			return;
		
		$content = $this->load_template('help/snippet_group', array(), true);
		$screen->add_help_tab( array(
				'id'		=> 'usc_cb_snippet_group_help_tab',
				'title'		=> __('Snippet Group Help', $this->domain),
				'content'	=> apply_filters('usc_cb_snippet_group_help', $content),
		) );
	}
}

/* End of file context_help.class.php */
/* Location: cronblocks/includes/lib/c/context_help.class.php */