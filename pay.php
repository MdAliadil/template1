<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://proapi.safepayindia.com/QRService.svc/StatusCheck',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "order_id": "SM2024091814222744564199"
}',
  CURLOPT_HTTPHEADER => array(
    'Client-ID: SFP_11MXTSQ8WS4811MXTSQ8WS',
    'Client-Secret: 11MXTSQ8WSD14811MXTSQ8WSD1',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;