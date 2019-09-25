<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class Notification extends Model
{
    use CrudTrait;
    protected $table = 'notification';

    protected $fillable = ['token_id', 'type', 'title', 'message', 'status'];

    public function fcmToken()
    {
        return $this->belongsTo('App\Models\FcmToken', 'token_id', 'id');
    }

    public static function notification($fcmNotification)
    {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';


        $headers = [
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function web_notification($webNotification)
    {

        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';


        $headers = [
            'Authorization: key=' . env('SERVER_KEY'),
            'Content-Type: application/json'
        ];

//        $client = new Client();
//        $result = $client->post($fcmUrl, [
//            'form_params' => $webNotification,
//            'headers' => $headers,
//            'http_errors' => false,
//            'verify' => 'certificates/certificate.pem'
//        ]);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webNotification));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

}
