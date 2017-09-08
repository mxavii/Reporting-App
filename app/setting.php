<?php

return [
	//setting display error
	'displayErrorDetails'	=> true,

	'addContentLengthHeader' => false,

	//setting timezone
	'timezone'	=> 'Asia/Jakarta',

	//setting language
	'lang'	=> [
		'default'	=> 'id',
	],

	//setting db (with doctrine)
	'db'	=> [
		'url'	=> 'mysql://mit_report:masukaja123@localhost/mit_report',
	],

	'determineRouteBeforeAppMiddleware' => true,

	'reporting' => [
       'base_uri' => 'http://reporting-app.cpm/api/',
       'headers' => [
           'key' => @$_ENV['REPORTING_API_KEY'],
           'Accept' => 'application/json',
           'Content-Type' => 'application/json',
           'Authorization' => @$_SESSION['key']['key_token']
       ],
  ],
	// Setting View
	'view' => [
		'path'	=>	__DIR__ . '/../views',
		'twig'	=> 	[
			'cache'	=>	false,
			'debug' => true
		]
	],

    'base_url' => "http://reporting-app.cpm/",
    "plates_path" => "/../view",

    'flysystem' => [
    	'path'	=> __DIR__ . "/../public/assets",
     ]
];
