<?php
	include('include/init.php');

	loadlib('http');


	#
	# clear the state cookie now - it's already in $_COOKIE
	#

	setcookie('st', '');


	#
	# if we're already logged in, do nothing
	#

	if ($user['id']){
		header("location: {$cfg['root_url']}");
		exit;
	}


	#
	# is this even oauth?
	#

	if (!$_GET['code'] && !$_GET['error']){
		header("location: {$cfg['root_url']}");
		exit;
	}


	#
	# did the user deny us?
	#

	if ($_GET['error_reason'] == 'user_denied'){

		header("location: {$cfg['root_url']}");
		exit;
	}


	#
	# some other error?
	#

	if ($_GET['error']){

		$smarty->display('page_error_oauth.txt');
		exit;
	}


	#
	# check state token
	#

	if (!$_GET['state'] || $_COOKIE['st'] != $_GET['state']){

		$smarty->display('page_error_oauth_csrf.txt');
		exit;
	}


	#
	# do the post-back
	#

	$args = array(
		'client_id'	=> $cfg['fb_app_id'],
		'client_secret'	=> $cfg['fb_app_secret'],
		'redirect_uri'	=> $cfg['abs_root_url'].'auth',
		'code'		=> $_GET['code'],
	);
	$url = 'https://graph.facebook.com/oauth/access_token?'.http_build_query($args);

	$ret = http_get($url);

	if (!$ret['ok']){
		$smarty->assign('error', $ret);
		$smarty->display('page_error_oauth_misc.txt');
		exit;
	}

	$params = array();
	parse_str($ret['body'], $params);
	if (!$params['access_token']){

		$smarty->assign('error', $ret);
		$smarty->display('page_error_oauth_misc.txt');
		exit;
	}


	#
	# fetch some info from the graph API
	#

	$url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
	$ret = http_get($url);

	if (!$ret['ok']){
		$smarty->assign('error', $ret);
		$smarty->display('page_error_oauth_misc.txt');
		exit;
	}

	$obj = JSON_decode($ret['body'], true);


	#
	# create user row in DB (if we don't already have it)
	#

	$uid = 'fb_'.$obj['id'];

	users_create(array(
		'uid'	=> $uid,
		'name'	=> $obj['name'],
	));

	users_login($uid);


	#
	# if this is first login, take them to their profile
	#

	$user = users_fetch($uid);

	if ($user['email']){

		header("location: {$cfg['root_url']}");
		exit;
	}

	header("location: {$cfg['root_url']}settings?new=1");
	exit;
