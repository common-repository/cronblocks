<?php defined('ABSPATH') or die("No direct access allowed");

/* Cronblocks Geo Model class */
class UscCronblocks_geo extends UscCronblocks
{
	static private $country_list;
	
	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	
// 		$this->set_table_names();
	}
	
	
	public function get_countries()
	{
		if (self::$country_list)
			return self::$country_list;
		
		require_once($this->env->lib_dir . 'maxmind/geoip.class.php');
		
		$gi = new GeoIP;
		
		$country_codes = $gi->GEOIP_COUNTRY_CODES;
		$country_names = $gi->GEOIP_COUNTRY_NAMES;
		
		array_shift($country_codes);
		array_shift($country_names);
		
		$mix = array();
		for($i=0; $i<count($country_codes); $i++)
		{
			$mix[ $country_codes[$i] ] = (object) array('country_code' => $country_codes[$i], 'country_name' => $country_names[$i], 'has_regions' => 0); 
		}
		
		// Sort by country name
		$this->aasort($mix,"country_name");
		
		self::$country_list = $mix;
		
		return self::$country_list;
	}


	function aasort(&$array, $key)
	{
		$sorter = array();
		$ret = array();
		reset($array);
		
		foreach ($array as $cc => $obj)
		{
			$sorter[$cc] = $obj->$key;
		}
		asort($sorter);
		foreach ($sorter as $cc => $va)
		{
			$ret[$cc] = $array[$cc];
		}
		
		$array = $ret;
	}
}

/* End of file geo.class.php */
/* Location: cronblocks/includes/lib/m/geo.class.php */