<?
	$this_dir = dirname(__FILE__);

	define('INCLUDE_DIR', $this_dir);
	define('APP_DIR', "$this_dir/..");


	#
	# start-up
	#

	include("$this_dir/config.php");

	putenv('TZ='.$cfg['tz_id']);
	$cfg['abs_root_url'] = $cfg['app_protocol'].'://'.$cfg['app_domain'].$cfg['root_url'];

	loadlib('db');
	loadlib('smarty');
	loadlib('users');
	loadlib('bugs');


	#
	# core functions
	#

	function loadlib($name){
		include_once(INCLUDE_DIR."/lib_$name.php");
	}

	function dumper($foo){
            echo "<pre style=\"text-align: left;\">";
            echo HtmlSpecialChars(var_export($foo, 1));
            echo "</pre>\n";
	}

	function microtime_ms(){
		list($usec, $sec) = explode(" ", microtime());
		return intval(1000 * ((float)$usec + (float)$sec));
	}


	#
	# log em in?
	#

	users_check_login();

	if ($user){
		$smarty->assign('needs_closing',db_fetch_single("SELECT count(*) AS count FROM bugs WHERE opened_user='$user[name]' AND status='resolved'"));
	}


	#
	# constants
	#

	$cfg[resolutions] = array(
		'fixed'		=> 'Fixed',
		'cant_dupe'	=> 'Can\'t duplicate',
		'cant_fix'	=> 'Can\'t fix',
		'wont_fix'	=> 'Won\'t fix',
		'not_issue'	=> 'Not an issue',
		'dupe'		=> 'Duplicate',
	);


	#
	# other stuff
	#

	$smarty->assign('max_attach_bytes', $cfg[max_attach_mb] * 1024 * 1024);
	$smarty->assign('max_attach_label', "$cfg[max_attach_mb] MB");

	function get_attachement(){

		if (!strlen($_FILES[attach][tmp_name])) return "";

		$target = dirname(__FILE__).'/../attachments';
		$base_name = preg_replace('![^a-z0-9.]!', '_', StrToLower($_FILES[attach][name]));
		$use_name = $base_name;
		$i = 1;

		while (file_exists("$target/$use_name")){

			$use_name = "{$i}_$base_name";
			$i++;
		}

		if (move_uploaded_file($_FILES[attach][tmp_name], "$target/$use_name")){

			return $use_name;
		}

		return "";
	}
?>
