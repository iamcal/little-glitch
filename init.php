<?
	include('config.php');
	include('curl.php');


	function api_call($method, $args){

		$ret = curl_http_post($GLOBALS['cfg']['glitch_api_base']."/simple/{$method}", $args);

		if ($ret['status'] != 200){
			return array(
				'ok'		=> 0,
				'error'		=> 'http_failed',
				'http_code'	=> $ret['status'],
				'rsp'		=> $ret,
			);
		}

		$obj = @json_decode($ret['body'], true);

		if (!is_array($obj) || !count($obj)){
			return array(
				'ok'		=> 0,
				'error'		=> 'bad_json',
				'rsp'		=> $ret,
			);
		}

		return $obj;
	}

	function fail_500($msg){

		header("HTTP/1.0 500 Application error");
		echo $msg;
		exit;
	}

	function fail_400($msg){

		header("HTTP/1.0 400 Bad request");
		echo $msg;
		exit;
	}

