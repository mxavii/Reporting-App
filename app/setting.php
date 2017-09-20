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
		'url'	=> 'mysql://dhezign:PXUqTYk1@localhost/dhezign_reporting',
	],

	'determineRouteBeforeAppMiddleware' => true,

	'reporting' => [
       'base_uri' => 'http://reporting.mitschool.co.id/api/',
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

    'base_url' => "http://reporting.mitschool.co.id",
    "plates_path" => "/../view",

    'flysystem' => [
    	'path'	=> __DIR__ . "/../public/assets",
     ]
];
