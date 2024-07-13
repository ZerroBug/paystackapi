<?php
include_once("db_connect.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check to be sure that it's a POST method
if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
    exit();
}

$paymentDetails = @file_get_contents("php://input");
$headers = getallheaders();
$headers = json_encode($headers);

// Append the payment details and headers to the respective files with new lines
file_put_contents("paystack_webhook_status.html", "<pre>" . $paymentDetails . "</pre>" . PHP_EOL, FILE_APPEND);
file_put_contents("file2.html", "<pre>" . $headers . "</pre>" . PHP_EOL, FILE_APPEND);

define('PAYSTACK_SECRET_KEY', '');

// Verify the Paystack signature
$signature = hash_hmac('sha512', $paymentDetails, PAYSTACK_SECRET_KEY);
if (!isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE']) || !hash_equals($signature, $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'])) {
    exit();
}

http_response_code(200);

// Parse the event
$event = json_decode($paymentDetails);


// Ensure event is charge.success
if ($event->event !== 'charge.success') {
    exit('Event is not charge.success');
}

// Handle the transaction based on the status
$trans_id = $event->data->id;
$reference = $event->data->reference;
$amount = $event->data->amount / 100;
$status = $event->data->status;
$first_name = $event->data->metadata->first_name;
$last_name = $event->data->metadata->last_name;
$client_name = $first_name . " " . $last_name;
$customer_email = $event->data->customer->email;
$customer_code = $event->data->customer->customer_code;

date_default_timezone_set('Africa/Accra');
$trans_date_time = date('m/d/Y h:i:s a', time());

$stmt = $conn->prepare("INSERT INTO transaction(trans_id, reference_id, client_email, client_name, amount, status, trans_date )
                        VALUES(?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('issssss', $trans_id, $reference, $customer_email, $client_name, $amount, $status, $trans_date_time);


if ($stmt->execute()) {
    // Log success for debugging purposes
    file_put_contents("success_log.html", "<pre>Transaction successful: Reference = " . htmlspecialchars($reference) . "</pre>" . PHP_EOL, FILE_APPEND);
} else {
    // Handle error (e.g., log error, send notification, etc.)
    file_put_contents("db_errors.log", "Database insert failed: " . htmlspecialchars($stmt->error) . PHP_EOL, FILE_APPEND);
    http_response_code(500); // Internal Server Error
}




$stmt->close();
$conn->close();


?>
