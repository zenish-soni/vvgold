<?php

define('BASE_URL',env('APP_URL'));
define('HELP_EMAIL','kroonal.manavadariya@gmail.com');
define('INFO_EMAIL','kroonal.manavadariya@gmail.com');
define('JS_VERSION','?ver=v.1.0');
define('CSS_VERSION','?ver=v.1.0');
define('HTML_VERSION','?ver=v.1.0');
define('USER_NAME','user');
define('API_NAMESPACE_PREFIX','Api\V1');
define('USER_NAMESPACE_PREFIX','User\V1');

define('DEFAULT_IMG_NAME','kk-693378000.png');

define('API_DEBUG',false);
define('API_VERSION','v1');
define('IS_LIVE',false);

define('INVOICE_FOLDER_NAME','invoices');
define('INQUIRY_FOLDER_PATH',public_path().'/'.INVOICE_FOLDER_NAME.'/');

/* Api All HTTP Response Code */
define("HTTP_UNAUTHORIZED",401);
define("HTTP_FORBIDDEN",403);
define("HTTP_SUCCESS",200);
define("HTTP_INTERNAL_SERVER_ERROR",500);
define("HTTP_NO_DATA_FOUND",404);
define("UNAUTHORIZED_MESSAGE","Unauthorized");
define("FORBIDDEN_MESSAGE","Forbidden");
define('FCM_AUTHORIZATION_KEY', 'AAAAPdkQFRQ:APA91bENI-I5f_JHp2e6Lw1EnkQyhoZs4Gy8dqmhRWKDjEaA3fnT8ArxFtaJmfTvxoQ7UycpMkMwYLhMr7LPcKitQRqyDXSGfcIljOCRgqZZzGTRtA8XhHHCTeFKie3elL_lq3r0UvFT');
define('FCM_URL','https://fcm.googleapis.com/fcm/send');
define('IS_DEBUG',true);