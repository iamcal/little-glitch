<?
	include('config.php');
	include('curl.php');


	#
	# user has authed?
	#

	if ($_GET['authed']){

		if ($_GET['error']){
			$msg = "There was a problem authenticating you: ".HtmlSpecialChars($_GET['error']);
			if ($_GET['error_description']){
				 $msg .= ' ('.HtmlSpecialChars($_GET['error_description']).')';
			}
			fail_500($msg);
		}

		if (!$_GET['code']){
			fail_500("No OAuth code in auth callback");
		}

		$args = array(
			'grant_type'	=> 'authorization_code',
			'code'		=> $_GET['code'],
			'client_id'	=> $cfg['glitch_client_id'],
			'client_secret'	=> $cfg['glitch_client_secret'],
			'redirect_uri'	=> $cfg['redir_url'],
		);

		$ret = curl_http_post($cfg['glitch_api_base']."/oauth2/token", $args);

		if ($ret['status'] != 200 && $ret['status'] != 400){
			fail_500("Unexpected HTTP status code: {$ret['status']}");
		}

		$obj = @json_decode($ret['body'], true);

		if (!is_array($obj) || !count($obj)){
			fail_500("Unable to parse JSON response");
		}

		if (strlen($obj['error'])){
			fail_500("Glitch API error: ".HtmlSpecialChars($obj['error']));
		}

		if (!strlen($_COOKIE['berg_redir'])){
			fail_500("Missing redir cookie - are cookies enabled?");
		}


		$base = $_COOKIE['berg_redir'];
		$base .= "?config[access_token]=".urlencode($obj['access_token']);

		echo "ok - $base";
		exit;
	}


	#
	# stash the redirect cookie
	#

	setcookie('berg_redir', $_GET['return_url']);


	#
	# redirect to glitch auth endpoint
	#

	$args = array(
		'response_type'	=> 'code',
		'client_id'	=> $cfg['glitch_client_id'],
		'redirect_uri'	=> $cfg['redir_url'],
		'scope'		=> 'identity',
	);

	$url = $cfg['glitch_api_base']."/authorize?".http_build_query($args);

	header("location: $url");
	exit;



	#
	# failure helper function
	#

	function fail_500($msg){

		header("HTTP/1.0 500 Application error");
		echo $msg;
		exit;
	}
