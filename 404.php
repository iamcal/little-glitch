<?
	$req = $_SERVER['REQUEST_URI'];
	$rdr = $_SERVER['REDIRECT_URL'];

	if (strlen($_SERVER['REDIRECT_QUERY_STRING'])){
		$rdr .= "?" . $_SERVER['REDIRECT_QUERY_STRING'];
	}
?>
<h1>404</h1>
<pre>
Requested URL  : <?=$req."\n"?>
Translated URL : <?=$rdr."\n"?>
</pre>
