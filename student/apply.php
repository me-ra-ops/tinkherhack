<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || 
   !in_array($_SESSION['role'], ['class_rep','execom'])){
    die("Unauthorized");
}

$student_id = $_SESSION['user_id'];


/* Fetch Staff List */
$staffs = $conn->query("SELECT id, name FROM users WHERE role='staff'");

/* Fetch Staff List */
$staffs = $conn->query("SELECT id, name FROM users WHERE role='staff'");

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $type = $_POST['type'];
$start = $_POST['start'];
$end = $_POST['end'];
$details = $_POST['details'];
$staff_id = $_POST['staff_id'];

$club = $_POST['club'];

if($club === "Other"){
    $club = trim($_POST['other_club']);
}

if(empty($club)){
    die("Club is required.");
}

/* Force file upload */
if(empty($_FILES["file"]["name"])){
    die("File upload is mandatory.");
}

    $file_path = "";

    if(!empty($_FILES["file"]["name"])){
        $file_name = time()."_".$_FILES["file"]["name"];
        move_uploaded_file($_FILES["file"]["tmp_name"], "../uploads/".$file_name);
        $file_path = "uploads/".$file_name;
    }

    $stmt = $conn->prepare("INSERT INTO requests
(student_id,type,staff_id,club,start_datetime,end_datetime,details,file_path,status)
VALUES (?,?,?,?,?,?,?,?,'pending_staff')
");

    $stmt->bind_param("isisssss",
    $student_id,
    $type,
    $staff_id,
    $club,
    $start,
    $end,
    $details,
    $file_path
);


    $stmt->execute();

    $success = "Request submitted successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply Leave</title>
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
    max-width: 600px;
    margin: 80px auto;
    background: #1d2545;
    padding: 40px;
    border-radius: 18px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
}

h2 {
    margin-bottom: 30px;
    font-size: 26px;
}

label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.8;
    display: block;
    margin-bottom: 6px;
}

select, input, textarea {
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

select:focus, input:focus, textarea:focus {
    background: #323a6a;
}

textarea {
    resize: none;
    height: 100px;
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

.back-link {
    text-align: center;
    margin-top: 25px;
}

.back-link a {
    color: #f5a25d;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
}

.back-link a:hover {
    color: #ffb870;
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
    <h2>Apply for Leave</h2>

    <form method="POST" enctype="multipart/form-data">

        <label>Type</label>
        <select name="type" required>
            <option value="attendance">Attendance</option>
            <option value="duty_leave">Duty Leave</option>
        </select>
        <label>Club</label>
<select name="club" id="clubSelect" required>
    <option value="">Select Club</option>
    <option value="IEDC">IEDC</option>
    <option value="NSS">NSS</option>
    <option value="IEEE">IEEE</option>
    <option value="Arts Club">Arts Club</option>
    <option value="Other">Other</option>
</select>

<div id="otherClubDiv" style="display:none;">
    <label>Specify Other Club</label>
    <input type="text" name="other_club" id="otherClubInput">
</div>


        <label>Select Staff Coordinator</label>
        <select name="staff_id" required>
            <option value="">Select Staff</option>
            <?php while($staff = $staffs->fetch_assoc()): ?>
                <option value="<?= $staff['id'] ?>">
                    <?= $staff['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Start Date & Time</label>
        <input type="datetime-local" name="start" required>

        <label>End Date & Time</label>
        <input type="datetime-local" name="end" required>

        <label>Details</label>
        <textarea name="details" required></textarea>

        <label>Upload File</label>
<input type="file" name="file" required>

        <button type="submit">Submit Request</button>
    </form>
    <script>
document.getElementById("clubSelect").addEventListener("change", function() {
    const otherDiv = document.getElementById("otherClubDiv");
    const otherInput = document.getElementById("otherClubInput");

    if(this.value === "Other"){
        otherDiv.style.display = "block";
        otherInput.setAttribute("required", "true");
    } else {
        otherDiv.style.display = "none";
        otherInput.removeAttribute("required");
    }
});
</script>


    <?php if(isset($success)): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
