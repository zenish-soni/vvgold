<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Response;
use App\Infrastructure\AppConstant;
use App\Models\User, Auth, DB, App\Models\Company, App\Models\LuTimeZone, App\Models\Notification;

class BaseController extends Controller
{
    /**
	* Save request in file from api
	*
    * @param  \Illuminate\Http\Request  $request
	*/
    public function __construct(Request $request){
    	if(API_DEBUG == true && $request->is('api/'.API_VERSION.'/*')){
            $req_dump = print_r(json_encode($request->all()), TRUE);
            $req_dump .= sprintf(url()->current());
            $fp = fopen('newfile.txt', 'a');
            fwrite($fp, "\n".\Carbon\Carbon::now());
            fwrite($fp, "\n".$req_dump);
            fwrite($fp, "\n");
            fclose($fp);
        }
    }

    public $imageArr = array('jpeg','jpg','png');

    /**
    * Check request data
    *
    * @param array $params required params
    * @param array $request_data requested params
    * @return string
    */
    public static function checkRequestData($params,$request_data){
        $response = '';
        if(is_array($params)){
            if(!empty($request_data)){
                foreach($params as $value){
                    if(!empty($request_data[$value])){
                        $response = 'SUCC100';
                    }else{
                        $response = trans('messages.ERR100');
                        break;
                    }
                }
            }else{
                $response = trans('messages.ERR101');
            }
        }else{
            $response = trans('messages.ERR102');
        }

        return $response;
    }

    /**
    * Check api request data
    *
    * @param array $params required params
    * @param array $request_data requested params
    * @return string
    */
    public static function checkRequestDataAPI($params,$request_data){
        $response = '';
        if(is_array($params)){
            if(!empty($request_data)){
                foreach($params as $value){
                    if(!empty($request_data[$value])){
                        $response = 'SUCC100';
                    }else{
                        $response = 'ERR100';
                        break;
                    }
                }
            }else{
                $response = 'ERR101';
            }
        }else{
            $response = 'ERR102';
        }
        return $response;
    }

    /**
    * Check collection is empty
    *
    * @param collection $collection
    * @return bool
    */
    public function isCollectionEmpty($collection){
        $response = false;
        if($collection->isEmpty()){
            $response = true;
        }
        return $response;
    }

    /**
    * Check collection is not empty
    *
    * @param collection $collection
    * @return bool
    */
    public function isCollectionNotEmpty($collection){
        $response = false;
        if($collection->isNotEmpty()){
            $response = true;
        }
        return $response;
    }

    /**
    * Generate api token.
    *
    * @param string $value
    * @return string
    */
    public function generateApiToken($value)
    {
        return md5(time().Str::random(30)).md5($value).md5(Str::random(30).time());
    }

    /**
    * Get JSON Response.
    *
    * @param object $serviceResponse
    * @param int $code
    * @return json
    */
    public function GetJsonResponse($serviceResponse,$code = 200){
       $jsonResponse = Response::make(json_encode($serviceResponse), $code);
       $jsonResponse->header('Content-Type', 'application/json');
       return $jsonResponse;
    }

