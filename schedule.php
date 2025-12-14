<?php
// Database Connection
$con = new mysqli("localhost", "root", "", "hms");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $doctor_name = $_POST['doctor_name'];
        $specialization = $_POST['specialization'];
        $day = $_POST['day'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $stmt = $con->prepare("INSERT INTO doctor_schedule (doctor_name, specialization, day, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $doctor_name, $specialization, $day, $start_time, $end_time);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $doctor_name = $_POST['doctor_name'];
        $specialization = $_POST['specialization'];
        $day = $_POST['day'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];

        $stmt = $con->prepare("UPDATE doctor_schedule SET doctor_name=?, specialization=?, day=?, start_time=?, end_time=? WHERE id=?");
        $stmt->bind_param("sssssi", $doctor_name, $specialization, $day, $start_time, $end_time, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $con->prepare("DELETE FROM doctor_schedule WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all schedules
$schedules = $con->query("SELECT * FROM doctor_schedule");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Doctor Schedule</h2>

        <!-- Add Schedule Form -->
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="doctor_name" class="form-control" placeholder="Doctor Name" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="specialization" class="form-control" placeholder="Specialization" required>
                </div>
                <div class="col-md-2">
                    <input type="text" name="day" class="form-control" placeholder="Day" required>
                </div>
                <div class="col-md-2">
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <input type="time" name="end_time" class="form-control" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="add" class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </form>

        <!-- Schedule Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $schedules->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['doctor_name']; ?></td>
                        <td><?php echo $row['specialization']; ?></td>
                        <td><?php echo $row['day']; ?></td>
                        <td><?php echo $row['start_time']; ?></td>
                        <td><?php echo $row['end_time']; ?></td>
                        <td>
                            <button 
                                class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                data-id="<?php echo $row['id']; ?>"
                                data-doctor_name="<?php echo $row['doctor_name']; ?>"
                                data-specialization="<?php echo $row['specialization']; ?>"
                                data-day="<?php echo $row['day']; ?>"
                                data-start_time="<?php echo $row['start_time']; ?>"
                                data-end_time="<?php echo $row['end_time']; ?>"
                            >
                                Edit
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Edit Schedule Modal -->
        <div class="modal fade" id="editModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Schedule</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" id="edit-id">
                            <div class="mb-3">
                                <label for="edit-doctor_name" class="form-label">Doctor Name</label>
                                <input type="text" class="form-control" name="doctor_name" id="edit-doctor_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control" name="specialization" id="edit-specialization" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-day" class="form-label">Day</label>
                                <input type="text" class="form-control" name="day" id="edit-day" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" name="start_time" id="edit-start_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit-end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" name="end_time" id="edit-end_time" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const doctorName = button.getAttribute('data-doctor_name');
            const specialization = button.getAttribute('data-specialization');
            const day = button.getAttribute('data-day');
            const startTime = button.getAttribute('data-start_time');
            const endTime = button.getAttribute('data-end_time');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-doctor_name').value = doctorName;
            document.getElementById('edit-specialization').value = specialization;
            document.getElementById('edit-day').value = day;
            document.getElementById('edit-start_time').value = startTime;
            document.getElementById('edit-end_time').value = endTime;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
