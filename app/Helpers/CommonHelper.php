<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use App\Models\UserOtp;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CommonHelper
{
    public const STATUS_ENABLED = 1;
    public const STATUS_DISABLED = 2;

    /**
     * Generate OTP using the country and phone.
     *
     * @param  string  $country
     * @param  string  $phone
     * @return \App\Models\UserOtp
     */
    public static function generateOtp(string $country, string $phone)
    {
        $customer = User::where(function ($qery) use ($country, $phone) {
            $qery->where('phone', $phone)
                ->where('phone_country', $country);
        })->first('id');

        $otp = rand(1111, 9999);

        if ( !\App::environment('production')) {
            $otp = 1234;
        }

        $userOtp = UserOtp::create([
            'phone_country' => $country,
            'phone' => $phone,
            'otp' => $otp,
            'user_id' => $customer ? $customer->id : null
        ]);

        return $userOtp;
    }

     /**
     * Send OTP to the cstomer via twillio.
     */
    public static function sendSms($userOtp, string $num, string $text = '')
    {
        try {
            $otp=$userOtp;
            if($userOtp instanceof UserOtp){
                $otp=$userOtp->otp;
            }
         
            if ( !\App::environment('production') &&  !in_array(str_replace("+","",$num), ["6280407767"])) {
                if($userOtp instanceof UserOtp)
                $userOtp->update(['sent_at' => now()]);

                return true;
            }

            if ($text === '') {
                $text = 'Welcome to Brunch Me. Use this code to verify your mobile number: ' . $otp;
            }
            $client = new Client(config('constants.twilio_sid'), config('constants.twilio_auth_token'));
            $message = $client->messages->create(
                $num,
                [
                    'from' => config('constants.twilio_from_number'), // From a valid Twilio number
                    'body' => $text,
                ]
            );
            if($userOtp instanceof UserOtp)
            $userOtp->update(['sent_at' => now()]);

            return $message->sid;
        } catch (Exception $exception) {
            \report($exception);

            return 111;
        }
    }

    /**
     * remove leading 0 from phone number
     */
    public static function updatePhone(string $phone)
    {
        return str_replace(' ', '', ltrim($phone, '0'));
    }

    /**
     * upload base64 encoded image
     *
     * @param  string  $path
     * @param  string  $imgBlobUrl
     * @return string
     */
    public static function uploadBlobImage($path, $imgBlobUrl)
    {
        $fileName = md5(self::getRandomNumber() . time()) . '.jpeg';

        $image = base64_decode($imgBlobUrl);

        Storage::disk(config('constants.image_path.driver'))->put($path . $fileName, $image, 'public');

        return $fileName;
    }

    /**
     * upload image to storage folder
     *
     * @param  string  $path
     * @param  string  $file
     * @return string
     */
    public static function uploadImage($path, $file)
    {
        $fileName = md5(hexdec(uniqid()) . time()) . '.' . $file->getClientOriginalExtension();
        Storage::disk(config('constants.image_path.driver'))->put($path . $fileName,File::get($file), 'public');
        return $fileName;
    }

    /**
     * generate and return random number
    */
    public static function getRandomNumber()
    {
        return hexdec(uniqid());
    }
    
    /**
     * generate Booking Id
    */
    public static function generateBookingId() {
        $bookingId = 'B' . uniqid() . Str::random(6);
        return $bookingId;
    }
    
    /**
     * Check time is inbetween or not
    */
    public static function isTimeBetween($startTime, $endTime, $checkTime) {
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        $checkTime = strtotime($checkTime);
    
        return ($checkTime >= $startTime && $checkTime <= $endTime);
    }

    /**
     * Send Push Notification
     *
     * @param  string  $deviceTokens fcm_token of user
     * @param  array  $additionalData notification data
     * @param  string  $priority priority of notification
     * @return bool
     */
    // public static function sendPushNotification($deviceTokens, $additionalData = [], $priority = 'high')
    // {
    //     try {
    //         if (!empty($additionalData)) {
    //             $res =  Larafirebase::withTitle($additionalData['title'])
    //                 ->withBody($additionalData['body'])
    //                 ->withPriority('high')
    //                 ->withAdditionalData([
    //                     'type' => $additionalData['type'] ?? '',
    //                     'notificationType' => $additionalData['notificationType'] ?? '',
    //                     'connection_id' => $additionalData['connection_id'] ?? '',
    //                     'name' => $additionalData['name'] ?? '',
    //                 ])
    //                 ->sendNotification($deviceTokens);
    //         }

    //         return false;
    //     } catch (Exception $ex) {
    //         Log::error($ex->getMessage(), $ex->getTrace());
    //         return false;
    //     }
    // }

    public static function sendCurlPushNotification($device_ids, $data) {

        try {

            $url = 'https://fcm.googleapis.com/fcm/send';
            $api_key = null;

            if($data['role'] == 'business') {
                $api_key = env('FIREBASE_BUSINESS_AUTH_KEY_ID');
            }
            elseif($data['role'] == 'customer') {
                $api_key = env('FIREBASE_CUSTOMER_AUTH_KEY_ID');
            }

            if($api_key) {

                $count   = 1;
                $fields  = array(
                    'registration_ids' => [$device_ids],
                    'data' => array(
                        'title'                     => $data['title'],
                        "message"                   => $data['body'],
                        "notification_type"         => $data['notificationType'],
                        "notification_message_type" => '1',
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "android_channel_id" => "default_channel"
                    ),
                    'notification' => array(
                        'title' => $data['title'],
                        'body'  => $data['body'],
                        'sound' => 'default',
                        'badge' =>  1,
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "android_channel_id" => "default_channel"
                    ),
                    'priority' => 'high'
                );
                
                if(isset($data['booking_number'])) {
                    $fields['data']['booking_number'] = $data['booking_number'];
                }
                if(isset($data['restaurant_id'])) {
                    $fields['data']['restaurant_id'] = $data['restaurant_id'];
                }

                //header includes Content type and api key
                $headers = array(
                    'Content-Type:application/json',
                    'Authorization:key=' . $api_key
                );
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                $result = curl_exec($ch);
               
                if ($result === FALSE) {
                    die('FCM Send Error: ' . curl_error($ch));
                }
                else
                 {
                    $r = (array)json_decode($result);
                    Notification::create([
                         'user_id' => $data['user_id'],
                         'status'  => $r['success'],
                         'data'    => json_encode($fields)
                    ]);
                }
                curl_close($ch);
                return $result;
            }

        } catch (Exception $e) {
            Log::error($e->getMessage(), $e->getTrace());
            echo 'Message: ' . $e->getMessage();
        }

    }

    public function anotherWaytosendpush() {

        //$url = "https://fcm.googleapis.com/fcm/send";
        //$token = "eWbOFmf3DbI:APA91bE9_LSK4Zn-pbR15CVwtdmsSYhbCb9xVD3ZkBMJqCVacTZiGBjgHKXFodBn9CzGZr8cJFV_EPBc0QCBw6-e9SaudDS-EoTk6ZRxFMdV2IniuFkcM7pAQZE7I9dFYuWkOqpDV_ox";
        //$serverKey = 'AAAAqumLf00:APA91bGvrtJ01_PyVuQycqF7DKvKrjvsbL3vNbAEWHAG1YMQrTrBQMzzYL0UOUThR-ahStjuaiIrbuu0oJQpLtOoen-kR5Lb-1Lw1iyQ2EyOvS6cQqJyeYG-kI5PF_d044LtaXuJViCY';
        //$serverKey = 'AAAAD1wL9MY:APA91bHcD4S3xa_oFEwkz1maKycZndrYHhEp8IucegY2_Rqd22pmI4fCZww5xMFy_8I7DuMBWWbnfzoyq7qmooH8-QOvjXpfzlPH5eOTCDwg7nF4CiBofl85n7HEs6YYtneqPHnQXq9X';
       // $title = "wallet notification";
       // $body = "Test notification from order delivery server";
        //$notification = array('title' =>$title , 'body' => $body, 'sound' => 'default', 'badge' => '1');
        //$arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        //$json = json_encode($arrayToSend);
       // $headers = array();
        //$headers[] = 'Content-Type: application/json';
        //$headers[] = 'Authorization: key='. $serverKey;
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"POST");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        // curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //$ch = curl_init();
        //curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        //Send the request
        //$response = curl_exec($ch);
        //curl_close($ch);
        //return $response;
    }

}    