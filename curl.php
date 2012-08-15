<?
	##################################################################

	#
	# perform a 'simple' HTTP POST.
	#

	function curl_http_post($url, $post_args){

		return curl_http_request(array(
			'url'		=> $url,
			'post'		=> 1,
			'post_args'	=> $post_args,
		));
	}

	##################################################################

	#
	# perform an HTTP request.
	#

	function curl_http_request($args){

		$conn_timeout = floatval($args['conn_timeout']);
		$io_timeout = floatval($args['io_timeout']);

		if (!$conn_timeout) $conn_timeout = 5;
		if (!$io_timeout) $conn_timeout = 3;


		$curl_handler = curl_init();

		curl_setopt($curl_handler, CURLOPT_URL, $args['url']);
		curl_setopt($curl_handler, CURLOPT_CONNECTTIMEOUT, $conn_timeout);
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handler, CURLOPT_TIMEOUT, $io_timeout);
		curl_setopt($curl_handler, CURLOPT_FAILONERROR, FALSE);


		#
		# ignore invalid HTTPS certs. you may want to comment out
		# these lines...		
		#

		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_handler, CURLOPT_SSL_VERIFYHOST, FALSE);


		#
		# post?
		#

		if ($args['post']){
			curl_setopt($curl_handler, CURLOPT_POST, 1);
			if ($args['post_args']){
				curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $args['post_args']);
			}
		}


		#
		# build headers
		#

		$headers = is_array($args['headers']) ? $args['headers'] : array();

		if (!$headers['Expect']) $headers['Expect'] = '';

		$headers_flat = array();
		foreach ($headers as $k => $v){
			$headers_flat[] = "$k: $v";
		}

		curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $headers_flat);


		#
		# callback function to process the headers from return
		#
		
		curl_setopt($curl_handler, CURLOPT_HEADERFUNCTION, 'curl_process_headers');


		#
		# send the request
		#

		$GLOBALS['curl_headers'] = array();

		$body = @curl_exec($curl_handler);
		$info = @curl_getinfo($curl_handler);

		$head = $GLOBALS['curl_headers'];


		#
		# close the connection
		#

		curl_close($curl_handler);


		#
		# return
		#

		return array(
			'status'	=> $info['http_code'],
			'head'		=> $head,
			'body'		=> $body,
			'info'		=> $info,
		);
	}

	##################################################################

	function curl_process_headers($curl_handler, $string){

		$lines = split("\n", $string);

		foreach ($lines as $str){
			list($key, $value) = split(": ", $str);
			if ($key){
				$GLOBALS['curl_headers'][$key] = trim($value);
				#$GLOBALS['curl_headers'][strtolower($key)] = trim($value);
			}
		}

		return strlen($string);
	}

	##################################################################

?>
