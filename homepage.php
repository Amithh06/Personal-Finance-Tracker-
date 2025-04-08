<?php
session_start();
if (!isset($_SESSION['userid'])) {
    echo "<script>alert('Please log in first'); window.location.href='login.php';</script>";
    exit();
}

// Include database connection
include 'db.php';

// Fetch user details from the database
$userid = $_SESSION['userid'];
$query = "SELECT userid, email, phone_number FROM users WHERE userid = '$userid'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

$userDetails = mysqli_fetch_assoc($result);

// Assign fetched details to variables
$username = $userDetails['userid']; // Fetch username from the database
$email = $userDetails['email'];
$phone = $userDetails['phone_number'];

$firstLetter = strtoupper(substr($username, 0, 1)); // Get the first letter of the username
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: rgb(2,0,36);
            background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 38%, rgba(0,212,255,1) 100%);
            color: white;
            text-align: center;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            padding-bottom: 30px;
        }

        /* Slide-In Animation for h1 */
        h1 {
            border-bottom: 2px solid cyan;
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            margin-top: 40px;
            font-size: 32px;
            color: yellow;
            animation: slideIn 1s ease-in-out; /* Slide-in animation */
        }

        @keyframes slideIn {
            from {
                transform: translateX(-100%); /* Start off-screen to the left */
                opacity: 0;
            }
            to {
                transform: translateX(0); /* Slide into the center */
                opacity: 1;
            }
        }

        /* Profile Logo */
        .profile-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(45deg, rgb(100, 90, 35), rgb(255, 251, 0));
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 30px;
            font-weight: bold;
            color: white;
            cursor: pointer;
            transition: 0.6s;
            position: absolute;
            top: 30px;
            right: 40px;
            box-shadow: 0 0 15px rgba(255, 111, 97, 0.7);
        }

        .profile-logo:hover {
            transform: scale(1.1);
            box-shadow: 0 0 30px rgba(22, 18, 17, 0.9);
        }

        /* Profile Section (Right Side) */
        .profile-section {
            position: fixed;
            top: 0;
            right: -300px; /* Hidden by default */
            width: 250px;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.5);
            transition: right 0.4s ease-in-out;
            z-index: 999;
            padding: 20px;
            text-align: center;
        }

        .profile-section.show-profile {
            right: 0; /* Slide in */
        }

        .profile-section h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: yellow;
        }

        .profile-section p {
            font-size: 16px;
            margin-bottom: 15px;
        }

        .profile-section button {
            padding: 10px 20px;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.6s;
            border-radius: 5px;
        }

        .profile-section button:hover {
            background: cyan;
            color: black;
            box-shadow: 0 0 20px cyan;
        }

        /* User Input and View Table Sections */
        .it {
            display: flex;
            flex-direction: row;
            justify-content: center;
            gap: 20px;
            margin-top: 50px;
        }

        .user {
            width: 250px;
            padding: 20px;
            border: 2px solid cyan;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            transition: transform 0.4s ease-in-out;
        }

        .user:hover {
            transform: scale(1.05);
        }

        .user a {
            font-weight: bold;
            font-size: 20px;
            text-decoration: none;
            color: white;
            transition: color 0.3s ease;
        }

        .user a:hover {
            color: yellow;
        }

        /* Brief View Section */
        .briefview {
            width: 300px;
            padding: 20px;
            border: 2px solid cyan;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            margin: 20px auto;
            transition: transform 0.4s ease-in-out;
        }

        .briefview:hover {
            transform: scale(1.05);
        }

        .briefview button {
            font-weight: bold;
            font-size: 20px;
            background: transparent;
            color: white;
            border: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .briefview button:hover {
            color: yellow;
        }

        /* About Section */
        .about-section {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            border-top: 2px solid cyan;
            text-align: center;
        }

        .about-section h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: cyan;
        }

        .about-section p {
            font-size: 16px;
        }

        /* Logout Button */
        .button {
            padding: 10px 20px;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.6s;
            position: absolute;
            bottom: 20px;
            right: 40px;
            border-radius: 5px;
        }

        .button:hover {
            background: cyan;
            color: black;
            box-shadow: 0 0 20px cyan;
        }

        /* Quick Look Table */
        .quicklook-table {
            display: none; /* Hidden by default */
            margin-top: 30px;
            padding: 15px;
            border: 2px solid cyan;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            width: 80%;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid cyan;
        }

        /* Responsive Design */
