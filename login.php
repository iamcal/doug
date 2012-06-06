<?
	#
	# $Id$
	#

	include('include/init.php');


	#
	# if we're already logged in, do nothing
	#

	if ($user['id']){
		header("location: {$cfg['root_url']}");
		exit;
	}


	#
	# generate a state token and store it in a session cookie
	#

	$state_token = sha1(rand());
	setcookie('st', $state_token);


	#
	# redir to facebook
	#

	$args = array(
		'client_id'	=> $cfg['fb_app_id'],
		'redirect_uri'	=> $cfg['abs_root_url'].'auth',
		'state'		=> $state_token,
	);

	$url = 'https://www.facebook.com/dialog/oauth?'.http_build_query($args);

	header("location: $url");
	exit;
?>
