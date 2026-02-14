<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dean'){
    die("Unauthorized access");
}

$stmt = $conn->prepare("
    SELECT r.*, u.name AS student_name
    FROM requests r
    JOIN users u ON r.student_id = u.id
    WHERE r.status = 'approved_staff'
    ORDER BY r.created_at DESC
");

$stmt->execute();
$data = $stmt->get_result();

if(isset($_POST['approve']) || isset($_POST['reject'])){
    $request_id = $_POST['request_id'];
    $note = $_POST['note'];
    $status = isset($_POST['approve']) ? "approved_dean" : "rejected_dean";

    $update = $conn->prepare("UPDATE requests SET status=?, dean_note=? WHERE id=?");
    $update->bind_param("ssi", $status, $note, $request_id);
    $update->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dean Dashboard</title>
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

.container {
    max-width: 1000px;
    margin: auto;
    padding: 50px 30px;
}

h2 {
    font-size: 28px;
    margin-bottom: 30px;
}

.top-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.btn {
    padding: 12px 18px;
    border-radius: 8px;
    background: #f5a25d;
    color: #1d2545;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

.btn:hover {
    background: #ffb870;
}

.card {
    background: #1d2545;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 25px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-4px);
}

.card p {
    margin: 8px 0;
    opacity: 0.9;
}

textarea {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    border: none;
    background: #2a315a;
    color: white;
    margin-top: 15px;
    margin-bottom: 15px;
    resize: none;
    transition: 0.3s;
}

textarea:focus {
    background: #323a6a;
}

.actions {
    display: flex;
    gap: 15px;
}

.approve, .reject {
    padding: 12px 18px;
    border-radius: 10px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.approve {
    background: #f5a25d;
    color: #1d2545;
}

.approve:hover {
    background: #ffb870;
}

.reject {
    background: #2a315a;
    color: #ff7a7a;
}

.reject:hover {
    background: #323a6a;
}

.empty {
    opacity: 0.7;
    font-size: 16px;
}

    </style>
</head>
<body>

<div class="container">

    <div class="top-bar">
        <h2>Requests Awaiting Dean Approval</h2>
        <a class="btn" href="../logout.php">Logout</a>
    </div>

    <?php if($data->num_rows == 0): ?>
        <p class="empty">No requests waiting for approval.</p>
    <?php endif; ?>

    <?php while($row = $data->fetch_assoc()): ?>

        <div class="card">
            <p><strong>Student:</strong> <?= $row['student_name'] ?></p>
            <p><strong>Type:</strong> <?= ucfirst($row['type']) ?></p>
            <p><strong>From:</strong> <?= $row['start_datetime'] ?></p>
            <p><strong>To:</strong> <?= $row['end_datetime'] ?></p>
            <p><strong>Details:</strong> <?= $row['details'] ?></p>
            <p><strong>Staff Note:</strong> <?= $row['staff_note'] ?></p>

            <?php if($row['file_path']): ?>
                <p><strong>Attachment:</strong>
                    <a href="../<?= $row['file_path'] ?>" target="_blank">View File</a>
                </p>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <textarea name="note" placeholder="Add dean approval / rejection note..." required></textarea>

                <div class="actions">
                    <button type="submit" name="approve" class="approve">Approve</button>
                    <button type="submit" name="reject" class="reject">Reject</button>
                </div>
            </form>
        </div>

    <?php endwhile; ?>

</div>

</body>
</html>
