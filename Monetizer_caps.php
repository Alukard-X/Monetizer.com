<?php
//Made by @Alukard_X


$monetizer_api = "PUT_YOUR_API_KEY_HERE";
//https://app.monetizer.com/profile  Access Token
$monetizer_id = "PUT_YOUR_ID_NUMBER_AT_MONETIZER_HERE";
//https://app.monetizer.com/profile Partner ID
$propellerads_api = "PUT_YOUR_API_KEY_HERE";
//https://partners.propellerads.com/#/profile/api
$zeropark_api = "PUT_YOUR_API_KEY_HERE";

$file = file("http://example.com/file/file_with_params.txt");
//In it:
//    7d7034,5801624,PropellerAds
//    7d7035,5801625,PropellerAds
// offer_id like 7d7034
// ad_campaign_id like  5801624
// ad_network_name like PropellerAds


foreach ($file as $key) {
  $array = explode(',',$key);
  $offerid = $array[0];
  $ad_campaign_id = $array[1];
  $trafficsource = $array[2];



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.monetizer.co/v3/offersAggregatedGlobal.php?min_payout=0&max_payout=300&min_epc=0&max_epc=2.00&min_revenue=0&max_revenue=50000&conversion_flow=all&vertical=all&offer_id=' . $offerid . '&_='. time(),
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'x_afflow_api_token:' .  $monetizer_api . ''
  ),
));

$response = json_decode(curl_exec($curl));

curl_close($curl);

$bl = json_decode($response[0]->aff_campaign_partners_bl_data);

if($response[0]->vauto_paused == "1" || $response[0]->outage !== "0" || empty($response) || !empty(preg_grep('/^' . $monetizer_id .'/',$bl))) {
        if($trafficsource = 'PropellerAds') {
          $headers = array();
          $headers[] = 'Accept: application/json';
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'Authorization: Bearer ' .  $propellerads_api . '';
          $ad_campaign = ["campaign_ids" => [$ad_campaign_id]]; 
          $ch = curl_init();

          curl_setopt($ch, CURLOPT_URL,"https://ssp-api.propellerads.com/v5/adv/campaigns/stop");
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_POSTFIELDS,
                  json_encode($ad_campaign));
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_exec($ch);
          curl_close($ch);
          echo("1");
              } elseif ($trafficsource = 'Zeropark'){
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://panel.zeropark.com/api/campaign/$ad_campaign_id/pause");
                curl_setopt($ch, CURLOPT_POST, true);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
                $headers = [
                  'api-token: ' . $zeropark_api . ''
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
                $server_output = curl_exec ($ch);
        }
} else {
          if($trafficsource = 'PropellerAds') {
            $headers = array();
            $headers[] = 'Accept: application/json';
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer ' .  $propellerads_api . '';
            $ad_campaign = ["campaign_ids" => [$ad_campaign_id]]; 
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,"https://ssp-api.propellerads.com/v5/adv/campaigns/play");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS,
                    json_encode($ad_campaign));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_exec($ch);
            curl_close($ch);
            echo("2");
          }  elseif ($trafficsource = 'Zeropark'){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"https://panel.zeropark.com/api/campaign/$ad_campaign_id/resume");
            curl_setopt($ch, CURLOPT_POST, true);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
            $headers = [
                'api-token: ' . $zeropark_api . ''
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
            $server_output = curl_exec ($ch);
    }
};
}




?>