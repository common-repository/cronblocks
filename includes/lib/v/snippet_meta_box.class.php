<?php defined('ABSPATH') or die("No direct access allowed");
/* Snippet Metabox View class */

class UscCronblocks_snippet_meta_box extends UscCronblocks
{

	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	
	public function render_geosched_box($post)
	{
		$opts = (object) apply_filters('usc_cb_get_geosched_meta', array(), $post->ID);
		
		$this->m = $this->load_lib('m/geo');

		$countries = $this->m->get_countries();
		
		wp_create_nonce('save_geosched_meta_nonce');
		
		$this->load_template('snippets/snippet_geo_scheduling', array('opts' => $opts, 'countries' => $countries));	
	}
	
}


/* End of file snippet_meta_box.class.php */
/* Location: cronblocks/includes/lib/m/table_setup.class.php */