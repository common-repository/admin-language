<?php

/*
Plugin Name: Admin Language
Plugin URI: http://kashiv.com/projects/admin-language-plugin/
Description: This plugin allows to have admin panel in different language (for example, admin's native language) from blog's home language.
Author: Adrian Kashivskyy
Version: 1.0
Author URI: http://kashiv.com/
*/

define('ADMIN_LANG_OPTION_NAME', 'lang');

require('lang-codes.php');

function get_installed_languages() {
	if(is_dir(ABSPATH.LANGDIR) && $dh = opendir(ABSPATH.LANGDIR)) {
		while(($lang_file = readdir($dh)) !== false) {
			if(substr($lang_file, -3) == '.mo' && strlen($lang_file) == 8) {
				$lang_files[] = $lang_file;
			}
		}
		return $lang_files;
	}
}

function dropdown_langs($lang_files = array(), $current = '') {
	$flag = false;
	$output = array();
	foreach ((array)$lang_files as $val) {
		$code_lang = basename($val, '.mo');
		if ($code_lang == 'en_US') {
			$flag = true;
			$ae = __('American English');
			$output[$ae] = '<option value="'.esc_attr($code_lang).'"'.selected($current, $code_lang).'> '.$ae.'</option>';
		} elseif ( $code_lang == 'en_GB' ) {
			$flag = true;
			$be = __('British English');
			$output[$be] = '<option value="'.esc_attr($code_lang).'"'.selected($current, $code_lang).'> '.$be.'</option>';
		} else {
			$translated = lang_from_code(substr($code_lang, 0, -3));
			$output[$translated] = '<option value="'.esc_attr($code_lang).'"'.selected($current, $code_lang).'> '.esc_html($translated).'</option>';
		}
	}
	if ($flag === false) $output[] = '<option value=""'.selected($current, '').'>'.__('English')."</option>";
	uksort($output, 'strnatcasecmp');
	echo implode("\n\t", $output);
}

function language_form() {
	?>
	
	<tr> 
		<th><?php _e('Language') ?></th> 
		<td><select id="<?php echo ADMIN_LANG_OPTION_NAME; ?>" name="<?php echo ADMIN_LANG_OPTION_NAME; ?>"><?php dropdown_langs(get_installed_languages(), get_user_language()); ?></select></td>
	</tr>
	
	<?php
	
}

function update_language($user_ID) {
	if(isset($_POST[ADMIN_LANG_OPTION_NAME])) {
		$admin_lang = $_POST[ADMIN_LANG_OPTION_NAME];
		update_user_option($user_ID, ADMIN_LANG_OPTION_NAME, $admin_lang);
	}
}


function get_user_language() {
	global $user_ID;
	return get_user_option(ADMIN_LANG_OPTION_NAME, $user_ID);
}

function admin_language_hook($locale) {
	global $user_ID;
	$lang = get_user_language();
	if (!$lang) {
		return false;
	}
	if(is_user_logged_in()) {
		if ($lang) {
			$locale = $lang;
		} else {
			$locale = $locale;
		}
	}
	return $locale;
}
	

add_action('personal_options', 'language_form');
add_action('profile_update', 'update_language');
add_filter('locale', 'admin_language_hook');

?>