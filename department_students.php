<?php
include("./config.php");
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$department = isset($_GET['dept']) ? $_GET['dept'] : '';
$batch = $_GET['branch'];

// Fetch all unique departments for the sidebar
$dept_query = "SELECT DISTINCT department FROM student_details  where branch = '$batch'  ORDER BY department ";
$dept_result = $conn->query($dept_query);
$departments = [];
while ($row = $dept_result->fetch_assoc()) {
    $departments[] = $row['department'];
}
// Fetch students of the selected department and batch
$student_query = "
    SELECT id, name, usn, semester, section, branch 
    FROM student_details 
    WHERE department = ? 
    AND branch = ? 
    ORDER BY name";
    
$stmt = $conn->prepare($student_query);
$stmt->bind_param("ss", $department, $batch);  // Bind both department and batch
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($department) ?> Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./files/css/index.css">
    <link rel="stylesheet" href="./files/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #d8e6c3;">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold" href="#">Orientation</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="./index.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Add
            </a>
            <div class="dropdown-menu">
              <ul>
                <li><a class="dropdown-item" href="./add_student.php">Add Student Details</a></li>
                

              </ul>
            </div>
          </li>
          <li>
            &nbsp;

            &nbsp;
            &nbsp;
          </li>
          <li class="nav-item">
            <a class="btn btn-danger" href="./logout.php">Logout</a>
          </li>

        </ul>
        <form class="d-flex" role="search" id="searchForm">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" id="searchBar">
          <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
      </div>
    </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="my-5 col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                    <hr style="margin-top: -5px; margin-left:-12px; color: white; width:250px;">
                        <?php foreach ($departments as $dept): ?>
                            <li class="nav-item">
                                <a class="nav-link  side-link <?= ($dept == $department) ? 'sideActive' : '' ?>" 
                                   href="department_students.php?dept=<?= urlencode($dept) ?>&branch=<?=urldecode($batch)?>">
                                    <?= htmlspecialchars($dept) ?> 
                                </a>
                            </li>
                            
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
    <h2 class="mt-4"><?= htmlspecialchars($department) ?> Students</h2>
    <div class="container">
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 col-xl-3 mb-4">
                    <div class="card">
                        <div class="card-body" style="background-color: #fff;">
                            <p class="card-text"><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></p>
                            <p class="card-text"><strong>USN:</strong> <?= htmlspecialchars($row['usn']) ?></p>
                            <p class="card-text"><strong>Semester:</strong> <?= htmlspecialchars($row['semester']) ?></p>
                            <p class="card-text"><strong>Section:</strong> <?= htmlspecialchars($row['section']) ?></p>
                            <p class="card-text"><strong>Branch:</strong> <?= htmlspecialchars($row['branch']) ?></p>
                            <a href="profile.php?id=<?= $row['id'] ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./files/js/main.js"></script>
</body>
</html>