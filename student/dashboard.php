<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || 
   !in_array($_SESSION['role'], ['class_rep','execom'])){
    die("Unauthorized");
}


$user_id = $_SESSION['user_id'];
/* Handle Delete Request */
if(isset($_POST['delete_request'])){
    $request_id = $_POST['request_id'];

    // Only delete if:
    // 1. Belongs to this student
    // 2. Still pending staff approval

    $delete = $conn->prepare("
        DELETE FROM requests 
        WHERE id=? 
        AND student_id=? 
        AND status='pending_staff'
    ");

    $delete->bind_param("ii", $request_id, $user_id);
    $delete->execute();

    header("Location: dashboard.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM requests WHERE student_id=? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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

/* Main content */

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

/* Stats Section */

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

/* Request Cards */

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

.status {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.pending {
    background: #2a315a;
    color: #f5a25d;
}

.approved {
    background: #2a315a;
    color: #7CFFB2;
}

.rejected {
    background: #2a315a;
    color: #ff7a7a;
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
<body><div class="sidebar">
    <h3>Student Panel</h3>
    <a href="dashboard.php">Dashboard</a>
    <a href="apply.php">Apply Leave</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">

    <div class="topbar">
        <h2>My Requests</h2>
        <a class="btn" href="apply.php">+ New Request</a>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3><?= $result->num_rows ?></h3>
            <p>Total Requests</p>
        </div>
    </div>

    <?php while($row = $result->fetch_assoc()): ?>

    <?php
        $statusClass = "pending";

        if($row['status'] == "approved_dean"){
            $statusClass = "approved";
        } elseif(strpos($row['status'], "rejected") !== false){
            $statusClass = "rejected";
        }
    ?>

    <div class="card">
        <p><strong>Type:</strong> <?= ucfirst($row['type']) ?></p>

        <p>
            <strong>Status:</strong>
            <span class="status <?= $statusClass ?>">
                <?= strtoupper($row['status']) ?>
            </span>
        </p>

        <p><strong>From:</strong>
            <?= date("d M Y, h:i A", strtotime($row['start_datetime'])) ?>
        </p>

        <p><strong>To:</strong>
            <?= date("d M Y, h:i A", strtotime($row['end_datetime'])) ?>
        </p>

        <?php if($row['status'] == 'pending_staff'): ?>
            <form method="POST" style="margin-top:15px;">
                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                <button type="submit" name="delete_request"
                        style="
                            background:#ff7a7a;
                            color:white;
                            border:none;
                            padding:10px 16px;
                            border-radius:8px;
                            cursor:pointer;
                            font-weight:bold;
                        ">
                    Delete Request
                </button>
            </form>
        <?php endif; ?>

    </div>

<?php endwhile; ?>


</div>
    </body>
</html>
