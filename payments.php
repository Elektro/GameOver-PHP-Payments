<?php

$shared_secret = '#_YOUR_SHARED_SECRET_#';
$request = parse_signed_request($_GET['signed_request'], $shared_secret);

$request_type = $request['method'];
$response = '';

if ($request_type == 'payments_get_items') {
    
    // Supply the correct information for the package
    // Expected parameters: title, description, price, image_url
    $item = array(
      'title' => 'Title of the package',
      'description' => 'Use these coins to milk faster...',
      'price' => '199',
      'image_url' => 'http://www.iconshock.com/img_jpg/CLEAN/accounting/jpg/256/coins_icon.jpg',
    );
    
    // Construct response.
    $response = array(
        'content' => array(0 => $item),
        'method' => $request_type,
    );
    
    // Response must be JSON encoded.
    $response = json_encode($response);
    
    // Send response.
    echo $response;
    
    //kthxbye
    exit;
    
} elseif ($request_type == 'payments_status_update') {
    
    // 
    
    // Get the specific details for this order
    $order_details_status = $request['status'];
    $order_details_id = $request['order_id'];
    
    if ($order_details_status == 'placed') {
        
        // The payment has been succesfully handled on our side
        $new_status = 'settled';
        
    } elseif ($order_details_status == 'failed') {
        
        // The payment has been unsuccesfully handled on our side
        $new_status = 'failed';
        
    }
    
    // Construct response.
    $response = array(
        'content' => array(
            // Confirm that the payment has failed
            'status' => $new_status,
            // Relay the orderid
            'order_id' => $order_details_id,
        ), 'method' => $request_type,
    );
    
    // Response must be JSON encoded.
        $response = json_encode($response);
        
    // Send response.
    echo $response;
    
    //kthxbye
    exit;
}

// Parse the signed request
function parse_signed_request($signed_request, $secret) {
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);
    
    // decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);
    
    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }
    
    // check sig
    $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }
    
        return $data;
    }
    
    function base64_url_decode($input) {
    return base64_decode(strtr($input, '-_', '+/'));
}

?>
