<?php
include("config/db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$requested_role = $_POST['requested_role'];

$stmt = $conn->prepare("
    INSERT INTO users (username, name, password, role, status)
    VALUES (?, ?, ?, ?, 'pending')
");

$stmt->bind_param("ssss", $username, $name, $password, $requested_role);
$stmt->execute();


    echo "Registered. Wait for admin approval.";
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registration</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #0f1f3a;
    color: white;
}

.form-container {
    max-width: 500px;
    margin: 100px auto;
    background: #1d2545;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}

h2 {
    margin-bottom: 30px;
    font-size: 26px;
}

input {
    width: 100%;
    padding: 14px;
    margin-bottom: 20px;
    border-radius: 10px;
    border: none;
    background: #2a315a;
    color: white;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

input:focus {
    background: #323a6a;
}

button {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: none;
    background: #f5a25d;
    color: #1d2545;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #ffb870;
}

.success {
    margin-top: 15px;
    padding: 12px;
    background: #2a315a;
    color: #7CFFB2;
    border-radius: 10px;
    text-align: center;
}

.login-link {
    margin-top: 25px;
    text-align: center;
    font-size: 14px;
}

.login-link a {
    color: #f5a25d;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.login-link a:hover {
    color: #ffb870;
}
</style>
</head>

<body>

<div class="form-container">
    <h2>Student Registration</h2>

    <form method="POST">
        <input type="text" name="username" placeholder="Admission No" required>
        <input type="text" name="name" placeholder="Full Name" required>
        <select name="requested_role" required>
    <option value="">Select Position</option>
    <option value="class_rep">Class Representative</option>
    <option value="execom">Execom Member</option>
</select>

        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Register</button>
    </form>

    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="login-link">
        Already registered? <a href="login.php">Login here</a>
    </div>
</div>

</body>
</html>
