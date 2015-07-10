<?php

/**
 * Plugin Name: Prayer Times
 * Plugin URI: http://www.londonprayertimes.com/wp-plugin/
 * Description: Prayer Times widget for WordPress
 * Version: 1.0
 * Author: Dean McDonagh
 * Author URI: http://www.londonprayertimes.com/
 */

class PrayerTimesWidget extends WP_Widget
{

	private $api = null;
	private $cities = array();

	public function __construct()
	{
		$this->api = new PrayerTimes;
		parent::WP_Widget(false, $name = __('Prayer Times', 'PrayerTimes'));
	}

	public function form($instance)
	{
		if (!$instance) {
			$instance = array();
			$instance['title'] = 'Prayer Times';
			$instance['city'] = PrayerTimes::$defaultCity;
			$instance['date_format'] = 'D jS M Y';
			$instance['fajr_jamat'] = '';
			$instance['dhuhr_jamat'] = '';
			$instance['asr_jamat'] = '';
			$instance['magrib_jamat'] = '';
			$instance['isha_jamat'] = '';
			$instance['jummah'] = '';
		}
		extract($instance);
		echo '<p>';
		echo '<label for="' . $this->get_field_id('title') . '">' . __('Title', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" />';
		echo '</p>';
		$citites = $this->api->getCities();
		echo '<p>';
		echo '<label for="' . $this->get_field_id('city') . '">' . __('City', 'PrayerTimes') . '</label>';
		echo '<select class="widefat" id="' . $this->get_field_id('city') . '" name="' . $this->get_field_name('city') . '">';
		foreach ($citites as $cityValue) {
			echo '<option value="' . $cityValue . '"' . ($cityValue === $city ? ' selected="selected"' : '') . '>' . ucwords($cityValue) . '</option>';
		}
		echo '</select>';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('date_format') . '">' . __('Date Format', 'PrayerTimes') . ' <a href="http://php.net/manual/en/function.date.php" target="_new">(documentation)</a></label>';
		echo '<input class="widefat" id="' . $this->get_field_id('date_format') . '" name="' . $this->get_field_name('date_format') . '" type="text" value="' . $date_format . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('fajr_jamat') . '">' . __('Fajr Jamat', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('fajr_jamat') . '" name="' . $this->get_field_name('fajr_jamat') . '" type="text" value="' . $fajr_jamat . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('dhuhr_jamat') . '">' . __('Dhuhr Jamat', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('dhuhr_jamat') . '" name="' . $this->get_field_name('dhuhr_jamat') . '" type="text" value="' . $dhuhr_jamat . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('asr_jamat') . '">' . __('Asr Jamat', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('asr_jamat') . '" name="' . $this->get_field_name('asr_jamat') . '" type="text" value="' . $asr_jamat . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('magrib_jamat') . '">' . __('Magrib Jamat', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('magrib_jamat') . '" name="' . $this->get_field_name('magrib_jamat') . '" type="text" value="' . $magrib_jamat . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('isha_jamat') . '">' . __('Isha Jamat', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('isha_jamat') . '" name="' . $this->get_field_name('isha_jamat') . '" type="text" value="' . $isha_jamat . '" />';
		echo '</p>';
		echo '<p>';
		echo '<label for="' . $this->get_field_id('jummah') . '">' . __('Jummah', 'PrayerTimes') . '</label>';
		echo '<input class="widefat" id="' . $this->get_field_id('jummah') . '" name="' . $this->get_field_name('jummah') . '" type="text" value="' . $jummah . '" />';
		echo '</p>';
	}

	public function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['city'] = strip_tags($new_instance['city']);
		$instance['date_format'] = strip_tags($new_instance['date_format']);
		$instance['fajr_jamat'] = strip_tags($new_instance['fajr_jamat']);
		$instance['dhuhr_jamat'] = strip_tags($new_instance['dhuhr_jamat']);
		$instance['asr_jamat'] = strip_tags($new_instance['asr_jamat']);
		$instance['magrib_jamat'] = strip_tags($new_instance['magrib_jamat']);
		$instance['isha_jamat'] = strip_tags($new_instance['isha_jamat']);
		$instance['jummah'] = strip_tags($new_instance['jummah']);
		return $instance;
	}

	public function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		$city = empty($instance['city']) ? PrayerTimes::$defaultCity : $instance['city'];
		echo $before_widget;
		echo '<div class="widget-text">';
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		$this->print_times($this->api->getTimes($city), $instance);
		echo '</div>';
		echo $after_widget;
	}

	protected function print_times(array $times, array $defaults)
	{
		if (isset($times['error'])) {
			echo $times['error'];
		} else {
			if ($defaults['date_format']) {
				echo '<p class="prayertimes-date">Today: <em>' . date($defaults['date_format'], strtotime($times['date'])) . '</em></p>';
			}
			echo '<table class="prayertimes-table">';
			echo '<thead>';
			echo '<tr>';
			echo '<th></th>';
			echo '<th>Begin</th>';
			echo '<th>Jama\'ah</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Fajr</td>';
			echo '<td>' . $times['fajr'] . '</td>';
			echo '<td>' . ($defaults['fajr_jamat'] ? $defaults['fajr_jamat'] : $times['fajr_jamat']) . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Sunrise</td>';
			echo '<td>' . $times['sunrise'] . '</td>';
			echo '<td></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Dhuhr</td>';
			echo '<td>' . $times['dhuhr'] . '</td>';
			echo '<td>' . ($defaults['dhuhr_jamat'] ? $defaults['dhuhr_jamat'] : $times['dhuhr_jamat']) . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Asr</td>';
			echo '<td>' . $times['asr'] . '</td>';
			echo '<td>' . ($defaults['asr_jamat'] ? $defaults['asr_jamat'] : $times['asr_jamat']) . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Magrib</td>';
			echo '<td>' . $times['magrib'] . '</td>';
			echo '<td>' . ($defaults['magrib_jamat'] ? $defaults['magrib_jamat'] : $times['magrib_jamat']) . '</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td class="prayertimes-name">Isha</td>';
			echo '<td>' . $times['isha'] . '</td>';
			echo '<td>' . ($defaults['isha_jamat'] ? $defaults['isha_jamat'] : $times['isha_jamat']) . '</td>';
			echo '</tr>';
			if ($defaults['jummah']) {
				echo '<tr>';
				echo '<td class="prayertimes-name">Jummah</td>';
				echo '<td>' . $defaults['jummah'] . '</td>';
				echo '<td></td>';
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
		}
	}
}

function PrayerTimes_register_widget() {
	register_widget("PrayerTimesWidget");
}

add_action('widgets_init', 'PrayerTimes_register_widget');

class PrayerTimesSettings
{

	private $options;

	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_init', array($this, 'admin_init'));
	}

	public function admin_menu()
	{
		add_options_page(
			'Prayer Times Settings',
			'Prayer Times',
			'manage_options',
			'prayer-times',
			array($this, 'admin_page')
		);
	}

	public function admin_page()
	{
		$this->options = get_option('prayertimes_options');
		echo '<div class="wrap">';
		echo '<h2>Prayer Times</h2>';
		echo '<form method="post" action="options.php">';
		settings_fields('prayertimes_option_group');
		do_settings_sections('prayer-times');
		submit_button();
		echo '</form>';
		echo '</div>';
	}

    public function admin_init()
    {        
		register_setting(
			'prayertimes_option_group',
			'prayertimes_options',
			array($this, 'sanitize')
		);

		add_settings_section(
			'prayertimes_section_api',
			'Prayer Times API Settings',
			array( $this, 'print_section_info' ),
			'prayer-times'
		);  

		add_settings_field(
			'api_key',
			'API Key',
			array($this, 'api_key_callback'),
			'prayer-times',
			'prayertimes_section_api'
		);        
    }

    public function sanitize($input)
    {
		$new_input = array();
		if (isset($input['api_key'])) {
			$new_input['api_key'] = sanitize_text_field($input['api_key']);
			if (empty($new_input['api_key']) || !PrayerTimes::checkKey($new_input['api_key'])) {
				add_settings_error('api_key', 'prayertimes_section_api', 'Invalid API Key');
			}
		}
		return $new_input;
    }

	public function print_section_info()
	{
		echo 'Request yours at: <a href="mailto:admin@londonprayertimes.com">admin@londonprayertimes.com</a>';
	}

    public function api_key_callback()
    {
        printf(
            '<input type="text" class="regular-text" id="api_key" name="prayertimes_options[api_key]" value="%s" />',
            isset($this->options['api_key']) ? esc_attr($this->options['api_key']) : ''
        );
    }
}

if (is_admin()) {
	$prayerTimesSettings = new PrayerTimesSettings();
	
	add_filter('plugin_action_links', 'add_action_links');
	function add_action_links($links) {
		$mylinks = array(
			'<a href="' . admin_url('options-general.php?page=prayer-times') . '">Settings</a>',
		);
		return array_merge( $links, $mylinks );
	}
}

class PrayerTimes
{

	private $key = null;
	private $endpoints = array(
		'times' => 'http://www.londonprayertimes.com/api/times/?format=json&key=%s',
		'cities' => 'http://www.londonprayertimes.com/api/cities/?format=json&key=%s'
	);
	public static $defaultCity = 'london';

	public function __construct($key = null)
	{
		if (is_null($key)) {
			$option = get_option('prayertimes_options');
			if ($option && isset($option['api_key'])) {
				$key = $option['api_key'];
			}
		}
		$this->key = $key;
	}

	public function getTimes($city = null, $date = null)
	{
		if (is_null($city)) {
			$city = self::$defaultCity;
		}
		if (is_null($date)) {
			$date = date('Y-m-d');
		}
		$cacheKey = 'pt_' . $city . '_' . str_replace('-', '', $date);
		if (($times = get_transient($cacheKey)) === false) {
			$response = wp_remote_get(sprintf($this->endpoints['times'], $this->key, $city, $date));
			$times = json_decode($response['body'], true);
			if ($response['response']['code'] === 200) {
				set_transient($cacheKey, $times, DAY_IN_SECONDS);
			}
		}
		return $times;
	}

	public function getCities()
	{
		$cacheKey = 'pt_cities';
		if (($cities = get_transient($cacheKey)) === false) {
			$response = wp_remote_get(sprintf($this->endpoints['cities'], $this->key));
			if ($response['response']['code'] === 200) {
				$cities = json_decode($response['body'], true);
				set_transient($cacheKey, $cities, DAY_IN_SECONDS);
			} else {
				$cities = array();
			}
		}
		return $cities;
	}

	public static function checkKey($key)
	{
		$response = wp_remote_get(sprintf('http://www.londonprayertimes.com/api/times/?format=json&key=%s', $key));
		return $response['response']['code'] === 200;
	}
}

function PrayerTimes_deactivation() {
	unregister_setting('prayertimes_options', 'prayertimes_options');
}

register_deactivation_hook(__FILE__, 'PrayerTimes_deactivation');