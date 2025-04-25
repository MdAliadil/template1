<?php

// Start time before the cURL request
$startTime = microtime(true);

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://app.paycoons.com/controller/api/v2/auth/PaycoonDynamicQR?payin_ref='.rand(1111,9999).'&amount=10&mNo=6296421747',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "merchantId":"2886255",
    "clientid":"PV8E7DLP-2EZ3-CX7S-I16M-AMK711E3T368",
    "clientSecretKey":"9TNKXZVQ7H9SL696L5UFTRM1LQKV815S"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

// End time after the cURL request
$endTime = microtime(true);

// Calculate the response time
$responseTime = $endTime - $startTime;

curl_close($curl);

// Print the response
echo "Response: " . $response . "\n";

// Print the response time in seconds (and milliseconds)
echo "Response time: " . number_format($responseTime, 4) . " seconds\n";
?>
