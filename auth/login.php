<?php
include '../config/database.php';
session_start();

$error = "";

// Already logged in → redirect
if (isset($_SESSION['user_id'])) {
    $panels = [
        'admin'              => '../admin/dashboard.php',
        'supplier'           => '../supplier_panel/dashboard.php',
        'production_manager' => '../production_panel/dashboard.php',
        'delivery_partner'   => '../delivery_panel/dashboard.php',
        'customer'           => '../customer/dashboard.php',
    ];
    $role = $_SESSION['role'] ?? '';
    if (isset($panels[$role])) {
        header("Location: " . $panels[$role]);
        exit();
    }
}

// ─── HANDLE LOGIN FORM ────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $q = mysqli_query($conn,
        "SELECT * FROM users
         WHERE email='" . mysqli_real_escape_string($conn, $email) . "'
           AND password='" . mysqli_real_escape_string($conn, $password) . "'
           AND status='active'
         LIMIT 1"
    );

    if ($q && mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        $panels = [
            'admin'              => '../admin/dashboard.php',
            'supplier'           => '../supplier_panel/dashboard.php',
            'production_manager' => '../production_panel/dashboard.php',
            'delivery_partner'   => '../delivery_panel/dashboard.php',
            'customer'           => '../customer/dashboard.php',
        ];

        if (isset($panels[$user['role']])) {
            header("Location: " . $panels[$user['role']]);
            exit();
        } else {
            $error = "Invalid user role.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | BakeChain SCM</title>
<meta name="description" content="Sign in to BakeChain SCM – the premium bakery supply chain management platform.">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --cream:    #EEE4DA;
    --sand:     #D8C4AC;
    --dusty:    #C8A49F;
    --burgundy: #4D0E13;
    --dark:     #2B1917;
}

* { box-sizing: border-box; }

body {
    margin: 0;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 35px;
    color: var(--dark);
}

.login-wrapper {
    width: 100%;
    max-width: 1180px;
    min-height: 650px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    background: #fff8f1;
    border-radius: 28px;
    overflow: hidden;
    border: 1px solid var(--sand);
    box-shadow: 0 35px 90px rgba(77,14,19,.18);
}

/* ── Left Panel ─────────────────────────────────────────── */
.login-left {
    position: relative;
    padding: 58px;
    color: white;
    overflow: hidden;
    background:
        linear-gradient(rgba(77,14,19,.22), rgba(77,14,19,.34)),
        url('https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=2400&q=100');
    background-size: cover;
    background-position: center;
}

.login-left::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(77,14,19,.16);
    z-index: 0;
}

.left-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.logo-box {
    width: 70px; height: 70px;
    border-radius: 50%;
    background: var(--cream);
    display: flex; align-items: center; justify-content: center;
    font-size: 31px;
    margin-bottom: 35px;
    box-shadow: 0 12px 30px rgba(0,0,0,.22);
}

.login-left h1 {
    font-size: 58px;
    line-height: 1.04;
    font-weight: 900;
    margin: 0 0 24px;
    color: #fff;
    letter-spacing: -1.5px;
    text-shadow: 0 6px 22px rgba(0,0,0,.28);
}
.login-left h1 span { display: block; color: var(--cream); }

.login-left .left-content > p {
    max-width: 455px;
    font-size: 17px;
    line-height: 1.85;
    font-weight: 800;
    color: #fff7f1;
    margin: 0;
    text-shadow: 0 4px 16px rgba(0,0,0,.30);
}

.left-footer { margin-top: auto; }
.footer-line {
    width: 100%; max-width: 420px;
    height: 1px;
    background: rgba(238,228,218,.58);
    margin-bottom: 22px;
}
.left-footer h4 {
    margin: 0;
    font-size: 13px;
    letter-spacing: 3px;
    font-weight: 900;
    color: #fff7f1;
    text-shadow: 0 4px 16px rgba(0,0,0,.28);
}

/* ── Right Panel ─────────────────────────────────────────── */
.login-right {
    background: #fff8f1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 55px;
}

