<?php
	/**
	 * Plugin Name: Tamkeen TMS Integration
	 * Plugin URI: http://www.tamkeentms.com/wordpress-plugin
	 * Description: WordPress integration plugin for Tamkeen
	 * Version: 1.4
	 * Author: Tamkeen Team
	 * Author URI: http://www.tamkeentms.com
	 */

	const TAMKEEN_DEV_MODE = WP_DEBUG;
	const TAMKEEN_BASE_URL = 'https://tamkeenapp.com';

	/**
	 * @return bool
	 */
	function tamkeen_is_rest(){
		return strpos($_SERVER['REQUEST_URI'], trailingslashit(rest_get_url_prefix())) !== false;
	}

	/**
	 * Initiation
	 */
	function tamkeen_init(){
		// Starting the session
		if(session_status() === PHP_SESSION_NONE && ! tamkeen_is_rest()){
			session_start();
		}
	}

	function tamkeen_end_session(){
		if(session_status() == PHP_SESSION_ACTIVE){
			session_write_close();
		}
	}

	add_action('init', 'tamkeen_init', 1);
	add_action('wp_logout', 'tamkeen_end_session');
	add_action('wp_login', 'tamkeen_end_session');
	add_action('admin_init', 'tamkeen_admin_init');
	add_action('admin_menu', 'tamkeen_admin_menu');
	add_action('wp_enqueue_scripts', 'tamkeen_ui_assets');

	/**
	 * Register settings
	 */
	function tamkeen_admin_init(){
		add_settings_section('tamkeen_settings', null, null, 'tamkeen');

		// API Tenant
		add_settings_field('tamkeen_tenant_id', 'API tenant id', function(){
			print '<input name="tamkeen_tenant_id" value="' . get_option('tamkeen_tenant_id') . '" size="20" />';

		}, 'tamkeen', 'tamkeen_settings');

		// API secret
		add_settings_field('tamkeen_api_key', 'API secret key', function(){
			print '<input name="tamkeen_api_key" value="' . get_option('tamkeen_api_key') . '" size="40" />';

		}, 'tamkeen', 'tamkeen_settings');

		// Locale
		add_settings_field('tamkeen_locale', 'Display locale', function(){
			$locale = get_option('tamkeen_locale');

			print '<select name="tamkeen_locale">
				<option value="en" ' . ($locale == 'en' ?'selected' :'') . '>English</option>
				<option value="ar" ' . ($locale == 'ar' ?'selected' :'') . '>Arabic</option>
			</select>';

		}, 'tamkeen', 'tamkeen_settings');

		// Num categories per page
		add_settings_field('tamkeen_grid_items_per_row', 'Num. thumbnails per row', function(){
			print '<input name="tamkeen_grid_items_per_row" value="' .
					(get_option('tamkeen_grid_items_per_row') ?: 6) . '" size="10" />';

		}, 'tamkeen', 'tamkeen_settings');

		register_setting('tamkeen', 'tamkeen_tenant_id');
		register_setting('tamkeen', 'tamkeen_api_key');
		register_setting('tamkeen', 'tamkeen_locale');
		register_setting('tamkeen', 'tamkeen_grid_items_per_row');
	}

	/**
	 * Add to the admin main menu
	 */
	function tamkeen_admin_menu(){
		add_menu_page('tamkeen', 'Tamkeen', 'manage_options', 'tamkeen', 'tamkeen_options_page',
			plugin_dir_url(__FILE__) . '/src/icon.png');
	}

	/**
	 * The options page
	 */
	function tamkeen_options_page() {
		if(!current_user_can('manage_options')){
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}

		include 'src/admin.php';
	}

	/**
	 * Add UI assets to the queue
	 */
	function tamkeen_ui_assets(){
		$assetsUrl = plugin_dir_url(__FILE__) . '/src/assets/';

		// Js
		wp_register_script('bootstrap', $assetsUrl . 'js/bootstrap.bundle.min.js');

		// CSS
		wp_register_style('bootstrap', $assetsUrl . 'css/bootstrap.min.css');
		wp_register_style('bootstrap-icons', $assetsUrl . 'css/bootstrap-icons/bootstrap-icons.css');

		wp_enqueue_script('jquery');
		wp_enqueue_script('bootstrap');

		wp_enqueue_style('bootstrap');
		wp_enqueue_style('bootstrap-icons');
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Utils
	//
	//////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * @param string $path
	 * @return string
	 */
	function tamkeen_get_path($path = ''){
		return plugin_dir_path(__FILE__) . 'src/' . $path;
	}

	/**
	 * @param $view
	 * @param array $data
	 * @return mixed
	 */
	function tamkeen_render_view($view, array $data = []){
		ob_start();

		extract($data, EXTR_OVERWRITE);
		include tamkeen_get_path("views/layout/before.php");
		include tamkeen_get_path("views/{$view}.php");
		include tamkeen_get_path("views/layout/after.php");

		return ob_get_clean();
	}

	/**
	 * Display errors
	 * @param Exception $e
	 * @return string
	 */
	function tamkeen_display_error($e){
		$message = ($e instanceof Exception) ? $e->getMessage() :$e;

		return '<h4>Sorry, an error has happened.</h4>' .
			'<div>' . $message . '</div>';
	}

	/**
	 * @param       $method
	 * @param       $path
	 * @param array $params
	 * @param array $data
	 *
	 * @return mixed
	 */
	function tamkeen_api_request($method, $path, array $params = [], array $data = []){
		// API access
		$tenantId = get_option('tamkeen_tenant_id');
		$apiKey = get_option('tamkeen_api_key');
		$locale = get_option('tamkeen_locale');

		// Url and params
		$params['locale'] = $locale;

		// Url
		$url = TAMKEEN_BASE_URL . '/api/v1/' . $path . '?' . http_build_query($params);

		// The method
		$method = strtoupper($method);

		// Arguments
		$args = [
			'method' => $method,
			'timeout' => 30,
			'sslverify' => !TAMKEEN_DEV_MODE,
			'headers' => [
				"Authorization" => "Bearer " . $apiKey,
				"X-Tenant" => $tenantId,
				"Content-Type" => "application/json"
			]
		];

		if($method === 'POST'){
			$args['body'] = $data;
		}

		// Execute the request
		$response = wp_remote_request($url, $args);

		// Failed?
		if(is_wp_error($response)){
			throw new Exception('Request failed: ' . $response->get_error_message());
		}

		// Decode and return the body
		$response = json_decode(wp_remote_retrieve_body($response));

		if(isset($response->error)){
			$error = 'Request failed: ' . $response->error;

			if(isset($response->message) && TAMKEEN_DEV_MODE === true){
				$error .= ' (' . $response->message . ') ';
			}

			throw new Exception($error);
		}

		return $response;
	}

	/**
	 * @param $key
	 * @param $ar
	 * @return mixed
	 */
	function tamkeen_trans($key, $default = null){
		static $locale, $keys;

		if(!$locale){
			$locale = get_option('tamkeen_locale');
		}

		if(!$keys){
			$keys = include_once tamkeen_get_path("translation/{$locale}.php");
		}

		if(strpos($key, '.') === false){
			return $keys[$key] ?? value($default);
		}

		$translation = $keys;
		foreach(explode('.', $key) as $segment){
			if(array_key_exists($segment, $translation)){
				$translation = $translation[$segment];

			}else{
				return $default;
			}
		}

		return $translation;
	}

	/**
	 * Dump
	 */
	function tamkeen_dd(){
		var_dump(func_get_args());
		exit;
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	function tamkeen_url($path = ''){
		return get_page_link() . $path;
	}

	/**
	 * @param $type
	 * @param $text
	 *
	 * @return string
	 */
	function tamkeen_alert($type, $text){
		return '<div class="alert alert-' . $type  . '">
			<i class="bi bi-info-circle-fill"></i> ' . $text . '</div>';
	}

	/**
	 * @param $url
	 */
	function tamkeen_redirect($url){
		if($url === 'back'){
			$url = $_SERVER['HTTP_REFERER'];
		}

		if(!empty($url)){
			print '<script>location.href = "' . $url . '";</script>';
			exit;
		}
	}

	function tamkeen_str_limit($string,  $limit){
		if(extension_loaded('mbstring')){
			if(mb_strlen($string) > $limit){
				return mb_substr($string, 0, $limit) . '...';
			}

		}else{
			if(strlen($string) > $limit){
				return substr($string, 0, $limit) . '...';
			}
		}

		return $string;
	}

	//////////////////////////////////////////////////////////////////////////////////////////
	//
	//  Short codes
	//
	//////////////////////////////////////////////////////////////////////////////////////////
	add_shortcode('tamkeen_courses_catalog', function($attrs, $content, $tag){
		return include tamkeen_get_path('courses.php');
	});
