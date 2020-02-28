<?php

Route::post('/paypal/create', 'PaymentController@paypalCreate');
Route::post('/paypal/execute', 'PaymentController@paypalExecute');
Route::post('/stripe/create', 'PaymentController@stripCreate');
Route::post('/stripe/execute', 'PaymentController@stripExecute');
