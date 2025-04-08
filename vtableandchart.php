<?php

session_start(); // Start the session
include 'db.php'; // Ensure the database connection is included

// Ensure the user is logged in and has admin privileges (if applicable)
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details for both yearly and monthly data
$yearlyQuery = "SELECT * FROM user_expense WHERE month='em'"; // For yearly data (month is NULL)
$monthlyQuery = "SELECT * FROM user_expense WHERE month!='em'"; // For monthly data (month is not NULL)

$yearlyResult = mysqli_query($conn, $yearlyQuery);
$monthlyResult = mysqli_query($conn, $monthlyQuery);

// Handle delete operation
if (isset($_GET['delete'])) {
    $expenseName = $_GET['expense_name']; // Get the expense_name from the URL
    $userId = $_SESSION['userid']; // Get the user ID from session
    
    // Delete the row based on user ID and expense name
    $deleteQuery = "DELETE FROM user_details WHERE userid = '$userId' AND expense_name = '$expenseName'";
    mysqli_query($conn, $deleteQuery);
    
    // Redirect after deletion to avoid resubmission
    header("Location: vtableandchart.php"); // Adjust the page accordingly
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        min-height: 100vh;
    }

    h1 {
        text-shadow: none;
        border-bottom: 2px solid cyan;
        width: 100%;
        max-width: 1200px;
        margin-bottom: 20px;
        padding: 20px;
        text-align: center;
        font-size: 2rem;
    }

    .viewtc {
        display: flex;
        justify-content: center;
        gap: 20px; /* Space between buttons */
        margin-bottom: 30px;
    }

    .select {
        padding: 10px 20px;
        border: 2px solid cyan;
        border-radius: 30px;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .select:hover {
        transform: scale(1.1); /* Slightly enlarge on hover */
    }

    .select a {
        font-weight: bold;
        font-size: 1.2rem;
        text-decoration: none;
        color: white;
    }

    .select a:hover {
        color: orange;
    }

    table {
        width: 100%;
        max-width: 1000px;
        border-collapse: collapse;
        margin: 20px 0;
        box-shadow: 0px 0px 20px rgba(0, 255, 255, 0.5);
        overflow: hidden;
        border: 2px solid cyan;
    }

    th, td {
        padding: 15px;
        text-align: center;
        border: 2px solid rgba(0, 255, 255, 0.5);
        transition: background-color 0.3s ease;
    }

    th {
        background: rgba(0, 0, 0, 0.8);
        color: yellow;
    }

    tr:hover td {
        background-color: rgba(0, 255, 255, 0.1); /* Highlight row on hover */
    }

    button {
        padding: 8px 15px;
        background: none;
        border: 2px solid cyan;
        color: white;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background: cyan;
        color: black;
        box-shadow: 0px 0px 20px cyan;
    }

    .out {
        position: fixed; /* Fixed position for the back button */
        top: 20px;
        left: 20px;
        padding: 10px 15px;
        background: black;
        border: 2px solid cyan;
        border-radius: 50px;
        text-align: center;
    }

    .out a {
        text-decoration: none;
        color: white;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .out a:hover {
        color: cyan;
        text-shadow: 0px 0px 15px orange;
    }

    .content-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 1200px;
    }

    .content-section > div {
        margin-bottom: 40px; /* Space between tables */
    }

    h2 {
        margin-bottom: 15px;
        font-size: 1.5rem;
        text-align: center;
        color: #fff;
        border-bottom: 2px solid cyan;
        padding-bottom: 5px;
    }

    .end {
        margin-top: 20px;
        text-align: center;
        color: yellow;
    }
</style>

</head>
<body>
    <div class="out">
        <a href="homepage.php"><-</a>
    </div>

    <h1>Table</h1>
   
    <div class="viewtc">
        <div class="select" id="usertable">
            <a href="vtableandchart.php">Table</a>
        </div>
   
        <div class="select" id="userchart">
            <a href="chart.php">Chart</a>
        </div>
    </div>
<div class="y">
    <!-- Yearly Table (Month is NULL) -->
    <h2>Yearly Expenses</h2>
    <table>
        <tr>
            <th>Expense Name</th>
            <th>Year</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($yearlyResult)) { ?>
            <tr>
                <td><?php echo $row['expense_name']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td>
                    <!-- Delete Button -->
                    <a href="?delete=true&expense_name=<?php echo $row['expense_name']; ?>" onclick="return confirm('Are you sure you want to delete this expense?');">
                        <button>Delete</button>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>

    <div class="m">
    <!-- Monthly Table (Month is NOT NULL) -->
    <h2>Monthly Expenses</h2>
    <table>
        <tr>
            <th>Expense Name</th>
            <th>Month</th>
            <th>Year</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($monthlyResult)) { ?>
            <tr>
                <td><?php echo $row['expense_name']; ?></td>
                <td><?php echo $row['month']; ?></td>
                <td><?php echo $row['year']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td>
                    <!-- Delete Button -->
                    <a href="?delete=true&expense_name=<?php echo $row['expense_name']; ?>" onclick="return confirm('Are you sure you want to delete this expense?');">
                        <button>Delete</button>
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
    </div>
    <div class="end"><h3>END</h3></div>

</body>
</html>
