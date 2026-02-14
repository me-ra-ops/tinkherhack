<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'staff'){
    die("Unauthorized access");
}

$staff_id = $_SESSION['user_id'];
/* Pending approvals (only designated staff) */
$pendingStmt = $conn->prepare("
    SELECT r.*, u.name AS student_name
    FROM requests r
    JOIN users u ON r.student_id = u.id
    WHERE r.staff_id = ? AND r.status = 'pending_staff'
    ORDER BY r.created_at DESC
");
$pendingStmt->bind_param("i", $staff_id);
$pendingStmt->execute();
$pending = $pendingStmt->get_result();

/* All dean approved (view only for all staff) */
$approved = $conn->query("
    SELECT r.*, u.name AS student_name
    FROM requests r
    JOIN users u ON r.student_id = u.id
    WHERE r.status = 'approved_dean'
    ORDER BY r.created_at DESC
");

/* Build dynamic search query */
$searchDate = isset($_GET['search_date']) ? $_GET['search_date'] : '';
$searchClub = isset($_GET['search_club']) ? $_GET['search_club'] : '';

$query = "
    SELECT r.*, u.name AS student_name
    FROM requests r
    JOIN users u ON r.student_id = u.id
    WHERE r.status = 'approved_dean'
";

if(!empty($searchDate)){
    $query .= " AND DATE(r.start_datetime) = '$searchDate'";
}

if(!empty($searchClub)){
    $query .= " AND r.club LIKE '%$searchClub%'";
}

$query .= " ORDER BY r.created_at DESC";

$approved = $conn->query($query);

if(isset($_POST['approve']) || isset($_POST['reject'])){
    $request_id = $_POST['request_id'];
    $note = $_POST['note'];

    $status = isset($_POST['approve']) ? "approved_staff" : "rejected_staff";

    $update = $conn->prepare("UPDATE requests SET status=?, staff_note=? WHERE id=?");
    $update->bind_param("ssi", $status, $note, $request_id);
    $update->execute();

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <style>* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top left, #162a4a, #0f1f3a);
    color: white;
    display: flex;
}

/* Sidebar */

.sidebar {
    width: 220px;
    background: #121e36;
    padding: 30px 20px;
    min-height: 100vh;
    border-right: 1px solid rgba(255,255,255,0.05);
}

.sidebar h3 {
    margin-bottom: 40px;
    color: #f5a25d;
}

.sidebar a {
    display: block;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 8px;
    text-decoration: none;
    color: white;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.05);
}

/* Main */

.main {
    flex: 1;
    padding: 40px;
}

.topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
}

.topbar h2 {
    font-size: 26px;
}

.btn {
    padding: 10px 18px;
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

/* Stats */

.stats {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    flex: 1;
    background: #1a2b4c;
    padding: 20px;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
}

.stat-card h3 {
    margin: 0;
    font-size: 24px;
}

.stat-card p {
    opacity: 0.7;
}

/* Cards */

.card {
    background: #1a2b4c;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.5);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-4px);
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
}

.actions {
    display: flex;
    gap: 15px;
}

.approve, .reject {
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.approve {
    background: #7CFFB2;
    color: #0f1f3a;
}

.reject {
    background: #ff7a7a;
    color: white;
}

.view-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    background: #2a315a;
    color: #f5a25d;
}
</style>
</head>
<body><div class="sidebar">
    <h3>Staff Panel</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <h2>Staff Dashboard</h2>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3><?= $pending->num_rows ?></h3>
            <p>Pending Approvals</p>
        </div>

        <div class="stat-card">
            <h3><?= $approved->num_rows ?></h3>
            <!-- Search Bar -->
<form method="GET" style="margin:30px 0; display:flex; gap:15px; flex-wrap:wrap;">

    <input type="date" name="search_date"
           value="<?= isset($_GET['search_date']) ? $_GET['search_date'] : '' ?>"
           style="padding:10px; border-radius:8px; border:none; background:#2a315a; color:white;">

    <input type="text" name="search_club"
           placeholder="Search by Club"
           value="<?= isset($_GET['search_club']) ? $_GET['search_club'] : '' ?>"
           style="padding:10px; border-radius:8px; border:none; background:#2a315a; color:white;">

    <button type="submit"
            style="padding:10px 18px; border-radius:8px; border:none; background:#f5a25d; font-weight:bold; cursor:pointer;">
        Search
    </button>

    <a href="dashboard.php"
       style="padding:10px 18px; border-radius:8px; background:#2a315a; text-decoration:none; color:white;">
        Reset
    </a>

</form>

            <p>Dean Approved (View Only)</p>
        </div>
    </div>

    <h3>Pending Requests</h3>

    <?php if($pending->num_rows == 0): ?>
        <p>No pending approvals.</p>
    <?php endif; ?>

    <?php while($row = $pending->fetch_assoc()): ?>
        <div class="card">
            <p><strong>Student:</strong> <?= $row['student_name'] ?></p>
            <p><strong>Type:</strong> <?= ucfirst($row['type']) ?></p>
            <p><strong>From:</strong> <?= date("d M Y, h:i A", strtotime($row['start_datetime'])) ?></p>
            <p><strong>To:</strong> <?= date("d M Y, h:i A", strtotime($row['end_datetime'])) ?></p>

            <form method="POST">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <textarea name="note" placeholder="Add approval / rejection note..." required></textarea>

                <div class="actions">
                    <button type="submit" name="approve" class="approve">Approve</button>
                    <button type="submit" name="reject" class="reject">Reject</button>
                </div>
            </form>
        </div>
    <?php endwhile; ?>

    <h3 style="margin-top:50px;">Dean Approved Leaves</h3>

    <?php while($row = $approved->fetch_assoc()): ?>
        <div class="card">
    <p><strong>Student:</strong> <?= $row['student_name'] ?></p>
    <p><strong>Club:</strong> <?= htmlspecialchars($row['club']) ?></p>
    <p><strong>Type:</strong> <?= ucfirst($row['type']) ?></p>

    <p><strong>From:</strong>
        <?= date("d M Y, h:i A", strtotime($row['start_datetime'])) ?>
    </p>

    <p><strong>To:</strong>
        <?= date("d M Y, h:i A", strtotime($row['end_datetime'])) ?>
    </p>

    <p><strong>Details:</strong><br>
        <?= nl2br(htmlspecialchars($row['details'])) ?>
    </p>

    <?php if(!empty($row['file_path'])): ?>
        <p>
            <strong>Attachment:</strong>
            <a href="../<?= $row['file_path'] ?>"
               target="_blank"
               style="color:#f5a25d; text-decoration:none;">
                View Uploaded File
            </a>
        </p>
    <?php endif; ?>

    <?php if(!empty($row['staff_note'])): ?>
        <p><strong>Staff Note:</strong>
            <?= htmlspecialchars($row['staff_note']) ?>
        </p>
    <?php endif; ?>

    <?php if(!empty($row['dean_note'])): ?>
        <p><strong>Dean Note:</strong>
            <?= htmlspecialchars($row['dean_note']) ?>
        </p>
    <?php endif; ?>

    <span class="view-badge">Dean Approved</span>
</div>

    <?php endwhile; ?>

</div>
    </body>
</html>
