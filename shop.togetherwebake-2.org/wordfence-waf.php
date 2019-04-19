<?php
// Before removing this file, please verify the PHP ini setting `auto_prepend_file` does not point to this.

if (file_exists('/home/techvirginia/shop.togetherwebake.org/wp-content/plugins/wordfence/waf/bootstrap.php')) {
	define("WFWAF_LOG_PATH", '/home/techvirginia/shop.togetherwebake.org/wp-content/wflogs/');
	include_once '/home/techvirginia/shop.togetherwebake.org/wp-content/plugins/wordfence/waf/bootstrap.php';
}
?>