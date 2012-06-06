<?php
	#
	# $Id$
	#

	########################################################################################
	
	function users_fetch($uid){
		$uid_enc = AddSlashes($uid);
		return db_fetch_one("SELECT * FROM users WHERE uid='$uid_enc'");
	}
	
	function users_fetch_all(){
		return db_fetch_all("SELECT * FROM users ORDER BY name ASC");
	}

	function users_create($args){

		$hash = array(
			'uid'	=> AddSlashes($args['uid']),
			'name'	=> AddSlashes($args['name']),
		);
		$hash2 = $hash;
		unset($hash2['uid']);

		db_insert_dupe('users', $hash, $hash2);
	}

	########################################################################################

	function users_login($uid){

		$v = http_build_query(array(
			'uid'	=> $uid,
			'h'	=> users_login_hash($uid),
		));

		$expire = time() + (10 * 365 * 24 * 60 * 60);
		$path = $GLOBALS['cfg']['root_url'];
		$domain = $GLOBALS['cfg']['app_domain'];

		setcookie('u', $v, $expire, $path, $domain);
	}

	function users_logout(){

		$expire = time() - 3600;
		$path = $GLOBALS['cfg']['root_url'];
		$domain = $GLOBALS['cfg']['app_domain'];

		setcookie('u', '', $expire, $path, $domain);
	}

	function users_login_hash($uid){

		return md5($uid);
	}

	function users_check_login(){

		$GLOBALS['user'] = array();

		if ($_COOKIE['u']){
			$args = array();
			parse_str($_COOKIE['u'], $args);
			if ($args['uid'] && users_login_hash($args['uid']) == $args['h']){

				$GLOBALS['user'] = users_fetch($args['uid']);
			}
		}

		$GLOBALS['smarty']->assign_by_ref('user', $GLOBALS['user']);
	}

	########################################################################################

	function XX_users_update(&$user, $hash){
		if (!$user['name']){
			return 0;
		}
		
		$name_enc = addslashes($user['name']);
		db_update('users', $hash, "name='$name_enc'");
		
		return 1;
	}
	
	function XX_users_create($name){
		if (!$name){
			return 0;
		}
		
		$name_enc = addslashes($name);
		db_insert('users', array('name' => $name_enc));
		
		return array('name' => $name);
	}
?>

