<?php
// Include database connection
include_once("db_connect.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = $_POST['email-address'];
    $firstname = $_POST['first-name'];
    $lastname = $_POST['last-name'];
    $amount = $_POST['amount'] * 100; // Convert amount to kobo (100 kobo = 1 GHS)
    $currency = "GHS";
    $reference = uniqid(); // Generate a unique reference
    
    // Check if payment already exists in database for this email
    $query = "SELECT * FROM transaction WHERE client_email = '$email' AND status = 'success'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Payment already verified, redirect to success page
        header('Location: payment_success.php');
        exit;
    } else {
        // Initialize Paystack API request
        $paystack_key = "sk_test_3c6d17caf3ee0491f698a8a3f53e9fa3cc85b2a8"; // Replace with your Paystack secret key
        $url = "https://api.paystack.co/transaction/initialize";
        
        // Set up data for Paystack API request
        $callback_url = "http://localhost/paystackapi/payment_success.php?reference=" . $reference;
        $data = [
            'email' => $email,
            'amount' => $amount,
            'currency' => $currency,
            'reference' => $reference,
            'callback_url' => $callback_url,
            'metadata' => json_encode([
                'first_name' => $firstname,
                'last_name' => $lastname
            ])
        ];
        
        // Use cURL to send request to Paystack API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $paystack_key",
            "Content-Type: application/x-www-form-urlencoded",
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        if ($response) {
            $result = json_decode($response, true);
            if ($result && isset($result['data']['authorization_url'])) {
                // Redirect to Paystack payment page
                header("Location: " . $result['data']['authorization_url']);
                exit();
            } else {
                // Handle error
                echo "Error: Unable to initialize payment.";
            }
        } else {
            // Handle cURL error
            echo "Error: cURL request failed.";
        }
    }
} else {
    echo "Error: Invalid request method.";
}
?>
