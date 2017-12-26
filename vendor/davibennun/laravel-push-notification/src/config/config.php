<?php

return array(

    'Datelux'     => array(
        'environment' =>'development',
        'certificate' =>public_path()."/pem/datelux.pem",
        'passPhrase'  =>'datelux',
        'service'     =>'Apns'
    )
    // ,
    // 'datelux' => array(
    //     'environment' =>'production',
    //     'apiKey'      =>'yourAPIKey',
    //     'service'     =>'gcm'
    // )

);