<?php defined('ABSPATH') or die("No direct access allowed");

/* Shortcodes View class */

class UscCronblocks_shortcodes_view extends UscCronblocks
{
	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	public function do_snippet_group_shortcode( $atts )
	{
		$args = shortcode_atts( array(
					'id' => null,
					'ip' => $this->get_client_ip(),
		),
				 $atts);
		
		if (! isset( $args['id'] ) || null === $args['id'] )
			return 'Missing Snippet Group ID';
		
		if (! preg_match('/^\d+$/', $args['id']))
			return 'Invalid Snippet Group ID';
		
		$term = get_term($args['id'], $this->snippet_group);
		
		// Get posts for snippet
		$tax_args = array(
				'tax_query' => array(
						array(
								'taxonomy' => $this->snippet_group,
								'field' => 'slug',
								'terms' => $term->slug
						)
				),
				'post_type'   => $this->post_type,
				'post_status' => 'publish',
		);
		
		
		$query = new WP_Query( $tax_args );
		
		if (! $query->post_count )
			return sprintf('<!-- No snippets found for Group ID %d -->',$args['id']);
		

		$valid_snippets = array();
		foreach ($query->posts as $snippet)
		{
			// Get meta
			$meta = (object) apply_filters('usc_cb_get_geosched_meta',array(),$snippet->ID);
			
			// Get ctrl_type && Add if ctrl matches
			if ('use_geolocation' === $meta->ctl_type)
			{
				$valid_snippets = apply_filters('usc_cb_process_geo_ctl', $valid_snippets, $args['ip'], $snippet, $meta);
			}
			else if ('use_scheduling' === $meta->ctl_type)
			{
				$valid_snippets = apply_filters('usc_cb_process_sched_ctl', $valid_snippets, $snippet, $meta); 
			}
			
		}
		
		// Sort highest to lowest weight
		krsort($valid_snippets);
		
		$high = array_shift($valid_snippets);
		
		if (count($high) > 1)
			shuffle($high);
		
		// Return heaviest
		$content = wpautop(do_shortcode($high[0]->post_content));
		 
		return $this->load_template('snippets/snippet_display', 
									array('group_id' => $args['id'], 'content' => $content, 'snippet_id' => $high[0]->ID),
									true);
		
	}
	
	private function get_client_ip()
	{
		$ipaddress = '';
		
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if(isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if(isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = '127.0.0.1';
			
			
		if ('127.0.0.1' === $ipaddress)
			$ipaddress = $this->fetch_testing_ip(); # this has got to be a local testing machine, so fetch the external address
		
		return $ipaddress;
	}
	
	
	private function fetch_testing_ip()
	{
		$request = (object) wp_remote_get('http://checkip.dyndns.org');
		
		if (is_wp_error($request))
			return false;
		
		preg_match('/Current IP Address: ([^<]+)/', $request->body, $matches);
		
		return $matches[1];
	}
}


/* End of file shortcodes_view.class.php */
/* Location: cronblocks/includes/lib/v/shortcodes_view.class.php */