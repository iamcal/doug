<?
	#
	# $Id$
	#

	db_connect();

	#################################################################

	function db_fatal(){

		echo "<h1>Arrrgh!</h1>\n";
		echo "<p>We were unable to connect to the database, so we're just giving up. Sorry.</p>";
		echo "<p><code>".HtmlSpecialChars(mysql_error())."</code></p>";
		exit;
	}

	#################################################################

	function db_connect() {
		$GLOBALS['cfg']['db_conn'] = @mysql_connect($GLOBALS['cfg']['db_host'], $GLOBALS['cfg']['db_user'], $GLOBALS['cfg']['db_pass']);

		if (!$GLOBALS['cfg']['db_conn']) db_fatal();

		$ret = mysql_select_db($GLOBALS['cfg']['db_name'], $GLOBALS['cfg']['db_conn']);	
		if (!$ret) db_fatal();
	}

	#################################################################

	function db_query($qstring) {

		if (array_key_exists('debugsql',$_GET)){
			echo "QUERY: ".HtmlSpecialChars($qstring)."<br />\n";
		}

		$result = mysql_query($qstring, $GLOBALS['cfg']['db_conn']);

		if (!$result) {
			echo "<p>".db_errorno().' : '.db_error()."</p>";
		} else {
			return $result;
		}
	}

	#################################################################

	function db_insertid() {
		return mysql_insert_id($GLOBALS['cfg']['db_conn']);
	}

	#################################################################

	function db_error() {
		return mysql_error($GLOBALS['cfg']['db_conn']);
	}

	function db_errorno() {
		return mysql_errno($GLOBALS['cfg']['db_conn']);
	}

	#################################################################

	function db_insert($tbl, $hash){
		$fields = array_keys($hash);
		db_query("INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."')");
		return db_insertid();
	}

	#################################################################

	function db_insert_dupe($tbl, $hash, $hash2){
		$fields = array_keys($hash);
		$sql = "INSERT INTO $tbl (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$hash)."')";

		$bits = array();
		foreach(array_keys($hash) as $k){
			$bits[] = "`$k`='$hash[$k]'";
		}
		$sql .= " ON DUPLICATE KEY UPDATE ".implode(', ',$bits);

		db_query($sql);
		return db_insertid();
	}

	#################################################################

	function db_update($tbl, $hash, $where){
		$bits = array();
		foreach(array_keys($hash) as $k){
			$bits[] = "`$k`='$hash[$k]'";
		}
		db_query("UPDATE $tbl SET ".implode(', ',$bits)." WHERE $where");
	}

	#################################################################

	function db_num_rows($qhandle) {
		if ($qhandle) {
			return mysql_numrows($qhandle);
		} else {
			echo "no result set found";
			return 0;
		}
	}

	#################################################################

	function db_fetch_list($qhandle) {
		return mysql_fetch_array($qhandle, MYSQL_NUM);
	}

	#################################################################

	function db_fetch_hash($qhandle) {
		return mysql_fetch_array($qhandle, MYSQL_ASSOC);
	}

	#################################################################

	function db_escape_like($string){
		return str_replace(array('%','_'), array('\\%','\\_'), $string);
	}

	#################################################################

	function db_escape_rlike($string){
		return preg_replace("/([.\[\]*^\$()])/", '\\\$1', $string);
	}

	#################################################################

	function db_fetch_one($sql) {
		return db_fetch_hash(db_query($sql));
	}

	#################################################################

	function db_fetch_single($sql) {
		return array_shift(db_fetch_list(db_query($sql)));
	}

	#################################################################

	function db_fetch_all($sql) {
		$out = array();
		$result = db_query($sql);
		while($row = db_fetch_hash($result)){
			$out[] = $row;
		}
		return $out;
	}

	#################################################################
?>
