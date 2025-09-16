<?php
session_start();

// ✅ Auto-login if cookies exist
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['user_email'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['user_email'];

    header("Location: dashboard.php");
    exit;
}

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    if (empty($errors)) {
        $conn = new mysqli("localhost", "root", "", "smilesync");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashedPassword);
            $stmt->fetch();

            if (password_verify($password, $hashedPassword)) {
                // ✅ Save session
                $_SESSION['user_id'] = $id;
                $_SESSION['email'] = $email;

                // ✅ Remember Me: set cookies if checked
                if (!empty($_POST['remember'])) {
                    setcookie("user_email", $email, time() + (86400 * 30), "/"); // 30 days
                    setcookie("user_id", $id, time() + (86400 * 30), "/");
                }

                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SmileSync</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg border-0" style="max-width: 900px; width: 100%;">
      <div class="row g-0">
        
        <!-- Left side (Form) -->
        <div class="col-md-6 p-5">
          <h2 class="fw-bold" style="color: #1c6ea4;">Sign in to SmileSync</h2>
          <p class="text-muted mb-4">Enter your details below</p>

          <!-- Display errors -->
          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
              <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="login.php">
            <!-- Email -->
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="mb-2">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="rememberMe">
              <label class="form-check-label" for="rememberMe">
                Remember me
              </label>
            </div>

            <!-- Forgot + Login -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <a href="forgot.php" class="text-decoration-none text-muted" style="font-size: 15px;">Forgot Password?</a>
              <button type="submit" class="btn btn-primary w-50 py-1">Login</button>
            </div>

            <!-- Social logins (Optional placeholders) -->
            <p class="text-center text-muted mb-2" style="font-size: small;">Sign in with:</p>
            <div class="d-flex justify-content-center gap-5 mb-3">
              <button type="button" class="btn btn-outline-primary w-50">Facebook</button>
              <button type="button" class="btn btn-outline-danger w-50">Google</button>
            </div>

            <!-- Create account -->
            <p class="text-center mb-0">
              Don’t have an account? 
              <a href="register.php" class="fw-bold text-decoration-none" style="color: #1c6ea4;">Create account</a>
            </p>
          </form>
        </div>

        <!-- Right side (Image) -->
        <div class="col-md-6 d-none d-md-block">
          <img src="assets/images/Smile.png" 
               alt="Login Image" 
               class="img-fluid h-100 w-100" 
               style="object-fit: cover;">
        </div>

      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="text-muted text-center py-1" style="background-color: #9ECAD6;">
    <p class="mb-0">&copy; 2025 RanEditz. All rights reserved.</p>
  </footer>




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
 
function validateForm() {
  const emailField = document.getElementById("email");
  const emailError = document.getElementById("emailError");
  const email = emailField.value.trim();

  // Check if email ends with @gmail.com
  if (!email.endsWith("@gmail.com")) {
    emailError.textContent = "Email must be a @gmail.com address.";
    emailField.focus();
    return false;
  } else {
    emailError.textContent = "";
    return true;
  }
}

  </script>
</body>
</html>
