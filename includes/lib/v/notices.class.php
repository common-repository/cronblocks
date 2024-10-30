<?php defined('ABSPATH') or die("No direct access allowed");

/* Notices class */

class UscCronblocks_notices extends UscCronblocks
{
	private $notice_name = '_usc_cb_notices_';
	public static $instance;
	
	public function __construct()
	{
		global $blog_id;
		
		$this->notice_name .= $blog_id;
	}
	
	public static function bootstrap()
	{
		if (! isset(self::$instance))
			self::$instance = new self();
		
		return self::$instance;
	}
	
	/**
	 * @method set_error
	 * @desc Wrapper for set_notices($foo,true);
	 * @param string $msg
	 */
	public function set_error($msg)
	{
		$this->set_notice($msg,true);
	}
	
	/**
	 * @method set_warning
	 * @desc Wrapper for set_notices($foo,false)
	 * @param string $msg
	 */
	public function set_warning($msg)
	{
		$this->set_notice($msg,false);
	}
	
	/**
	 * @method set_notice
	 * @desc Populate the $msgs array with errors and warnings
	 * @param string $msg
	 * @param bool $is_error
	 */
	protected function set_notice($msg,$is_error)
	{
		global $current_user;
		
		$msgs = get_user_meta($current_user->ID, $this->notice_name, true); 

		$msgs[] = array('msg' => $msg,'is_error' => $is_error);
		
		update_user_meta($current_user->ID, $this->notice_name, $msgs);
	}
	
	/**
	 * @method show_notices
	 * @desc Displays the notices inside $msgs
	 */
	public function show_notices()
	{
		global $current_user;
		
		$msgs = get_user_meta($current_user->ID, $this->notice_name, true);
		
		if ($msgs)
		{
			foreach ($msgs as $msg) : $class = $msg['is_error'] ? 'error' : 'updated'; ?>
			    <div class="<?php echo $class; ?>">
			        <p><strong><?php echo $msg['msg']; ?></strong></p>
			    </div>
			<?php endforeach;
			
			delete_user_meta($current_user->ID, $this->notice_name);
		}
	}
}