.form-box { width: 100%; max-width: 430px; }

.form-box h2 {
    font-family: 'Playfair Display', serif;
    font-size: 38px;
    color: var(--burgundy);
    margin: 0 0 8px;
    font-weight: 900;
}
.form-box > p {
    color: #6f4d45;
    margin: 0 0 30px;
    font-weight: 600;
}

/* ── Error Box ───────────────────────────────────────────── */
.error-box {
    background: #f8d7da;
    color: #842029;
    border-left: 4px solid #842029;
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* ── Inputs ──────────────────────────────────────────────── */
.input-wrap {
    position: relative;
    margin-bottom: 18px;
}

.input-wrap i.input-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #a47d73;
    font-size: 18px;
    z-index: 3;
    pointer-events: none;
}

.input-wrap input {
    width: 100%;
    height: 58px;
    border: 1.5px solid var(--sand);
    background: #fff8f1;
    border-radius: 13px;
    padding: 0 54px 0 60px;
    color: var(--dark);
    font-size: 15px;
    font-weight: 600;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
}

.input-wrap input::placeholder { color: #a98b83; opacity: 1; }

.input-wrap input:focus {
    border-color: var(--burgundy);
    box-shadow: 0 0 0 4px rgba(77,14,19,.08);
}

.toggle-pw {
    position: absolute;
    right: 18px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #a47d73;
    font-size: 18px;
    cursor: pointer;
    padding: 0;
    z-index: 4;
}

/* ── Login Button ────────────────────────────────────────── */
.login-btn {
    width: 100%;
    height: 58px;
    border: none;
    border-radius: 13px;
    background: var(--burgundy);
    color: white;
    font-size: 17px;
    font-weight: 900;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: background .25s, transform .25s;
    margin-top: 6px;
}

.login-btn:hover {
    background: #2b070a;
    transform: translateY(-2px);
}

/* ── Auth Link ───────────────────────────────────────────── */
.auth-link {
    margin-top: 25px;
    text-align: center;
    color: #6f4d45;
    font-weight: 600;
}

.auth-link a {
    color: var(--burgundy);
    font-weight: 900;
    text-decoration: none;
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 900px) {
    .login-wrapper { grid-template-columns: 1fr; }
    .login-left    { display: none; }
    .login-right   { padding: 38px 25px; }
}
</style>
</head>

<body>

<div class="login-wrapper">

    <!-- ── Left Panel ── -->
    <div class="login-left">
        <div class="left-content">

            <div class="logo-box">🍪</div>

            <h1>
                BakeChain
                <span>SCM</span>
            </h1>

            <p>
                A premium bakery supply chain platform for tracking batches,
                inventory, orders, deliveries and QR traceability.
            </p>

            <div class="left-footer">
                <div class="footer-line"></div>
                <h4>BAKERY SUPPLY CHAIN MANAGEMENT</h4>
            </div>

        </div>
    </div>

    <!-- ── Right Panel ── -->
    <div class="login-right">

        <div class="form-box">

            <h2>Welcome Back 👋</h2>
            <p>Login to manage your bakery supply chain.</p>

            <?php if ($error): ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">

                <div class="input-wrap">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" name="email"
                           placeholder="Email Address"
                           required autocomplete="off">
                </div>

                <div class="input-wrap">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" name="password" id="login-password"
                           placeholder="Password"
                           required autocomplete="off">
                    <button type="button" class="toggle-pw"
                            onclick="togglePw('login-password', this)">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>

                <button type="submit" name="login" class="login-btn" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Login to Dashboard
                </button>

            </form>

            <div class="auth-link">
                Don't have an account?
                <a href="register.php">Create one →</a>
            </div>

        </div>

    </div>

</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type     = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type     = 'password';
        icon.className = 'bi bi-eye';
    }
}

document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing in…';
    btn.style.opacity = '.8';
    setTimeout(() => {
        btn.disabled = true;
    }, 10);
});
</script>

</body>
</html>