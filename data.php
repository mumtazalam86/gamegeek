<?php 
    
    if(!isset($_REQUEST['username']) || empty($_REQUEST['username'])){
        echo json_encode(['status' => 404, 'error' => "Username is missing. Please add valid Username to get the data."]); exit;
    }
    
    $user_name = $_REQUEST['username'];
    /* Pass Arguments */
	$url = 'https://api.geekdo.com/xmlapi/collection/'.$user_name;
    $get_user_name = explode('/', $url);
    $user_name = end($get_user_name);
    $user_data = ['user_name' => $user_name ];
    $crl = curl_init();
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($crl);

     if(!$response){
        die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
    }      
    curl_close($crl);
    
    
    $responsearray = json_decode(json_encode((array)simplexml_load_string($response)),true);
    
    if(isset($responsearray['error'])){
        echo json_encode(['status' => 404, 'error' => $responsearray['error']['message']]); exit;
    }

    // pr($responsearray); die;
    $objectsIds = [];
    $boardgameBasicData = [];
    foreach($responsearray['item'] as $value)
    {
        $objectsIds[] = $value['@attributes']['objectid'];
        $boardgameBasicData[$value['@attributes']['objectid']]['name'] =  $value['name'];
        $boardgameBasicData[$value['@attributes']['objectid']]['rating'] = '';
        if(isset($value['stats']['rating'])){
            $boardgameBasicData[$value['@attributes']['objectid']]['rating'] =  ($value['stats']['rating']['average']['@attributes']['value']/2);
        }
    }

    if(!empty($objectsIds)):
        $url = 'https://api.geekdo.com/xmlapi/boardgame/'.implode(',',$objectsIds).'?';
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($crl);
        if(!$response){
            die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
        }
        curl_close($crl);

        // echo $response; die;
        $boardgames = json_decode(json_encode((array)simplexml_load_string($response)),true);
    
        $boardgames['status'] = 200;
        $boardgames['boardgameBasicData'] = $boardgameBasicData;
        // pr($boardgames); die;
        echo json_encode($boardgames); exit;
    endif;


    function pr($array = []){
        echo '<pre>'; print_r($array); echo '</pre>';
    }
        
?>