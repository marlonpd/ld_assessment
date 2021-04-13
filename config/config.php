<?php

// config/config.php
return [
   'domain' => env('APP_URL'),
   'fromEmail' => env('MAIL_USERNAME'), 
   'roles' => [
       'admin' => 'admin',
       'user' => 'user',
   ], 
   'imageMaxDimension' => [
       'height' => 250,
       'width'  => 250
   ]
];