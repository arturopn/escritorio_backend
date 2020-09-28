<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAqahv1Cw:APA91bEd3MCqyxBFRnM9DtgTh0r95PA2NdON4gmXidUn-TYQMQlfaxO4qY7hyHBoAne4t2VkhAOJVUAveUD1IsEufhqk7xPlCu2WZJw1YszVqICk1VZwsEcHWy0ju1wjhc105NHYqxEw'),
        'sender_id' => env('FCM_SENDER_ID', '728675374124'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
