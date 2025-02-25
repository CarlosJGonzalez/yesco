<?php
session_start();
error_reporting( 0 );

/*******************************************************
 * LoalYesco ID is 1482
 * Here we compare if the session client == 1482
 * If not, stop execution.
 */
if ( ! $_SESSION['client'] || $_SESSION['client'] != "1482" ){
    http_response_code( 403 );
    echo json_encode(
        [ 
            'message' => 'Forbidden. Please log in.'
        ]
    );
    exit;
}

if( $_POST ){
    try{
        include $_SERVER['DOCUMENT_ROOT']."/includes/connect.php";

        $address    = htmlspecialchars($_POST['address']);
        $address2   = htmlspecialchars($_POST['address2']);
        $city       = htmlspecialchars($_POST['city']);
        $state      = htmlspecialchars($_POST['state']);
        $zip        = filter_var($_POST['zip'], FILTER_SANITIZE_NUMBER_INT);
        $storeid    = filter_var($_POST['storeid'], FILTER_SANITIZE_NUMBER_INT);

        if( $storeid ){
            $fullAddress = $address.' '.$address2.' '.$city.' '.$state.' '.$zip;
            $prepAddr = str_replace(' ','+',$fullAddress);
            $prepAddr = str_replace( '#', '', $prepAddr );
            /***
             * this key is under Das-Dashboard Project
             * Only must be used in CURL calls since it is not restricted
             */
            $maps_api = '';
            if( defined( 'GOOGLE_MAPS_API_KEY' ) ){
                $maps_api = GOOGLE_MAPS_API_KEY;
            }
            $urlmaps="https://maps.google.com/maps/api/geocode/json?address={$prepAddr}&sensor=false&key={$maps_api}";

            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $urlmaps );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_PROXYPORT, 3128 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
            curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
            $response = curl_exec( $ch );
            $curl_status_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
            curl_close($ch);
            
            $response_a = json_decode($response);
            if( $response && 
                property_exists( $response_a, 'results' ) && 
                property_exists( $response_a->results[0], 'geometry' ) &&
                property_exists( $response_a->results[0]->geometry, 'location' )
            ){
                $latitude = $response_a->results[0]->geometry->location->lat;
                $longitude = $response_a->results[0]->geometry->location->lng;

                $sql = "SELECT count(*) from `{$_SESSION['database']}`.`locationlist` WHERE `storeid` = '{$storeid}'";
                $result = $db->rawQuery( $sql );
                if( $result ){
                    $sql = "update `locationlist` set `latitude` = '{$latitude}', `longitude` = '{$longitude}' where `storeid` = {$storeid};";
                    $result = $db->rawQuery( $sql );
                    if( count( $result ) == 0 ){
                        http_response_code( 200 );
                        echo json_encode(
                            [
                                'lat' => $latitude,
                                'lng' => $longitude,
                                'updated' => true
                            ]
                        );
                    }else{
                        http_response_code( 400 );
                        echo json_encode(
                            [
                                'message' => 'The location was not updated.'
                            ]
                        );
                    }
                }else{
                    http_response_code( 400 );                   
                    echo json_encode(
                        [
                            'message' => 'The location was not updated.'
                        ]
                    );                    
                }
            }else{
                error_log( print_r( $response, true ) );
                http_response_code( 400 );
                echo json_encode(
                    [
                        'message' => 'Bad response from Google Maps API. Check the location address. If the issue persist, notify IT Department.'
                    ]
                );
            }
        }else{
            http_response_code( 400 );           
            echo json_encode(
                [ 
                    'message' => 'Bad Request. Please notify IT Department.'
                ]
            );
        }
    }catch ( \Throwable $thro ){
        http_response_code( 400 );
        error_log( 'xt_google_lat_lng.php script: ' . print_r( $thro->getMessage(), true ) );
        echo json_encode(
            [
                'message' => 'Error establishing Database Connection.'
            ]
        );

    }
}
?>