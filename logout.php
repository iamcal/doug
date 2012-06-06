<?
	include('include/init.php');

	users_logout();

	header("location: {$cfg['root_url']}");
	exit;
