<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    die("Unauthorized access");
}

/* Fetch pending students */
$pending = $conn->query("
    SELECT * FROM users 
    WHERE role IN ('class_rep','execom') 
    AND status='pending'
");

/* Handle approval */
if(isset($_POST['approve'])){
    $student_id = $_POST['student_id'];

    $update = $conn->prepare("UPDATE users SET status='approved' WHERE id=?");
    $update->bind_param("i", $student_id);
    $update->execute();

    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: #0f1f3a;
    color: white;
}

.container {
    max-width: 900px;
    margin: auto;
    padding: 60px 30px;
}

h2 {
    margin-bottom: 30px;
}

.card {
    background: #1d2545;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
}

select {
    padding: 10px;
    border-radius: 8px;
    border: none;
    background: #2a315a;
    color: white;
    margin-right: 10px;
}

button {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    background: #f5a25d;
    color: #1d2545;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    background: #ffb870;
}

.logout {
    margin-top: 40px;
}
</style>
</head>

<body>

<div class="container">

<h2>Pending Student Approvals</h2>

<?php if($pending->num_rows == 0): ?>
    <div class="card">
        <p>No students waiting for approval.</p>
    </div>
<?php endif; ?>

<?php while($student = $pending->fetch_assoc()): ?>
    <div class="card">
        <p><strong>Name:</strong> <?= $student['name'] ?></p>
        <p><strong>Admission No:</strong> <?= $student['username'] ?></p>
        <p><strong>Role:</strong> <?= $student['role'] ?></p>

        <form method="POST">
            <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

            <button name="approve">Approve</button>
        </form>
    </div>
<?php endwhile; ?>

<div class="logout">
    <a href="../logout.php" style="color:#f5a25d;">Logout</a>
</div>

</div>

</body>
</html>
