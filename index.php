<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }
        .form-container {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 350px;
        }
        .form-container h2 {
            margin-bottom: 1rem;
            color: #333;
            text-align: center;
        }
        .form-container p {
            text-align: center;
            color: #ff0000; /* Adjust the color as needed */
            margin-bottom: 1rem;
            font-weight: bold;
        }
        .form-container input {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container input:focus {
            border-color: #007bff;
            outline: none;
        }
        .form-container button {
            width: 100%;
            padding: 1rem;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #ffffff;
            font-size: 1rem;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: green;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Transfer money</h2>
        <!-- <p>Registration cost: GHS 20</p> -->
        <form id="paymentForm" method="post" action="paystack_handler.php">
            <input type="text" placeholder="Firstname" id="first-name" name="first-name" required>
            <input type="text" placeholder="Lastname" id="last-name" name="last-name" required>
            <input type="email" id="email-address" name="email-address" placeholder="Email" required>
            <input type="number" id="amount" name="amount" placeholder="Payment Amount" required>
            <button type="submit">Submit</button>
        </form>
    </div>


        

        <script src="https://js.paystack.co/v1/inline.js"></script>

        <!-- Ensure your script.js or any other script is loaded after the form -->
        <!-- <script src="script.js"></script> -->
     
</body>
</html>
