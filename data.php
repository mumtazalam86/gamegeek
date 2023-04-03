<?php 
    $user_name = 'Thitsa';
    if(isset($_REQUEST['username']) && !empty($_REQUEST['username'])){
        $user_name = $_REQUEST['username'];
    }
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
    



    $objectsIds = [];
    foreach($responsearray['item'] as $value)
    {
        $objectsIds[] = $value['@attributes']['objectid'];
    }

    if(!empty($objectsIds)):
        // $objectChunks = array_chunk($objectsIds, 30);
        // foreach($objectChunks as $objectArray){
            // Get Board game data from API endpoint
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
            $boardgames['username'] = $user_name;


        //     break;
        // }
        
        // print_r($boardgames);die();
        echo json_encode($boardgames);
    endif;


    function pr($array = []){
        echo '<pre>'; print_r($array); echo '</pre>';
    }
        
?>