<?php
session_start();
include("config/db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username=? AND status='approved'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        }
    }

    $error = "Invalid credentials or not approved.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    height: 100vh;
    font-family: 'Segoe UI', sans-serif;
    background: #0f1f3a;
    display: flex;
    justify-content: center;
    align-items: center;
}

.wrapper {
    width: 900px;
    /*height: 550px;*/
    display: flex;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0,0,0,0.6);
}

.left {
    width: 50%;
    background: #1d2545;
    padding: 60px;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.left h2 {
    margin-bottom: 10px;
    font-size: 28px;
}

.left p {
    margin-bottom: 30px;
    opacity: 0.8;
}

.input-group {
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 14px;
    border-radius: 8px;
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
    border-radius: 8px;
    border: none;
    background: #f5a25d;
    color: #1d2545;
    font-weight: bold;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
}


.error {
    margin-top: 15px;
    color: #ff7a7a;
}

.right {
    width: 50%;
    background: url('https://images.unsplash.com/photo-1501785888041-af3ef285b470') center/cover no-repeat;
}
.register-link {
    margin-top: 20px;
    font-size: 14px;
    opacity: 0.8;
}

.register-link a {
    color: #f5a25d;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.register-link a:hover {
    color: #ffb870;
}
/* ================= MOBILE FIX ================= */

@media (max-width: 768px) {

    body {
        height: 100vh;
        padding: 20px;
    }

    .wrapper {
        width: 100%;
        height: auto;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .right {
        display: none;
    }

    .left {
        width: 100%;
        max-width: 400px;
        padding: 40px 25px;
        border-radius: 16px;
    }

    .register-link {
        margin-top: 20px;
        text-align: center;
    }
}

@media (max-width: 480px) {

    .left h2 {
        font-size: 22px;
    }

    input {
        padding: 12px;
        font-size: 14px;
    }

    button {
        padding: 12px;
        font-size: 14px;
    }
}

</style>
</head>

<body>

<div class="wrapper">

    <div class="left">
        <h2>Welcome Back</h2>
        <p>Login to continue to Duty Leave Portal</p>

        <form method="POST">

            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">Login</button>
            <div class="register-link">
    New student? <a href="register.php">Register here</a>
</div>


            <?php if(isset($error)): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>

        </form>
    </div>

    <div class="right"></div>

</div>

</body>
</html>