@media screen and (max-width: 768px) {
    h1 {
        font-size: 24px;
        width: 90%;
    }

    .profile-logo {
        width: 50px;
        height: 50px;
        font-size: 25px;
        top: 20px;
        right: 20px;
    }

    .profile-section {
        width: 80%;
    }

    .user {
        width: 200px;
        padding: 15px;
    }

    .briefview {
        width: 90%;
    }

    .about-section {
        width: 90%;
        padding: 10px;
    }

    .quicklook-table {
        width: 90%;
    }

    .button {
        bottom: 20px;
        right: 20px;
        padding: 8px 15px;
    }

    table th, table td {
        font-size: 14px;
        padding: 8px;
    }
}

@media screen and (max-width: 480px) {
    h1 {
        font-size: 18px;
        margin-top: 20px;
    }

    .profile-logo {
        width: 40px;
        height: 40px;
        font-size: 20px;
        top: 15px;
        right: 15px;
    }

    .user {
        width: 150px;
        padding: 10px;
    }

    .briefview {
        width: 100%;
    }

    .about-section h2 {
        font-size: 20px;
    }

    .about-section p {
        font-size: 14px;
    }

    .quicklook-table {
        font-size: 12px;
    }

    .button {
        padding: 6px 10px;
    }
}

    </style>
</head>
<body>
    <h1>Welcome, <?php echo $username; ?>!</h1>

    <!-- Profile Logo with Click Event -->
    <div class="profile-logo" onclick="toggleProfile()">
        <?php echo $firstLetter; ?>
    </div>

    <!-- Profile Section (Right Side) -->
    <div class="profile-section" id="profile">
        <h2>Your Profile</h2>
        <p><b>Name:</b> <?php echo $username; ?></p>
        <p><b>Email:</b> <?php echo $email; ?></p>
        <p><b>Phone:</b> <?php echo $phone; ?></p>
        <button onclick="toggleProfile()">Close</button>
    </div>

    <!-- User Input and View Table Sections -->
    <div class="it">
        <div class="user" id="user_input">
            <a href="userinput.php">Add Expense</a>
        </div>
        <div class="user" id="vtable">
            <a href="vtableandchart.php">View Table and Chart</a>
        </div>
    </div>

    <!-- Brief View Section -->
    <div class="briefview">
        <button onclick="toggleQuickLook()">Quick Look</button>
    </div>
 

    <!-- Logout Button -->
    <button class="button" onclick="window.location.href='login.php'" >Logout</button>

   
    <?php

include 'db.php'; // Include database connection


$userid = $_SESSION['userid'];
$username = $_SESSION['username'];
$firstLetter = strtoupper(substr($username, 0, 1));

// Fetch user amount from the `users` table
$userQuery = "SELECT amount FROM users WHERE userid = '$userid'";
$userResult = mysqli_query($conn, $userQuery);
$userRow = mysqli_fetch_assoc($userResult);
$userAmount = $userRow['amount'];

// Fetch total amount from `user_details` table for the same userid
$totalQuery = "SELECT SUM(amount) AS total_expense FROM user_expense WHERE userid = '$userid'";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalExpense = $totalRow['total_expense'];

// Calculate savings
$savings = $userAmount - $totalExpense;
?>
<!-- Quick Look Table -->
<div class="quicklook-table" id="quicklook-table">
        <h2>Quick Look Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>User Amount</th>
                    <th>Total Expenses</th>
                    <th>Savings</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo number_format($userAmount, 2); ?></td>
                    <td><?php echo number_format($totalExpense, 2); ?></td>
                    <td><?php echo number_format($savings, 2); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
     <!-- About Section -->
     <div class="about-section">
        <h2>About Us</h2>
        <p>Welcome to our platform. We are here to help you manage your finances better.</p>
    </div>

    <!-- Logout Button -->
    <button class="button" onclick="window.location.href='login.php'">Logout</button>

    <script>
        function toggleProfile() {
            const profile = document.getElementById("profile");
            profile.classList.toggle("show-profile");
        }

        function toggleQuickLook() {
            const table = document.getElementById('quicklook-table');
            table.style.display = table.style.display === 'none' ? 'block' : 'none';
        }
    </script>

</body>
</html>
