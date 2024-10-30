<?php defined('ABSPATH') or die("No direct access allowed");

/* Shortcodes Controller class */

class UscCronblocks_shortcodes extends UscCronblocks
{
	public $shortcode_tag      = 'usc_cb_group';
	public $shortcode_column_name = 'usc_cb_shortcode';
	public $shortcode_tag_tmpl = '[usc_cb_group id=%d]';
	private $v       = null;
	private $geo_obj = null;
	
	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();

// 		add_filter('usc_cb_get_shortcode_obj', array(&$this, 'return_self'),10,1);
		add_filter('usc_cb_get_group_shortcode_method', array(&$this, 'return_group_shortcode_method_name'),10,1);
		
		add_filter('usc_cb_process_geo_ctl', array(&$this, 'process_geo_ctl'), 10, 4);
		add_filter('usc_cb_process_sched_ctl', array(&$this, 'process_sched_ctl'), 10, 3);
	}
	
	
	public function return_self()
	{
		return $this;
	}
	
	public function return_group_shortcode_method_name()
	{
		return 'call_snippet_group_shortcode';
	}
	
	public function call_snippet_group_shortcode( $atts )
	{
		if (! isset( $this->v ))
			$this->v = $this->load_lib('v/shortcodes_view');
		
		return $this->v->do_snippet_group_shortcode( $atts );
	}
	
	
	/**
	 * @method process_geo_ctl
	 * @desc Check if the snippet should be added to the valid list per geolocation
	 * @param array $snippets
	 * @param string $ip
	 * @param object $snippet
	 * @param object $meta
	 * @return array
	 */
	public function process_geo_ctl($snippets=array(), $ip, $snippet, $meta)
	{
		if (null === $this->geo_obj)
		{
			$geo_dat = $this->env->inc_dir . 'assets/geo/GeoIP.dat';
			require_once($this->env->lib_dir . 'maxmind/geoip.class.php');
				
			$this->geo_obj = geoip_open($geo_dat,GEOIP_STANDARD);
		}
	
		if ('any' === $meta->geo->country)
		{
			$snippets[$meta->weight][] = $snippet;
		}
		else
		{
			// Only load this if we found an IP
			if (false !== $ip)
				$geo_info = apply_filters('usc_cb_get_geoinfo', geoip_country_code_by_addr($this->geo_obj, $ip));
		
			if ($meta->geo->country === $geo_info)
			{
// 				if ('any' === $meta->geo->region || $geo_info->region === $meta->geo->region)
// 				{
					$snippets[$meta->weight][] = $snippet;
// 				}
			}
		}
		
		return $snippets;
	}
	
	
	/**
	 * @method process_sched_ctl
	 * @desc Check if the snippet should be added to the valid list per scheduling
	 * @param array $snippets
	 * @param object $snippet
	 * @param object $meta
	 * @return array
	 */
	public function process_sched_ctl($snippets=array(), $snippet, $meta)
	{
		$timestamp = date('r');
		$do_check_time = false;
		
		if ('weekly' === $meta->sched_type)
		{
			$weekday = strtolower(substr($timestamp,0,3));
			
			if ($meta->weekdays->$weekday)
				$do_check_time = true;
		}
		else
		{
			// Monthly
			//Thu, 07 Nov 2013 07:56:26 -0200"
			$month = strtolower(substr($timestamp, 8, 3));
			if ($meta->months->$month)
			{
				$day = (int) substr($timestamp, 5, 2);
				if ($meta->days->{'day_'.$day})
					$do_check_time = true;
			} 
		}
		
		if ($do_check_time)
		{
			$start_time = strtotime($meta->time->start_hour . ':' . $meta->time->start_minute);
			$end_time   = strtotime($meta->time->end_hour . ':' . $meta->time->end_minute);
			
			$now = time();
			
			if ($now >= $start_time && $now <= $end_time)
			{
				$snippets[$meta->weight][] = $snippet;
			}
		}
		
		return $snippets;
		
	}
	
	function __destruct()
	{
		if ( null !== $this->geo_obj )
			geoip_close($this->geo_obj);
	}
	
}

/* End of file shortcodes.class.php */
/* Location: cronblocks/includes/lib/c/shortcodes.class.php */