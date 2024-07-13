<?php
include_once("db_connect.php");
include_once("config.php");
$ref = $_GET["reference"];
if($ref == ""){
    header("location:javascript://history.go(-1)");
    exit;
}else{
    $curl = curl_init();
  
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.paystack.co/transaction/verify/".rawurlencode($ref),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer $test_api_secret_key",
        "Cache-Control: no-cache",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
  
    curl_close($curl);
    
    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      // echo"Your response is" . $response;
      // Decode json response to objects
      $results = json_decode($response);

      // check if verified success
      if($results->data->status == "success"){
        // Get data from the transaction
        $trans_id = $results->data->id;
        $status = $results->data->status;
        $reference_id = $results->data->reference;
        $fname = $results->data->customer->first_name;
        $lname = $results->data->customer->last_name;
        $client_name = $fname . " " . $lname;
        $client_email = $results->data->customer->email;
        date_default_timezone_set('Africa/Accra');
        $trans_date_time = date('m/d/Y h:i:s a', time());

        // Inserting data into database
        $stmt = $conn->prepare("INSERT INTO transaction(trans_id, reference_id, client_email, client_name, status, trans_date )
        VALUES(?,?,?,?,?,?)
        ");

        $stmt->bind_param('iissss', $trans_id,$reference_id, $client_email, $client_name, $status, $trans_date_time );
        $stmt->execute();
        if(!$stmt){
          echo "There was a problem " . mysqli_error($conn);
        }else{
          header("location:https://c918-154-161-19-224.ngrok-free.app/paystackapi/payment_success.php?status=success");
        }
        // echo $trans_id."  ". $reference_id . "  ". $cus_email ."  ". $fullname . "  ". $status ." ".$trans_date_time;
    } else {
        header("Location: error.html");
        exit();
    }
    
    }
  
}