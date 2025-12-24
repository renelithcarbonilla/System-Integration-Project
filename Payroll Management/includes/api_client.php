<?php
function callAPI($method, $url, $data = false) {
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;

        case "GET":
            if ($data) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            break;
    }

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ]
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}
