<?php
include("config/db.php");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $username = $_POST['username'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $requested_role = $_POST['requested_role'];

    $club = isset($_POST['club']) ? $_POST['club'] : NULL;
    $class = isset($_POST['class_name']) ? $_POST['class_name'] : NULL;

    $stmt = $conn->prepare("
        INSERT INTO users (username, name, password, role, status, club, class_name)
        VALUES (?, ?, ?, ?, 'pending', ?, ?)
    ");

    $stmt->bind_param("ssssss", $username, $name, $password, $requested_role, $club, $class);
    $stmt->execute();

    $success = "Registered. Wait for admin approval.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Registration</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top left, #162a4a, #0f1f3a);
    color: white;

    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}


.form-container {
    width: 100%;
    max-width: 520px;
    background: #1a2b4c;
    padding: 45px;
    border-radius: 20px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.6);
}


h2 {
    margin-bottom: 35px;
    font-size: 28px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

input, select {
    width: 100%;
    padding: 15px;
    margin-bottom: 22px;
    border-radius: 12px;
    border: none;
    background: #2a315a;
    color: grey;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
}

button {
    width: 100%;
    padding: 15px;
    border-radius: 12px;
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
    margin-top: 18px;
    padding: 14px;
    background: #2a315a;
    color: #7CFFB2;
    border-radius: 12px;
    text-align: center;
}

.login-link {
    margin-top: 30px;
    text-align: center;
    font-size: 14px;
}

.login-link a {
    color: #f5a25d;
    text-decoration: none;
    font-weight: 600;
}

.login-link a:hover {
    color: #ffb870;
}

.hidden {
    display: none;
}
/* ================= RESPONSIVE ================= */

@media (max-width: 900px) {

    body {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        min-height: auto;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 15px;
    }

    .sidebar h3 {
        display: none;
    }

    .sidebar a {
        margin: 0;
        padding: 8px 12px;
        font-size: 14px;
    }

    .main {
        padding: 20px;
    }

    .stats {
        flex-direction: column;
    }

    .stat-card {
        margin-bottom: 15px;
    }

    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
@media (max-width: 600px) {

    .card {
        padding: 18px;
    }

    .card p {
        font-size: 14px;
    }

    .btn {
        padding: 8px 14px;
        font-size: 14px;
    }

    textarea {
        font-size: 14px;
    }

    .actions {
        flex-direction: column;
    }

    .approve, .reject {
        width: 100%;
    }
}
@media (max-width: 600px) {

    .form-container {
        width: 90%;
        margin: 40px auto;
        padding: 25px;
    }

    input, select, textarea {
        font-size: 14px;
    }

    button {
        font-size: 14px;
    }
}

</style>
</head>

<body>

<div class="form-container">
    <h2>Student Registration</h2>

    <form method="POST">

        <input type="text" name="username" placeholder="Admission No" required>

        <input type="text" name="name" placeholder="Full Name" required>

        <select name="requested_role" id="roleSelect" required>
            <option value="">Select Position</option>
            <option value="class_rep">Class Representative</option>
            <option value="execom">Execom Member</option>
        </select>

        <!-- Club Field -->
        <div id="clubField" class="hidden">
            <input type="text" name="club" placeholder="Enter Club Name">
        </div>

        <!-- Class Field -->
        <div id="classField" class="hidden">
            <input type="text" name="class_name" placeholder="Enter Class (e.g., CSE-A)">
        </div>

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

<script>
document.getElementById("roleSelect").addEventListener("change", function() {
    let role = this.value;

    document.getElementById("clubField").classList.add("hidden");
    document.getElementById("classField").classList.add("hidden");

    if(role === "execom") {
        document.getElementById("clubField").classList.remove("hidden");
    }

    if(role === "class_rep") {
        document.getElementById("classField").classList.remove("hidden");
    }
});
</script>

</body>
</html>
