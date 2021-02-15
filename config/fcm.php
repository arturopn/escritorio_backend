<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAvQBzV6g:APA91bG-KI7q81ZYqgsCedTtprcZe_1FQMYnHhSydfVexAA-Cq9GptLaIzjhclXNrQtRrqklcaWpVvLww1lUoKN6xlFoUL2pkqrDi3Vuo5hUE-JreyG4-WJdVjCGOE5gozXaE6SbpQon'),
        'sender_id' => env('FCM_SENDER_ID', '811756378024'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
