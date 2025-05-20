<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use Illuminate\Support\Facades\Mail;

// Route::get('/test-mail', function () {
//     Mail::raw('This is a test email from Beastlink University', function ($message) {
//         $message->to('kt.singson06@gmail.com') // change to your real address
//                 ->subject('Test Mail');
//     });
//     return 'Mail sent';
// });
