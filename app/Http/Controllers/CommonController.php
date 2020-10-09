<?php

namespace App\Http\Controllers;

use ParagonIE\Sodium;
use App\Model\ShkeysModel;

class CommonController extends Controller {

    public function denied() {
        return view('denied');
    }

    public function encrypt($text) {
        $SODIUM_KEY = config('config.SODIUM_KEY');
        $text = (string) $text;
        $nonce = "b63aa3219714f4ed017eade6e7d";
        $nonce = hex2bin($nonce);
        return base64_encode(sodium_crypto_secretbox($text, $nonce, $SODIUM_KEY));
    }

    public function decrypt($text) {
        $SODIUM_KEY = config('config.SODIUM_KEY');
        $text = str_replace(" ", "+", $text);
        $text = base64_decode($text);
        $nonce = "b63aa3219714f4ed017eade6e7d";
        $nonce = hex2bin($nonce);
        return sodium_crypto_secretbox_open($text, $nonce, $SODIUM_KEY);
    }

  public function curl_post($url, $type, $isAuth = false) {
        $tries = 0;
        while ($tries < 5) {
            $accesstoken = false;
            $shkeyid = 0;
            if ($type == "SH") {
                $date = (time() - 1 * 1 * 600);
                $getSH = ShkeysModel::getAllSHKeys('0');
                foreach ($getSH as $value) {
                    if ($value->blockdate < $date && !empty($value->blockdate)) {
                        $params = array(
                            'blockdate' => NULL,
                            'status' => '1'
                        );
                        ShkeysModel::updateSHKey($params, $value->id);
                    }
                }
                $getSH = ShkeysModel::getAllSHKeys('1', true);
                if (isset($getSH->token) && !empty($getSH->token)) {
                    $encode = base64_encode($getSH->app_key . ":" . $getSH->app_secret);
                    $accesstoken = $this->SH_accesstoken($encode);
                    if (strpos($accesstoken, "Error") > -1) {
                        $v_res = array();
                        $v_res['status'] = false;
                        echo json_encode($v_res, true);
                        exit;
                    } else { 
                        $shkeyid = $getSH->id;
                    }
//                   
                } else {
                    $subject = "ERROR While getting data  from Stubhub";
                    $body = 'Hello Mayur <br/><br/>
             STUBHUB  API fail to get data.<br/>
                
                <b>All StubHub Key Limit Reached</b>: <br/>';
                    $from = "stubhub@ticketbrokertools.com";
                    $to = 'mayur.gmail.com';
                    $header = '';
                    $header .= "MIME-Version: 1.0\r\n";
                    $header .= "Content-type: text/html\r\n";
                    $header .= "FROM: TicketBrokerTools (Stubhub)<$from>\r\n";
                    @mail($to, $subject, $body, $header);
                    $v_res = array();
                    $v_res['status'] = false;
                    echo json_encode($v_res, true);
                    exit;
                }
            }


            $ch = curl_init();
            $headers = array();
            if ($type == "VS") {
                $headers[] = 'cookie: D_SID=43.240.8.99:h92NvIxHggaqBoXtbJh75qCIctGmrAN/QzpPe2GitEg; '
                        . 'D_ZUID=5FB2B3E7-70BA-3EE0-907F-665098D11A80; '
                        . 'D_HID=C98D6D3E-B7FD-3001-86C8-5B158C6BB6B0; '
                        . 'D_UID=9B2C40D0-83DC-3396-AC26-9060CAA7A317';
            } else {
                if ($isAuth) {
                    if ($accesstoken) {
                        $headers[] = 'Authorization: Bearer ' . $accesstoken;
                        $headers[] = 'Accept: application/json';
                    }
                } else {
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Accept: application/json';
                }
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 0);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);

            $data = json_decode($response, true);
            if (isset($data['fault'])) {
                $tries++;
                if ($shkeyid > 0) {
                    $params = array(
                        'blockdate' => time(),
                        'status' => '0'
                    );
                    ShkeysModel::updateSHKey($params, $shkeyid);
                }
            } else {
                $tries = 5;
            }
        }
        if (!isset($data['fault'])) {
            return $response;
        }
    }

    function getAccessToken() {

        $fileContent = file_get_contents(dirname(__DIR__) . '/sh.token.order.txt');

        $shApp = array(
            "wBjMX3l0R30UHdfkROCUAQn1QaqBfw", 
            "IucTQ7o9UG8g1ScTSGTXo2Re3xAx"
        );
        if (strlen($fileContent) == 0)
            return $shApp[rand(0, count($shApp) - 1)];
        else {
            $apps = explode(PHP_EOL, $fileContent);

            $chosentoken = $apps[count($apps) - 1];

            array_pop($apps);
            array_unshift($apps, $chosentoken);

            file_put_contents(dirname(__DIR__) . '/sh.token.order.txt', implode("\n", $apps));

            return $chosentoken;
        }

        return $shApp[rand(0, count($shApp) - 1)];
    }

    public function SH_accesstoken($encodestring) { 
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.stubhub.com/sellers/oauth/accesstoken?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\n    \"username\": \"mayur.com\",\n    \"password\": \"\"\n}");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = 'Authorization: Basic ' . $encodestring;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
         
        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        }
        $result = json_decode($result,true); 
        curl_close($ch);
        return $result['access_token'];
    }
    public function vividAPIRequest($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = array();
        $headers[] = 'Accept: application/json'; 
        $headers[] = 'X-Api-Token: e4895b4f-1469-43eb-81b5-4565bfc6a941';
        $headers[] = 'X-Account: 3031';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
        return $result;
    }

}
