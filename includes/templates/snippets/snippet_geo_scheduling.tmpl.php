<?php wp_nonce_field('save_geosched_meta_nonce', 'save_geosched_meta_nonce'); ?>
<p class="loading"><?php _e('Loading...', $this->domain); ?></p>
<div class="inside cronblocks-snippet-meta hidden">
  <div class="main-controls">
    <div class="priority">
      <h4><?php _e('Priority', $this->domain); ?></h4>
      <input id="priority_field" type="text" data-min="1" data-max="10" data-step="1" name="usc_cb_weight" value="<?php echo $opts->weight; ?>"/>
    </div>
    <h4><?php _e('Control Type:', $this->domain); ?></h4>
    <ul class="type">
      <li>
        <input type="radio" id="usc_cb_ctl_type_1" name="usc_cb_ctl_type" data-rel-selector="#usc_cb_geolocation"
               value="use_geolocation" <?php checked($opts->ctl_type, 'use_geolocation'); ?>/> <label
          for="usc_cb_ctl_type_1"><?php _e('Geolocation', $this->domain); ?></label>
      </li>
      <li>
        <input type="radio" id="usc_cb_ctl_type_2" name="usc_cb_ctl_type" data-rel-selector="#usc_cb_schedule"
               value="use_scheduling" <?php checked($opts->ctl_type, 'use_scheduling'); ?> /> <label
          for="usc_cb_ctl_type_2"><?php _e('Scheduling', $this->domain); ?></label>
      </li>
    </ul>
  </div>

  <div id="usc_cb_geolocation" data-section-rel="usc_cb_ctl_type_1" 
  	   class="section <?php echo 'use_geolocation' ===  $opts->ctl_type ? '' : 'hidden'; ?>">
    <ul>
      <li><label for="usc_cb_country"><?php _e('Country:', $this->domain); ?>
          <select name="usc_cb_country" id="usc_cb_country">
            <option
              value="any" <?php selected($opts->geo->country, 'any', true); ?>><?php _e('Any', $this->domain); ?></option>
            <option value="" disabled="disabled">-------</option>
            <?php foreach ($countries as $country): ?>
              <option value="<?php echo esc_attr($country->country_code); ?>"
                      data-has-regions="<?php echo $country->has_regions; ?>"
                <?php selected($opts->geo->country, $country->country_code, true); ?>><?php echo esc_attr($country->country_name); ?></option>
            <?php endforeach; ?>
          </select></label></li>
    </ul>
  </div>

  <div id="usc_cb_schedule" class="section <?php echo 'use_scheduling' === $opts->ctl_type ? '' : 'hidden'; ?>">
    <div class="inside">
	    <table class="schedule">
	      <tr class="time-fields">
	        <td class="label">
	          <label for="time_from_hours"><?php echo _e('From Time:', $this->domain); ?></label>
	        </td>
	        <td class="input">
	          <input type="text" class="hours-input" name="start_hour" id="time_from_hours" value="<?php echo $opts->time->start_hour; ?>"/>
	          <input type="text" class="minutes-input" name="start_minute" id="time_from_minutes" value="<?php echo $opts->time->start_minute; ?>"/>
	        </td>
	      </tr>
	      <tr class="time-fields">
	        <td class="label">
	          <label for="time_to_hours"><?php echo _e('To Time', $this->domain); ?>:</label>
	        </td>
	        <td class="input">
	          <input type="text" class="hours-input" name="end_hour" id="time_to_hours" value="<?php echo $opts->time->end_hour; ?>"/>
	          <input type="text" class="minutes-input" name="end_minute" id="time_to_minutes" value="<?php echo $opts->time->end_minute; ?>"/>
	        </td>
	      </tr>
	
		<tr><td colspan="2">&nbsp;</td></tr>
	
		  <tr>
	     	<td class="label"><?php _e('Schedule Type:', $this->domain); ?></td> 
	      	<td><input type="radio" name="usc_cb_sched_type" id="usc_cb_weekly" data-rel-selector=".week_fields"
	            	value="weekly" <?php checked($opts->sched_type, 'weekly', true); ?> />
			       <label for="usc_cb_weekly"><?php _e('Weekly', $this->domain); ?></label>
			       <input type="radio" name="usc_cb_sched_type" id="usc_cb_monthly" data-rel-selector=".month_fields"
			            value="monthly" <?php checked($opts->sched_type, 'monthly', true); ?> />
			       <label for="usc_cb_monthly"><?php _e('Monthly', $this->domain); ?></label>
			</td>
		 </tr>
	
	<tr><td colspan="2">&nbsp;</td></tr>
	
	      <tr class="month_fields<?php echo 'monthly' === $opts->sched_type ? '' : ' hidden'; ?>">
	        <td class="label">
	          <label for="month_days"><?php echo _e('Days', $this->domain); ?>:</label>
	        </td>
	        <td class="inputs">
	          <input type="button" class="button select-all button-secondary"
	                 value="<?php echo _e('Select All', $this->domain) ?>"/>
	          <select multiple id="month_days" name="usc_cb_days[]" placeholder="<?php _e('Select Days', $this->domain); ?>">
	            <?php for ($i = 1; $i <= 31; $i++): ?>
	              <option <?php selected($opts->days->{'day_' . $i}); ?>
	                value="<?php echo 'day_' . $i; ?>"><?php echo $i; ?></option>
	            <?php endfor; ?>
	          </select>
	        </td>
	      </tr>
	
	      <tr class="month_fields<?php echo 'monthly' === $opts->sched_type ? '' : ' hidden'; ?>">
	        <td class="label">
	          <label for="months_selection"><?php echo _e('Months', $this->domain); ?>:</label>
	        </td>
	        <td class="inputs months">
	          <input type="button" class="button select-all button-secondary"
	                 value="<?php echo _e('Select All', $this->domain) ?>"/>
	          <select multiple name="usc_cb_months[]" id="months_selection" placeholder="<?php _e('Select Months', $this->domain); ?>">
	            <option value="jan" <?php selected($opts->months->jan); ?>><?php echo _e('January');
	              ?></option>
	            <option value="feb" <?php selected($opts->months->feb); ?>><?php echo _e('February');
	              ?></option>
	            <option value="mar" <?php selected($opts->months->mar); ?>><?php echo _e('March');
	              ?></option>
	            <option value="apr" <?php selected($opts->months->apr); ?>><?php echo _e('April');
	              ?></option>
	            <option value="may" <?php selected($opts->months->may); ?>><?php echo _e('May');
	              ?></option>
	            <option value="jun" <?php selected($opts->months->jun); ?>><?php echo _e('June');
	              ?></option>
	            <option value="jul" <?php selected($opts->months->jul); ?>><?php echo _e('July');
	              ?></option>
	            <option value="aug" <?php selected($opts->months->aug); ?>><?php echo _e('August');
	              ?></option>
	            <option value="sep" <?php selected($opts->months->sep); ?>><?php echo _e('September');
	              ?></option>
	            <option value="oct" <?php selected($opts->months->oct); ?>><?php echo _e('October');
	              ?></option>
	            <option value="nov" <?php selected($opts->months->nov); ?>><?php echo _e('November');
	              ?></option>
	            <option value="dec" <?php selected($opts->months->dec); ?>><?php echo _e('December');
	              ?></option>
	          </select>
	        </td>
	      </tr>
	      <tr class="week_fields<?php echo 'weekly' === $opts->sched_type ? '' : ' hidden'; ?>">
	        <td class="label">
	          <label for="weekdays_selection"><?php echo _e('Weekdays:', $this->domain); ?></label>
	        </td>
	        <td class="inputs weekdays">
	          <input type="button" class="button select-all button-secondary"
	                 value="<?php echo _e('Select All', $this->domain) ?>"/>
	          <select id="weekdays_selection" multiple name="usc_cb_weekdays[]" placeholder="<?php _e('Select Weekdays', $this->domain); ?>">
	            <option <?php selected($opts->weekdays->mon); ?>
	              value="mon"><?php echo _e('Monday'); ?></option>
	            <option <?php selected($opts->weekdays->tue); ?>
	              value="tue"><?php echo _e('Tuesday'); ?></option>
	            <option <?php selected($opts->weekdays->wed); ?>
	              value="wed"><?php echo _e('Wednesday'); ?></option>
	            <option <?php selected($opts->weekdays->thu); ?>
	              value="thu"><?php echo _e('Thursday'); ?></option>
	            <option <?php selected($opts->weekdays->fri); ?>
	              value="fri"><?php echo _e('Friday'); ?></option>
	            <option <?php selected($opts->weekdays->sat); ?>
	              value="sat"><?php echo _e('Saturday'); ?></option>
	            <option <?php selected($opts->weekdays->sun); ?>
	              value="sun"><?php echo _e('Sunday'); ?></option>
	          </select>
	        </td>
	      </tr>
	    </table>
    </div>
  </div>
</div>