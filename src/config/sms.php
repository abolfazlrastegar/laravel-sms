<?php
return [

    /*
     |--------------------------------------
     | set default sms
     |--------------------------------------
     | form 'Kavenegar', 'Smsir', 'Melipayamak'
     */
    'default' => 'Kavenegar',

    /*
     |-------------------------------------
     | set setting driver sms
     |------------------------------------
     */
    'drivers' => [
        'Kavenegar' => [
            'key' => '',
            'type' => 'sms' // set call and sms
        ],
        'Smsir' => [
            'key' => 'PN1TVeBeaAehFLJAKU4XdfpsFXsQguYfleO0bV4ceh6diTZid2hRXza3uSkBbDef'
        ],
        'Melipayamak' => [
            'key' => ''
        ]
    ]
];
