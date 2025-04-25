<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://proapi.safepayindia.com/PayoutService.svc/StatusCheck',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "order_id":"SMBL11666080"
}',
  CURLOPT_HTTPHEADER => array(
    'Client-ID: SFP_LFJ1YYAX8A48LFJ1YYAX8A',
    'Client-Secret: LFJ1YYAX8ASA48LFJ1YYAX8ASA',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
