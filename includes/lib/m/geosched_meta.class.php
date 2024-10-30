<?php defined('ABSPATH') or die("No direct access allowed");

/* Cronblocks GeoSchedule Meta Model class */
class UscCronblocks_geosched_meta extends UscCronblocks
{
	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	/**
	 * @method geosched_defaults
	 * @desc The default keys so we don't have to check if they exist
	 * @return array $defaults
	 */
	public function geosched_defaults()
	{
		$defaults = array(
				'ctl_type'   => 'use_geolocation',
				'sched_type' => 'weekly',
				'weight'     => 1,
				'geo'        => (object) array('country' => null,'region' => null),
				'weekdays'   => (object) array('mon' => false, 'tue' => false, 'wed' => false,'thu' => false,
											   'fri' => false, 'sat' => false,'sun' => false),
				'time'       => (object) array('start_hour' => '00', 'start_minute' => '00', 
									           'end_hour'   => '23', 'end_minute'   => '59'),
 			 	'months'     => (object) array('jan' => false, 'feb' => false, 'mar' => false, 'apr' => false,
										       'may' => false, 'jun' => false, 'jul' => false, 'aug' => false, 
 			 							       'sep' => false, 'oct' => false, 'nov' => false, 'dec' => false),
				'days'       => null,
		);
		
		$days = array();
		foreach (range(1, 31) as $i)
		{
			$days['day_'.$i] = false;
		}
		
		$defaults['days'] = (object) $days;
		
		return $defaults;
	}
	
	/**
	 * @method get_geosched_meta
	 * @desc Method to return the meta data. Can handle direct calls and filter calls
	 * @param int $arg1
	 * @param string $format
	 * @return array $opts
	 */
	public function get_geosched_meta($arg1, $arg2=null)
	{
		$post_id = is_numeric($arg1) ? $arg1 : $arg2;
		
		$defaults = apply_filters('usc_cb_geosched_defaults', $this->geosched_defaults());
		
		$db_data = get_post_meta($post_id, $this->geosched_meta_name, true);
		
		$opts = wp_parse_args($db_data, $defaults);
		
		
		return $opts;
	}
	
	
	public function save_geosched_meta($post_id, $data)
	{
		if (is_object($data))
			$data = (array) $data;
		
		update_post_meta($post_id, $this->geosched_meta_name, $data);
	}
	
}


/* End of file geo.class.php */
/* Location: cronblocks/includes/lib/m/geo.class.php */