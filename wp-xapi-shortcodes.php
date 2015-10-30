<?php

require_once __DIR__."/xapi.php";

/*
Plugin Name: xAPI Shortcodes
Plugin URI: http://github.com/tunapanda/wp-xapi-shortcodes
Description: Shortcodes to show status icons for xAPI objectives.
Version: 0.0.1
*/

/**
 * Create the admin menu.
 */
function xapisc_admin_menu() {
	add_options_page(
		'xAPI Shortcodes',
		'xAPI Shortcodes',
		'manage_options',
		'xapisc_settings',
		'xapisc_create_settings_page'
	);
}

/**
 * Admin init.
 */
function xapisc_admin_init() {
	register_setting("xapisc","xapisc_endpoint_url");
	register_setting("xapisc","xapisc_username");
	register_setting("xapisc","xapisc_password");
}

/**
 * Create settings page.
 */
function xapisc_create_settings_page() {
	require __DIR__."/settings.php";
}

add_action('admin_menu','xapisc_admin_menu');
add_action('admin_init','xapisc_admin_init');

/**
 * Compare statements.
 */
function xapiscCompareStatements($a, $b) {
	$verbs=array(
		"http://adlnet.gov/expapi/verbs/attempted",
		"http://adlnet.gov/expapi/verbs/completed"
	);

	$aVal=array_search($a["verb"]["id"],$verbs);
	if ($aVal===FALSE)
		$aVal=-1;

	$bVal=array_search($b["verb"]["id"],$verbs);
	if ($bVal===FALSE)
		$bVal=-1;

	if ($aVal<$bVal)
		return -1;

	if ($aVal>$bVal)
		return 1;

	if ($a["result"]["score"]["raw"]<$b["result"]["score"]["raw"])
		return -1;

	if ($a["result"]["score"]["raw"]>$b["result"]["score"]["raw"])
		return 1;

	return 0;
}

/**
 * Render the icon.
 */
function xapiscIcon($p) {
	global $xapisc;

	if (!$xapisc)
		$xapisc=new Xapi(
			get_option("xapisc_endpoint_url"),
			get_option("xapisc_username"),
			get_option("xapisc_password")
		);

	$user=wp_get_current_user();

	$params=array();
	$params["agentEmail"]=$user->user_email;

	if (array_key_exists("activity",$p))
		$params["activity"]=$p["activity"];

	if (array_key_exists("h5p_id",$p)) {
		$activityUrl=get_site_url()."/wp-admin/admin-ajax.php?action=h5p_embed&id=".$p["h5p_id"];
		$params["activity"]=$activityUrl;
	}

	$statements=$xapisc->getStatements($params);

	usort($statements,"xapiscCompareStatements");
	$statements=array_reverse($statements);
	$statement=$statements[0];

	$icon="activity-gray.png";

	switch ($statement["verb"]["id"]) {
		case "http://adlnet.gov/expapi/verbs/completed":
			if ($p["minscore"] && 
					$statement["result"]["score"]["raw"]<$p["minscore"])
				$icon="activity-red.png";

			else {
				if (array_key_exists("starscores",$p)) {
					$starscores=explode(",", $p["starscores"]);
					$score=$statement["result"]["score"]["raw"];

					if (sizeof($starscores)>=3 && $score>=$starscores[2])
						$icon="activity-green-star-3.png";

					else if (sizeof($starscores)>=2 && $score>=$starscores[1])
						$icon="activity-green-star-2.png";

					else if (sizeof($starscores)>=1 && $score>=$starscores[0])
						$icon="activity-green-star-1.png";

					else
						$icon="activity-green.png";
				}

				else
					$icon="activity-green.png";
			}

			break;

		case "http://adlnet.gov/expapi/verbs/attempted":
			$icon="activity-yellow.png";
			break;
	}

	return '<img src="'.plugins_url().'/wp-xapi-shortcodes/img/'.$icon.'"/>';
}

add_shortcode("xapi-icon", "xapiscIcon");

