<?php
require_once __DIR__ . '/db.php';

$message = '';
$alert_type = 'error';

function isValidUsername($username) {
    return preg_match('/^[A-Za-z0-9 ._-]{3,50}$/', $username);
}

function isStrongPassword($password) {
    $weakPasswords = ['password', '123456', '12345678', 'qwerty', 'abc123', 'admin', 'letmein', '111111', '123123', 'password123', 'admin123'];
    if (strlen($password) < 8) {
        return false;
    }
    foreach ($weakPasswords as $weak) {
        if (stripos($password, $weak) !== false) {
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (empty($username) || !isValidUsername($username)) {
        $message = 'Please enter a valid username (3-50 characters, letters and numbers only).';
    } elseif (!$email) {
        $message = 'Please enter a valid email address.';
    } elseif (!isStrongPassword($password)) {
        $message = 'Password must be at least 8 characters and not a common weak value.';
    } elseif (empty($role)) {
        $message = 'Please select a role.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssss', $username, $email, $hashed_password, $role);

        try {
            if ($stmt->execute()) {
                $message = 'Registration successful! You can now login.';
                $alert_type = 'success';
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $message = 'That email is already registered.';
            } else {
                $message = 'Registration failed: ' . $e->getMessage();
            }
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
  <title>Register | Stud-Truck</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body class="min-h-screen bg-emerald-100 text-slate-900">
  <div class="min-h-screen flex items-center justify-center px-6">
    <main class="w-full max-w-md">
      <section class="rounded-[32px] bg-white p-10 shadow-xl">
        <div class="max-w-xl">
          <div class="flex justify-between items-center">
            <h1 class="text-4xl font-semibold text-slate-900">Register</h1>
            <button onclick="window.location.href='login.php'" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">&larr; Back</button>
          </div>
          <p class="mt-4 text-slate-600">Create your Stud-Track account to access the system.</p>
        </div>

        <form id="signinForm" action="register.php" method="post" class="mt-10 space-y-6" novalidate>
          <div id="alert" class="<?php echo $message ? '' : 'hidden'; ?> rounded-3xl border <?php echo $alert_type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900'; ?> px-4 py-3 text-sm"><?php echo $message; ?></div>

          <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-700" for="username">Username</label>
            <input id="username" name="username" type="text" placeholder="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" />
          </div>

          <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-700" for="email">Email Address</label>
            <input id="email" name="email" type="email" placeholder="@stud-track.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" />
          </div>

          <div class="space-y-3">
            <label class="block text-sm font-medium text-slate-700" for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="password" required class="w-full rounded-[28px] border border-slate-200 bg-slate-50 px-5 py-4 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100" />
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

          <button type="submit" class="w-full rounded-[28px] bg-emerald-500 px-5 py-4 text-base font-semibold text-white shadow-lg shadow-emerald-200 transition hover:bg-emerald-600">Register</button>
        </form>
      </section>
    </main>
  </div>
</body>
</html>