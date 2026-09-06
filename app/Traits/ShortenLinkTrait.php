<?php

namespace App\Traits;

trait ShortenLinkTrait {
    public function get_link($parcel_no) {
        return '';
        $data['url'] = url('/track/'.$parcel_no);
        $data['type'] = 'splash';
        $data['parameters'] = '[
                                {
                                    "name": "aff",
                                    "value": "3"
                                },
                                {
                                    "device": "gtm_source",
                                    "link": "api"
                                }
                            ]';

        $curl = \curl_init();

        \curl_setopt_array($curl, array(
            CURLOPT_URL                 => "https://sg7.sbs/api/url/add",
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_ENCODING            => "",
            CURLOPT_MAXREDIRS           => 2,
            CURLOPT_TIMEOUT             => 10,
            CURLOPT_FOLLOWLOCATION      => true,
            CURLOPT_CUSTOMREQUEST       => "POST",
            CURLOPT_HTTPHEADER          => array(
                "Authorization: Bearer gjMhFjGmLXsafyZ2",
                "Content-Type: application/json",
            ),
            CURLOPT_POSTFIELDS          => json_encode($data),
        ));

        $response = \curl_exec($curl);

        \curl_close($curl);

        $response = json_decode($response);

        if ($response->error == 0) :
            return $response->shorturl;
        else:
            return '';
        endif;

    }
}
