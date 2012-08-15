<?
	$out = array(
		'publication_api_version'	=> '1.0',
		'name'				=> 'Glitch',
		'description'			=> 'A daily report of your progress in Glitch',
		'delivered_on'			=> 'every day',
		'external_configuration'	=> 'true',
		'config' => array(
			'fields' => array(),
		)
	);

	header("Content-type: application/json; charset=utf-8");
	echo JSON_encode($out);
