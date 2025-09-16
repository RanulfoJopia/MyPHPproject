<?php
session_start();

// Initialize error and success variables
$errors = [];
$success = '';
$old = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $middleName = htmlspecialchars(trim($_POST['middleName']));
    $suffix = htmlspecialchars(trim($_POST['suffix']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $old = $_POST;

    // Validation
    if (empty($firstName)) $errors[] = "First name is required.";
    elseif (!preg_match("/^[a-zA-Z-' ]+$/", $firstName)) $errors[] = "First name must contain letters only.";

    if (empty($lastName)) $errors[] = "Last name is required.";
    elseif (!preg_match("/^[a-zA-Z-' ]+$/", $lastName)) $errors[] = "Last name must contain letters only.";

    if (!empty($middleName) && !preg_match("/^[a-zA-Z-' ]*$/", $middleName)) $errors[] = "Middle name must contain letters only.";

    if (!empty($suffix) && !preg_match("/^(Jr|Sr|II|III|IV|V|[a-zA-Z0-9]+)$/", $suffix)) $errors[] = "Invalid suffix format.";

    if (empty($email)) $errors[] = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email.";

    if (empty($password)) $errors[] = "Password is required.";
    if (!empty($password)) {
        if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters long.";
        if (!preg_match("/[A-Z]/", $password)) $errors[] = "Password must contain at least one uppercase letter.";
        if (!preg_match("/[a-z]/", $password)) $errors[] = "Password must contain at least one lowercase letter.";
        if (!preg_match("/[0-9]/", $password)) $errors[] = "Password must contain at least one number.";
        if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)) $errors[] = "Password must contain at least one special character.";
    }

    if ($password !== $confirmPassword) $errors[] = "Passwords do not match.";

    // If no errors, insert into database
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $conn = new mysqli("localhost", "root", "", "smilesync");
        if ($conn->connect_error) $errors[] = "Connection failed: " . $conn->connect_error;

        if (empty($errors)) {
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $checkEmail->bind_param("s", $email);
            $checkEmail->execute();
            $checkEmail->store_result();

            if ($checkEmail->num_rows > 0) {
                $errors[] = "Email is already registered. Try logging in.";
            } else {
                $stmt = $conn->prepare("INSERT INTO users (firstName, lastName, middleName, suffix, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $firstName, $lastName, $middleName, $suffix, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $success = "✅ Registration successful! You can now log in.";
                    $old = []; // Clear old values
                } else {
                    $errors[] = "Database error: " . $stmt->error;
                }
                $stmt->close();
            }
            $checkEmail->close();
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - SmileSync</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/login.css">
<style>
.error-msg { color: red; font-size: 0.85rem; }
</style>
</head>
<body>
<div class="d-flex justify-content-center align-items-center min-vh-100">
<div class="card shadow-lg border-0" style="max-width: 900px; width: 100%;">
<div class="row g-0">
<div class="col-md-6 p-5">
<h4 class="fw-bold" style="color: #1c6ea4;">Create Your SmileSync Account</h4>
<p class="text-muted mb-4">Fill in your details below</p>

<!-- Display Errors -->
<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
<ul class="mb-0">
<?php foreach ($errors as $error): ?>
<li><?= $error ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<!-- Display Success -->
<?php if ($success): ?>
<div class="alert alert-success">
<?= $success ?>
</div>
<?php endif; ?>

<form method="POST" action="" id="registerForm">
<div class="row">
<div class="col-md-6 mb-2">
<label class="form-label">First Name</label>
<input type="text" class="form-control form-control-sm" name="firstName" id="firstName" required
value="<?= htmlspecialchars($old['firstName'] ?? '') ?>">
<div id="firstNameError" class="error-msg"></div>
</div>
<div class="col-md-6 mb-2">
<label class="form-label">Last Name</label>
<input type="text" class="form-control form-control-sm" name="lastName" id="lastName" required
value="<?= htmlspecialchars($old['lastName'] ?? '') ?>">
<div id="lastNameError" class="error-msg"></div>
</div>
</div>

<div class="row">
<div class="col-md-6 mb-2">
<label class="form-label">Middle Name</label>
<input type="text" class="form-control form-control-sm" name="middleName" id="middleName"
value="<?= htmlspecialchars($old['middleName'] ?? '') ?>">
<div id="middleNameError" class="error-msg"></div>
</div>
<div class="col-md-4 mb-2">
<label class="form-label">Suffix</label>
<input type="text" class="form-control form-control-sm" name="suffix" placeholder="e.g. Jr, Sr, III"
value="<?= htmlspecialchars($old['suffix'] ?? '') ?>">
</div>
</div>

<div class="mb-2">
<label class="form-label">Email</label>
<input type="email" class="form-control form-control-sm" name="email" required
value="<?= htmlspecialchars($old['email'] ?? '') ?>">
</div>

<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">Password</label>
<input type="password" class="form-control form-control-sm" name="password" required>
</div>
<div class="col-md-6 mb-3">
<label class="form-label">Confirm Password</label>
<input type="password" class="form-control form-control-sm" name="confirmPassword" required>
</div>
</div>

<div class="d-grid mb-3">
<button type="submit" class="btn btn-primary py-2">Register</button>
</div>

<p class="text-center mb-0">
Already have an account? 
<a href="login.php" class="fw-bold text-decoration-none" style="color: #1c6ea4;">Login here</a>
</p>
</form>
</div>

<div class="col-md-6 d-none d-md-block">
<img src="assets/images/Smile.png" alt="Register Image"
class="img-fluid h-100 w-100" style="object-fit: cover;">
</div>
</div>
</div>
</div>

<footer class="text-muted text-center py-1" style="background-color: #9ECAD6;">
<p class="mb-0">&copy; 2025 RanEditz. All rights reserved.</p>
</footer>

<script>
function validateNameField(inputId, errorId) {
    const input = document.getElementById(inputId);
    const error = document.getElementById(errorId);
    
    input.addEventListener('input', function() {
        if (/[^a-zA-Z-' ]/.test(input.value)) {
            error.textContent = 'Must contain letters only';
        } else {
            error.textContent = '';
        }
    });
}

// Apply real-time validation to name fields
validateNameField('firstName', 'firstNameError');
validateNameField('lastName', 'lastNameError');
validateNameField('middleName', 'middleNameError');

// Block form submission if errors exist
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const middleName = document.getElementById('middleName').value;

    const nameRegex = /^[a-zA-Z-' ]*$/;

    if (!nameRegex.test(firstName) || !nameRegex.test(lastName) || (middleName && !nameRegex.test(middleName))) {
        alert('Please fix the errors before submitting the form.');
        e.preventDefault();
    }
});
</script>

</body>
</html>
