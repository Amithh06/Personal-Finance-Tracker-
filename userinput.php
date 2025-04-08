<?php
session_start(); // Start the session
include 'db.php'; // Ensure database connection is included

// Ensure the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['userid']; 

// Fetch user status from the database
$query = "SELECT status FROM users WHERE userid = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Check if the user is active
if ($user['status'] !== 'active') {
    echo "<script>alert('Your account is not active. Please wait for admin approval.'); window.location.href='homepage.php';</script>";
    exit();
}

if (isset($_POST['add'])) {
    // Get the form data
    $expenseName = $_POST['expensename'];
    $amount = $_POST['number'];
    $month = $_POST['month'];
    $year = $_POST['year']; // Ensure the year is included in the form for monthly
    $tableName = "user_expense"; // Construct the table name dynamically for monthly data
    
    // Check if the expense already exists for that month and year, if so, update it, otherwise insert it
    $checkExpenseQuery = "SELECT * FROM $tableName WHERE expense_name = '$expenseName' AND userid = '$user_id'";
    $result = mysqli_query($conn, $checkExpenseQuery);
    
    if (mysqli_num_rows($result) > 0) {
        // Expense already exists, update the value for that month
        $updateQuery = "UPDATE $tableName SET year = '$year', month = '$month' , amount = $amount WHERE expense_name = '$expenseName' AND userid = '$user_id'";
        mysqli_query($conn, $updateQuery);
    } else {
        // Insert new expense
        $insertQuery = "INSERT INTO $tableName (userid, expense_name, year, month, amount) VALUES ('$user_id', '$expenseName', '$year','$month', $amount)";
        mysqli_query($conn, $insertQuery);
    }

    // Redirect after insertion to avoid resubmission on page refresh
    header("Location: userinput.php"); // Adjust the redirect page accordingly
    exit();
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User input</title>
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
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
            animation: gradientShift 10s ease infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        h1 {
            text-shadow:none;
            border-bottom: 2px solid cyan;
            width: 1300px;
            margin: 20px auto;
            padding: 20px;
            margin-top: 40px;
            position: absolute;
            top: 40px;
            font-size: 40px;
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .select {
            width: 161.4px;
            margin: 20px -1px 20px -1px;
            padding: 7.6px;
            border: 2px solid cyan;
            border-radius: 29px;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            display: inline;
            font-weight: bold;
            font-size: 20px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
        }

        .select:hover {
            text-decoration: none;
            color: cyan;
            transform: scale(1.1);
            box-shadow: 0 0 30px rgb(0, 0, 0);
        }

        .viewtc {
            display: flex;
            flex-direction: row;
        }

        #userchart {
            border-bottom-left-radius: 1px;
            border-top-left-radius: 1px;
            color: cyan;
        }

        #usertable {
            border-bottom-right-radius: 1px;
            border-top-right-radius: 1px;
        }

        button {
            padding: 8px 15px;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.5s;
        }

        .btn {
            margin-top: 50px;
        }

        #a {
            margin-right: 25px;
            width: 100px;
        }

        #c {
            margin-left: 25px;
            width: 100px;
        }

        button:hover {
            background: none;
            color: yellow;
            box-shadow: 0px 0px 20px black;
            transform: scale(1.1);
        }

        .input-box {
            display: flex;
            flex-direction: row;
        }

        .ym {
            margin: 20px 350.0px;
        }

        select {
            width: 297.8px;
            padding: 9px;
            border: 2px solid cyan;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            text-align: center;
            display: inline;
            background: transparent;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        select:hover {
            transform: scale(1.05);
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.8);
        }

        option {
            background: rgb(1, 3, 17);
            color: cyan;
            border: none;
        }

        .input-ex input {
            width: 10%;
            padding: 10px;
            background: transparent;
            border: none;
            border-bottom: 2px solid yellow;
            outline: none;
            color: white;
            font-size: 18px;
            transition: all 0.6s ease;
        }

        .input-ex input:focus {
            border-bottom: 2px solid yellow;
            transform: scale(1.05);
        }

        .input-ex label {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }

        #e {
            margin-right: 100px;
        }

        .out {
            width: 50px;
            height: 50px;
            margin-top: 15.4px;
            margin-right: 90vw;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid cyan;
            border-radius: 50%;
            position: absolute;
            top: 20px;
            left: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .out:hover {
            background: rgba(0, 0, 0, 0.8);
            border-color: yellow;
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
        }

        .out a {
            text-decoration: none;
            color: white;
            font-size: 24px;
            transition: 0.4s;
        }

        .out a:hover {
            color: cyan;
            text-shadow: 0px 0px 15px orange;
        }
    </style>

<script>
       // On page load, ensure Yearly is the default view
window.onload = function() {
    toggleSelection("yearly"); // Set the initial view to "yearly"
}

// On page load, ensure Yearly is the default view
window.onload = function() {
    toggleSelection("yearly"); // Set the initial view to "yearly"
}

function toggleSelection(view) {
    if (view === "yearly") {
        // Hide the month dropdown and show the year dropdown
        document.getElementById("mon").style.display = "none";
        document.getElementById("yea").style.display = "block";

        // Set the month value to "null"
        document.getElementsByName("month")[0].value = "em";

        // Reset the form fields
        resetForm();

        // Update the styles
        document.getElementById("usertable").style.color = "cyan";
        document.getElementById("userchart").style.color = "white";
        document.getElementById("yea").style.margin = "20px -110.0px";
        document.getElementById("e").style.width = "18%";
        document.getElementById("r").style.width = "15%";
    } else {
        // Show both year and month dropdowns
        document.getElementById("mon").style.display = "block";
        document.getElementById("yea").style.display = "block";

        // Reset the month value to the default option (e.g., "mon")
        document.getElementsByName("month")[0].value = "em";

        // Update the styles
        document.getElementById("userchart").style.color = "cyan";
        document.getElementById("usertable").style.color = "white";
        document.getElementById("yea").style.margin = "20px 350.0px";
        document.getElementById("e").style.width = "10%";
        document.getElementById("r").style.width = "10%";
    }
}

// Reset the form fields when switching to Yearly view
function resetForm() {
    // Clear the expense name and amount fields
    document.getElementById("expenseForm").reset();

    // Set the year dropdown to the first option (2025)
    document.getElementsByName("year")[0].value = "2025";

    // Ensure the month dropdown shows "mon"
}


        function clearForm() {
            document.getElementById("expenseForm").reset();
        }
    </script>
</head>
<body>
    
<div class="out">
        <a href="homepage.php"><-</a>
</div>

<h1>Add Expense</h1>

<div class="viewtc">
            <button class="select" id="usertable" name="year" onclick="toggleSelection('yearly')">Yearly</button>
            <button class="select" id="userchart" name="month" onclick="toggleSelection('monthly')">Monthly</button>
        </div> 

<form id="expenseForm" action="userinput.php" method="POST">


    <div class="input-box">
        <div class="ym" id="yea">
            <select name="year" required>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                <option value="2023">2023</option>
                <option value="2022">2022</option>
                <option value="2021">2021</option>
                <option value="2020">2020</option>
            </select>
        </div>

        <div class="ym" id="mon">
            <select name="month" >
            <option value="em">month</option>
                <option value="Jan">Jan</option>
                <option value="Feb">Feb</option>
                <option value="Mar">Mar</option>
                <option value="Apr">Apr</option>
                <option value="May">May</option>
                <option value="Jun">Jun</option>
                <option value="Jul">Jul</option>
                <option value="Aug">Aug</option>
                <option value="Sep">Sep</option>
                <option value="Oct">Oct</option>
                <option value="Nov">Nov</option>
                <option value="Dec">Dec</option>
            </select>
        </div>
    </div>

    <div class="input-ex">
        <label>Expense Name</label>
        <input id="e" type="text" name="expensename" required placeholder="Home Tax">

        <label>Rs.</label>
        <input id="r" type="number" name="number" required placeholder="10000">
    </div>

    <div class="btn">
        <button id="a" name="add" type="submit">Add</button>
        <button id="c" type="button" onclick="clearForm()">Clear</button>
    </div>
</form>
    
</body>
</html>