    /**
    * Encrypt - Decrypt Value
    *
    * @param string $action
    * @param string $string
    * @return string
    */
    public static function encryptor($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        //pls set your unique hashing key
        $secret_key = AppConstant::$secret_key;
        $secret_iv = AppConstant::$secret_iv;

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        //do the encyption given text/string/number
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            //decrypt the given text/string/number
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    /**
    * Encrypt ID
    *
    * @param string $stringKey
    * @param string $id
    * @return string
    */
    public function getEncryptID($stringKey,$id){
        $encrypt_id = $stringKey.'='.$id;
        $encrypt_id = BaseController::getEncryptDecryptID('encrypt',$encrypt_id);
        return $encrypt_id;
    }

    /**
    * Url Encode
    *
    * @param string $action
    * @param string $propertyName
    * @return string
    */
    public static function getEncryptDecryptID($action,$propertyName){
        return urlencode(BaseController::encryptor($action, $propertyName));
    }

    /**
    * Url Decode
    *
    * @param string $action
    * @param string $propertyName
    * @return string
    */
    public static function getEncryptDecryptValue($action,$propertyName){
        return urldecode(BaseController::encryptor($action, $propertyName));
    }

    /**
    * Explode value
    *
    * @param string $multiQueryString
    * @param string $queryStringKey
    * @return string
    */
    public static function getExplodeValue($multiQueryString,$queryStringKey){
        if(Str::contains($multiQueryString,'&'.$queryStringKey.'=') < 0){
            $first = explode('=',$multiQueryString);
            return $first[1];
        }
        if( Str::startsWith($multiQueryString,$queryStringKey.'=') == 1 || Str::contains($multiQueryString,'&'.$queryStringKey.'=') > 0) {
            $MultiQueryStringArray = explode('&', $multiQueryString);

            $first = current(array_filter($MultiQueryStringArray, function ($keyValue) use ($multiQueryString, $queryStringKey) {
                return Str::contains($keyValue, $queryStringKey . '=') > 0;
            }));
            if(!empty($first))
                return explode('=',$first)[1];
        }
        return 'Incorrect Encryption';
    }

    /**
    * Validation message handler
    *
    * @param string $tableName
    * @param array $reqData
    * @return json $reqdata
    */
    public static function getValidationMessagesFormat($validationMessage){
        $validationMessagesArray = "";
        if(!empty($validationMessage)){
            foreach($validationMessage->toArray() as $key => $value){
                $validationMessagesArray.= $value[0];
            }
        }
        return $validationMessagesArray;
    }

    /**
    * Get Current Date Time
    *
    * @return dateTime
    */
    public function getDateTime()
    {
        return \Carbon\Carbon::now()->toDateTimeString();
    }

    /**
    * Get Current Date
    *
    * @return dateTime
    */
    public function getDate()
    {
        return \Carbon\Carbon::now()->toDateString();
    }

    /**
    * Convert null value to char or integer number
    *
    * @return dateTime
    */
    public function convertNullToChar($field,$is_int = 0)
    {
        return !empty($field) ? (!empty($is_int) ? (int)$field : $field) : (!empty($is_int) ? 0 : "");
    }

    /**
    * Convert static null value to char or integer number
    *
    * @return dateTime
    */
    public static function staticConvertNullToChar($field,$is_int = 0)
    {
        return !empty($field) ? (!empty($is_int) ? (int)$field : $field) : (!empty($is_int) ? 0 : "");
    }

    /**
    * Generate random number
    **/
    public function generateRandomNumber($length = 12) {
        $number = '1234567890';
        $numberLength = strlen($number);
        $randomNumber = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNumber .= $number[rand(0, $numberLength - 1)];
        }
        return $randomNumber;
    }

    /**
    * Get week start and end date
    *
    * @return array $week_date
    */
    public function getWeekStartEndDate() {
        date_default_timezone_set($this->getUserTimezone(1));
        $date = date('Y-m-d');
        $time = strtotime($date);
        $today_day = date('D');

        if($today_day == "Mon"){
            $week_date['start_date'] = date('Y-m-d',strtotime("Monday This Week"));
        }else{
            $week_date['start_date'] = date('Y-m-d',strtotime("Monday Last Week"));
        }
        $week_date['end_date'] = date('Y-m-d',strtotime("Sunday This Week"));
        return $week_date;
    }

    /**
    * Get week start and end date
    *
    * @param date $date
    * @return array $date_array
    */
    public function x_week_range($date) {
        $ts = strtotime($date);
        $start = (date('N', $ts) == 0) ? strtotime("Monday Last Week",$ts) : strtotime("Monday This Week",$ts);
        return array(date('Y-m-d', $start),date('Y-m-d', strtotime('next Sunday', $start)));
    }

    /**
    * Get month start and end date
    *
    * @param date $date
    * @return array $date_array
    */
    public function x_month_range($date) {
        $startDate = date("Y-m-01", strtotime($date));
        $endDate = date("Y-m-t", strtotime($date));
        return array($startDate,$endDate);
    }

