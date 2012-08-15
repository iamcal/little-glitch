<?
	include('init.php');

	$player_tsid = null;

	if ($_GET['access_token']){

		$ret = api_call("players.info", array(
			'oauth_token'	=> $_GET['access_token'],
		));

		if (!$ret['ok']){
			fail_400("Unable to fetch player");
		}

		$player_tsid = $ret['player_tsid'];
	}

	if ($_GET['sample'] && !$player_tsid){

		$player_tsid = 'PLI16FSFK2I91';
	}

	if (!$player_tsid){
		fail_400("No access token");
	}

	$ret = api_call("players.fullInfo", array(
		'player_tsid'	=> $player_tsid,
		'oauth_token'	=> $_GET['access_token'],
	));

	if (!$ret['ok']){
		fail_400("API failure");
	}

	header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html>
<head>
<title>Glitch</title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<style type="text/css">

body {
	width: 384px;
	padding: 0px;
	margin: 0px;
}

h1 {
	word-wrap: break-word;
	font-family:sans-serif;
}

</style>
</head>
<body>
	<h1>Buenos d&#237;as, <?=HtmlSpecialChars($ret['player_name'])?></h1>

<pre>
<? print_r($ret); ?>
</pre>

</body>
</html>
