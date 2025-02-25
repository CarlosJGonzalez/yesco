<?php
    session_start();
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
    error_reporting( 0 );
    if( $_POST ){
        include $_SERVER['DOCUMENT_ROOT'].'/includes/connect.php';
        $store_id       = (int)$_POST['store_id'];
        $photo_index    = (int)$_POST['photo_index'];
        try{
            $sqlStr = sprintf( "select `yext_photos` from `locationlist` where `storeid` = %d;", $store_id );
            $gallery_arr = $db->rawQuery( $sqlStr );
        }catch( ErrorException $ee ){
            print_r( $ee->getMessage() );
        }

        $status = false;
        if( $gallery_arr && is_countable( $gallery_arr ) && count( $gallery_arr ) > 0 ){
            $photos = str_replace( [ "[", "]" ], "", $gallery_arr[0]['yext_photos'] );
            $photos = explode( ",", $photos );
            $result = array_filter( $photos, function( $photo, $index ) use( $photo_index ) {
                if( $index != $photo_index ){
                    return true;
                }
            }, ARRAY_FILTER_USE_BOTH );

            $photos_str = implode( ",", $result );
            try{            
                try{
                    $status = $db->where( 'storeid', $store_id )->update( 'locationlist', ['yext_photos' => "[{$photos_str}]"] );
                }catch( ErrorException $ee ){
                    $status = false;
                }
            }catch( Throwable $thro ){
                $status = false;
                error_log( 
                    print_r( $thro->getMessage(), true ) 
                ) ;
            }            
        }
        
        $db->disconnect();

        if( $status ){
            http_response_code( 200 );
            echo json_encode ([
                'message' => 'Success'
            ]);
        }else{
            http_response_code( 400 );
            echo json_encode ([
                'message' => 'Failure. The image was not deleted.'
            ]);            
        }
    }