    public function saveUserImage($base64img) {
        $v_random_image = time().Str::random(10).'.png';
        $tmpFile = $v_random_image;
        if (strpos($base64img,'data:image/jpeg;base64,') !== false) {
                $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.jpeg';
            }
            if (strpos($base64img,'data:image/png;base64,') !== false) {
                $base64img = str_replace('data:image/png;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.png';
            }
            if (strpos($base64img,'data:image/webp;base64,') !== false) {
                $base64img = str_replace('data:image/webp;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.png';
            }
            if (strpos($base64img,'data:image/jpg;base64,') !== false) {
                $base64img = str_replace('data:image/jpg;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.jpg';
            }
            if (strpos($base64img,'data:image/gif;base64,') !== false) {
                $base64img = str_replace('data:image/gif;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.gif';
            }
        //$tmpFile = $v_random_image.'.png';
        $data = base64_decode($base64img);
        file_put_contents(storage_path().'/app/user_images/'.$tmpFile, $data);

        $destinationPath = AppConstant::getUserImageThumbPath();
        $img = \Image::make(AppConstant::getUserImage($tmpFile));
        $img->resize(100, 100, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$tmpFile);
        return $tmpFile;
    }

    public function saveImage($base64img) {
        $v_random_image = time().Str::random(10).'.png';
        $tmpFile = $v_random_image;
        if (strpos($base64img,'data:image/jpeg;base64,') !== false) {
                $base64img = str_replace('data:image/jpeg;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.jpeg';
            }
            if (strpos($base64img,'data:image/png;base64,') !== false) {
                $base64img = str_replace('data:image/png;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.png';
            }
            if (strpos($base64img,'data:image/webp;base64,') !== false) {
                $base64img = str_replace('data:image/webp;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.png';
            }
            if (strpos($base64img,'data:image/jpg;base64,') !== false) {
                $base64img = str_replace('data:image/jpg;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.jpg';
            }
            if (strpos($base64img,'data:image/gif;base64,') !== false) {
                $base64img = str_replace('data:image/gif;base64,', '', $base64img);
                //$tmpFile = $v_random_image.'.gif';
            }
        //$tmpFile = $v_random_image.'.png';
        $data = base64_decode($base64img);
        file_put_contents(storage_path().'/app/media/'.$tmpFile, $data);
        return $tmpFile;
    }

    public function userTimeEclapse($createdDate,$timezone){
        $currentTime = \Carbon\Carbon::now()->setTimezone($timezone);
        $difference = $createdDate->diff($currentTime);

        $timeStr = '';

        if($difference->y > 0){
            $timeStr = $difference->y.' years ago';
            return($timeStr);
        }
        if($difference->m > 0){
            $timeStr = $difference->m.' months ago';
            return($timeStr);
        }
        if($difference->d > 0){
            $timeStr = $difference->d.' days ago';
            return($timeStr);
        }
        if($difference->h > 0){
            $timeStr = $difference->h.' hrs ago';
            return($timeStr);
        }
        if($difference->i > 0){
            $timeStr = $difference->i.' mins ago';
            return($timeStr);
        }
        $timeStr = $difference->s.' sec ago';
        return($timeStr);
    }

    public function getLoginResponse($record){
        return array(
            'id'                => $record->id,
            'name'              => $this->staticConvertNullToChar($record->name),
            'state'             => $this->staticConvertNullToChar($record->state),
            'city'              => $this->staticConvertNullToChar($record->city),
            'address'           => $this->staticConvertNullToChar($record->address),
            'company_name'      => $this->staticConvertNullToChar($record->company_name),
            'phone_number'      => $record->phone_number,
            'is_approved'       => $record->is_approved,
            'api_token'         => $record->api_token
        );
    }

    public function getSizePercentage(){
        return array(10 , 20 , 30 , 40 , 50 , 60 , 70 , 80 , 90 , 100);
    }

    public function getWidthBasedOnPercentage($percentage){
        switch ($percentage) {
            case '10':
                $width = 100;
                break;
            case '20':
                $width = 200;
                break;
            case '30':
                $width = 300;
                break;
            case '40':
                $width = 400;
                break;
            case '50':
                $width = 500;
                break;
            case '60':
                $width = 600;
                break;
            case '70':
                $width = 700;
                break;
            case '80':
                $width = 800;
                break;
            case '90':
                $width = 900;
                break;
            case '100':
                $width = 1000;
                break;
            
            default:
                $width = 300;
                break;
        }

        return $width;
    }

    /** 
     * Get Status Name
    **/
    public function getStatusName($id){
        $productTypes = array(
            1 => 'Pending', 
            2 => 'Approved',
            3 => 'Cancelled',
        );
        
        if(!array_key_exists($id, $productTypes)){
            $id = 1;
        }
        return $productTypes[$id];
    }

    /**
    * Send notification for new products
    **/
    public function pushNotifications($deviceToken,$message,$id,$image){        
        $msg = array(
            'body'  => $message,
            'id' => $id,
            'title' => "VV Gold",
            'icon'  => "Default Icon",
            'sound' => 'mySound',
            'timestamp' => time(),
            'image' => $image
        );

        $fields = array('registration_ids' => $deviceToken, 'notification' => $msg, 'data' => $msg, "priority" => "high");
        $headers = array('Authorization: key=' . FCM_AUTHORIZATION_KEY,'Content-Type: application/json');
        $isSent = 0;

        #Send Reponse To FireBase Server
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );

        $response = json_decode($result);
    }
}
