<?php defined('ABSPATH') or die("No direct access allowed");

/* Cronblocks Ajax handler class */
class UscCronblocks_ajax extends UscCronblocks
{
	public function __construct($env=null)
	{
		if (null !== $env)
			$this->env = $env;
		else
			$this->set_env();
	}
	
	public function get_regions()
	{
		$out = array('success' => false);
		
		if (empty($_POST['usc_cb_country']))
		{
			$out['msg'] = __('Please select a country!',$this->domain);
			
			echo json_encode($out);
			die();
		}
		
		if (2 !== strlen($_POST['usc_cb_country']))
		{
			$out['msg'] = __('Invalid Country Code!', $this->domain);
			
			echo json_encode($out);
			die();
		}
		
		$this->m = $this->load_lib('m/geo');
		$regions = $this->m->get_regions($_POST['usc_cb_country']);
		
		$out['success'] = true;
		$out['data'] = $regions;

		echo json_encode($out);
		die();
	}
	
}

/* End of file ajax.class.php */
/* Location: cronblocks/includes/lib/c/ajax.class.php */