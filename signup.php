<?php
session_start(); // Start the session at the top before any HTML code or output

include 'db.php'; // Include the database connection file

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF token validity
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>alert('Invalid CSRF token'); window.location.href='signup.php';</script>";
        exit();
    }

    // Sanitize and validate inputs
    $full_name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $userid = isset($_POST['userid']) ? trim($_POST['userid']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $password = isset($_POST['new_password']) ? trim($_POST['new_password']) : null;
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : null;

    // Validate input fields
    if (!empty($full_name) && !empty($userid) && !empty($email) && !empty($phone) && !empty($password) && !empty($amount)) {
        // Ensure phone number is exactly 10 digits
        if (strlen($phone) != 10 || !preg_match('/^[0-9]{10}$/', $phone)) {
            echo "<script>alert('Phone number must be exactly 10 digits!'); window.location.href='signup.php';</script>";
            exit();
        }

        // Check if the username already exists in the database
        $stmt = $conn->prepare("SELECT userid FROM users WHERE userid = ?");
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo "<script>alert('User ID is already taken. Please choose another.'); window.location.href='signup.php';</script>";
            $stmt->close();
            exit();
        }
        $stmt->close();

        // Check if the email already exists in the database
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            echo "<script>alert('Email is already registered. Please use another email.'); window.location.href='signup.php';</script>";
            $stmt->close();
            exit();
        }
        $stmt->close();

        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (userid, full_name, email, phone_number, password, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $userid, $full_name, $email, $phone, $hashed_password, $amount);

        if ($stmt->execute()) {
            echo "<script>alert('Signup successful!'); window.location.href='login.php';</script>";
            exit();
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='signup.php';</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('All fields are required!'); window.location.href='signup.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="styles.css">
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
            overflow: hidden;
            position: relative;
        }
        
        .login-box {
            width: 350px;
        
            background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 38%, rgba(0,212,255,1) 100%);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 255, 255, 0.2);
            text-align: center;
        }
        
        .input-box {
            position: relative;
            margin: 20px 0;
        }
        
        .input-box input {
            width: 100%;
            padding: 10px;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(0, 255, 255, 0.5);
            outline: none;
            color: white;
            font-size: 18px;
        }
        
        .input-box label {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            transition: 0.3s;
            color: rgba(255, 255, 255, 0.5);
        }
        
        .input-box input:focus ~ label,
        .input-box input:valid ~ label {
            top: 0;
            font-size: 14px;
            color: orange;
            text-shadow: 0px 0px 10px orange, 0px 0px 20px rgba(0, 255, 255, 0.7);
        }
        
        button {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            font-weight: 600;
            background: none;
            border: 2px solid cyan;
            color: white;
            cursor: pointer;
            transition: 0.7s;
            position: relative;
            overflow: hidden;
        }
        
        button::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: yellow;
            transition: 0.3s;
            z-index: -1;
        }
        
        button:hover {
            color: yellow;
            box-shadow: 0px 0px 20px cyan;
        }

        .register-link a {
            color: yellow;
            text-decoration: none;
            transition: 0.3s;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Sign Up</h2>
            <form action="signup.php" method="POST" onsubmit="return validatePassword()">
                <div class="input-box">
                    <input type="text" name="name" required>
                    <label>Full Name</label>
                </div>
                <div class="input-box">
                    <input type="text" name="userid" required>
                    <label>User ID</label>
                </div>
                <div class="input-box">
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <input type="text" name="phone" id="phone" required pattern="\d{10}" maxlength="10" title="Phone number must be 10 digits" oninput="validatePhoneNumber()">
                    <label>Phone Number</label>
                </div>
                <div class="input-box">
                    <input type="password" name="new_password" id="new_password" required>
                    <label>New Password</label>
                    <small id="password_error" style="color: red; display: none;">Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character, and be at least 8 characters long.</small>
                </div>
                <div class="input-box">
                    <input type="number" name="amount" required>
                    <label>Net Income</label>
                </div>
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <button type="submit">Submit</button>
            </form>
            <div class="register-link">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </div>
    </div>

    <script>
        // Optional: JS validation for phone number
        function validatePhoneNumber() {
            let phone = document.getElementById('phone').value;
            if (phone.length === 10) {
                document.getElementById('phone').setCustomValidity('');
            } else {
                document.getElementById('phone').setCustomValidity('Phone number must be exactly 10 digits.');
            }
        }

        // JS Password Validation
        function validatePassword() {
            const password = document.getElementById('new_password').value;
            const passwordError = document.getElementById('password_error');
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

            if (!passwordPattern.test(password)) {
                passwordError.style.display = 'block';
                return false;  // Prevent form submission
            }

            passwordError.style.display = 'none';
            return true;  // Allow form submission
        }
    </script>
</body>
</html>
