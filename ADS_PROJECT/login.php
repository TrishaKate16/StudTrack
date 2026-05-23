<?php
session_start();
require_once __DIR__ . '/db.php';

$message = '';
$alert_type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$email) {
        $message = 'Please enter a valid email address.';
    } elseif (empty($password)) {
        $message = 'Please enter your password.';
    } elseif (empty($role)) {
        $message = 'Please select a role.';
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($userId, $hashedPassword, $dbRole);
            $stmt->fetch();

            if ($dbRole !== $role) {
                $message = 'Invalid email or role.';
            } elseif (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $userId;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $dbRole;

                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
                $historyStmt = $conn->prepare("CREATE TABLE IF NOT EXISTS login_history (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id INT(6) UNSIGNED NOT NULL,
                    email VARCHAR(100) NOT NULL,
                    role ENUM('Student','Staff','Admin') NOT NULL,
                    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    ip_address VARCHAR(45) DEFAULT NULL,
                    user_agent TEXT,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
                $historyStmt->execute();
                $historyStmt->close();

                $insertHistoryStmt = $conn->prepare("INSERT INTO login_history (user_id, email, role, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
                $insertHistoryStmt->bind_param('issss', $userId, $email, $dbRole, $ipAddress, $userAgent);
                $insertHistoryStmt->execute();
                $insertHistoryStmt->close();

                $message = 'Login successful! Redirecting...';
                $alert_type = 'success';
                if ($dbRole === 'Admin') {
                    header('refresh:2;url=admin.php');
                } elseif ($dbRole === 'Staff') {
                    header('refresh:2;url=staff.php');
                } else {
                    header('refresh:2;url=student.php');
                }
            } else {
                $message = 'Invalid password.';
            }
        } else {
            $message = 'Invalid email or role.';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Stud-Truck Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-emerald-100 text-slate-900">
  <div class="min-h-screen flex items-center justify-center px-6">
    <main class="w-full max-w-md">
      <section class="rounded-[32px] bg-white p-10 shadow-xl">
            <div class="max-w-xl">
              <h1 class="text-4xl font-semibold text-center text-slate-700">Welcome!!</h1>
              <p class="mt-2 text-center text-slate-400">Enter your valid credentials to access the Stud-Track Management System.</p>
            </div>

            <form id="loginForm" action="login.php" method="post" class="mt-10 space-y-6" novalidate>
              <div id="alert" class="<?php echo $message ? '' : 'hidden'; ?> rounded-3xl border <?php echo $alert_type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900'; ?> px-4 py-3 text-sm"><?php echo $message; ?></div>

              <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-700" for="email">Email Address</label>
                <input id="email" name="email" type="email" placeholder="@stud-track.com" required class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
              </div>

              <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
                <div class="relative">
                  <input id="password" name="password" type="password" placeholder="password" required class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 pr-14 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" />
                  <button type="button" id="togglePassword" aria-label="Toggle password visibility" class="absolute inset-y-0 right-3 flex items-center rounded-full px-3 text-slate-500 transition hover:text-slate-700">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                      <circle cx="12" cy="12" r="3" />
                    </svg>
                  </button>
                </div>
              </div>

              <div class="space-y-3">
                <label class="block text-sm font-medium text-slate-700" for="role">Role</label>
                <select id="role" name="role" required class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                  <option value="">Select role</option>
                  <option value="Student" <?php echo ($_POST['role'] ?? '') === 'Student' ? 'selected' : ''; ?>>Student</option>
                  <option value="Staff" <?php echo ($_POST['role'] ?? '') === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                  <option value="Admin" <?php echo ($_POST['role'] ?? '') === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
              </div>

              <button type="submit" class="w-full rounded-[28px] bg-emerald-500 px-5 py-4 text-base font-semibold text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-600">Login</button>

              <div class="relative py-4">
                <div class="absolute inset-x-0 top-1/2 h-px bg-slate-200"></div>
              </div>
              <p class="text-center text-sm text-slate-500">Don't have an account? <a href="register.php" class="font-medium text-emerald-600 hover:text-emerald-700">Register here</a></p>
            </form>
          </section>
        </main>
      </div>
  <script src="assets/js/script.js"></script>
</body>
</html>