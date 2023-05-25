<?php


require_once(__DIR__ . '/vendor/autoload.php');

$credentials = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '%env(resolve:SENDINBLUE_API_KEY)%');
$apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(new GuzzleHttp\Client(),$credentials);

$sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
    'subject' => 'from the PHP SDK!',
    'sender' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
    'replyTo' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
    'to' => [[ 'name' => 'Max Mustermann', 'email' => 'example@example.com']],
    'htmlContent' => '<html><body><h1>This is a transactional email {{params.bodyMessage}}</h1></body></html>',
    'params' => ['bodyMessage' => 'made just for you!']
]);

try {
   $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
   print_r($result);
} catch (Exception $e) {
   echo $e->getMessage(),PHP_EOL;
}

?>