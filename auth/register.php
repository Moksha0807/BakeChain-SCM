<?php
include '../config/database.php';

$success = "";
$error   = "";

$old_name = "";
$old_email = "";
$old_role = "";
$old_phone = "";
$old_address = "";

$internal_roles = ['admin', 'supplier', 'production_manager', 'delivery_partner'];

if (isset($_POST['register'])) {
    $name     = trim($_POST['name']);
    $email    = strtolower(trim($_POST['email']));
    $password = trim($_POST['password']);
    $role     = $_POST['role'];
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);

    $old_name = $name;
    $old_email = $email;
    $old_role = $role;
    $old_phone = $phone;
    $old_address = $address;

    if ($role == "") {
        $error = "Please select your role.";
    } elseif (in_array($role, $internal_roles)) {
        if (!preg_match("/^[a-zA-Z0-9._%+\-]+@cookicraft\.com$/", $email)) {
            $error = "Staff accounts must use a @cookicraft.com email address.";
        }
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        }
    }

    if (!$error && !preg_match("/^[0-9]{10}$/", $phone)) {
        $error = "Phone number must be exactly 10 digits.";
    }

    if (!$error && strlen($password) < 4) {
        $error = "Password must be at least 4 characters.";
    }

    if (!$error) {
        $safe_email = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$safe_email'");

        if (mysqli_num_rows($check) > 0) {
            $error = "This email is already registered.";
        } else {
            $query = "INSERT INTO users (name,email,password,role,phone,address,status)
                      VALUES (
                        '" . mysqli_real_escape_string($conn, $name) . "',
                        '" . mysqli_real_escape_string($conn, $email) . "',
                        '" . mysqli_real_escape_string($conn, $password) . "',
                        '" . mysqli_real_escape_string($conn, $role) . "',
                        '" . mysqli_real_escape_string($conn, $phone) . "',
                        '" . mysqli_real_escape_string($conn, $address) . "',
                        'active'
                      )";

            if (mysqli_query($conn, $query)) {
                $success = "Account created successfully. You can now login.";
                $old_name = "";
                $old_email = "";
                $old_role = "";
                $old_phone = "";
                $old_address = "";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | BakeChain SCM</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
    --cream:#EEE4DA;
    --sand:#D8C4AC;
    --dusty:#C8A49F;
    --burgundy:#4D0E13;
    --dark:#2B1917;
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    min-height:100vh;
    font-family:'Inter',sans-serif;
    background:var(--cream);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:35px;
    color:var(--dark);
}

.register-wrapper{
    width:100%;
    max-width:1260px;
    min-height:680px;
    display:grid;
    grid-template-columns:1fr 1.08fr;
    background:#fff8f1;
    border-radius:28px;
    overflow:hidden;
    border:1px solid var(--sand);
    box-shadow:0 35px 90px rgba(77,14,19,.18);
}

.register-left{
    position:relative;
    padding:58px;
    color:white;
    overflow:hidden;
    background:
        linear-gradient(
            rgba(77,14,19,.22),
            rgba(77,14,19,.34)
        ),
        url('https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=2400&q=100');
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
}

.register-left::before{
    content:"";
    position:absolute;
    inset:0;
    background:rgba(77,14,19,.16);
    z-index:0;
}

.left-content{
    position:relative;
    z-index:2;
    height:100%;
    display:flex;
    flex-direction:column;
}

.logo-box{
    width:70px;
    height:70px;
    border-radius:50%;
    background:var(--cream);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:31px;
    margin-bottom:35px;
    box-shadow:0 12px 30px rgba(0,0,0,.22);
}

.register-left h1{
    font-size:56px;
    line-height:1.04;
    font-weight:900;
    margin:0 0 24px;
    color:#fff;
    letter-spacing:-1.5px;
    text-shadow:0 6px 22px rgba(0,0,0,.28);
}

.register-left h1 span{
    display:block;
    color:var(--cream);
}

.register-left p{
    max-width:455px;
    font-size:17px;
    line-height:1.85;
    font-weight:800;
    color:#fff7f1;
    margin:0;
    text-shadow:0 4px 16px rgba(0,0,0,.30);
}

.steps{
    margin-top:34px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.step{
    display:flex;
    align-items:center;
    gap:13px;
    color:#fff7f1;
    font-size:15px;
    font-weight:800;
    text-shadow:0 4px 14px rgba(0,0,0,.25);
}

.step-num{
    width:34px;
    height:34px;
    border-radius:50%;
    background:var(--cream);
    color:var(--burgundy);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    box-shadow:0 8px 20px rgba(0,0,0,.18);
}

.left-footer{
    margin-top:auto;
}

.footer-line{
    width:100%;
    max-width:420px;
    height:1px;
    background:rgba(238,228,218,.58);
    margin-bottom:22px;
}

.left-footer h4{
    margin:0;
    font-size:13px;
    letter-spacing:3px;
    font-weight:900;
    color:#fff7f1;
    text-shadow:0 4px 16px rgba(0,0,0,.28);
}

.register-right{
    background:#fff8f1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:55px;
}

.form-box{
    width:100%;
    max-width:680px;
}

.form-box h2{
    font-family:'Playfair Display',serif;
    font-size:40px;
    color:var(--burgundy);
    margin:0 0 8px;
    font-weight:900;
}

.form-box p{
    color:#6f4d45;
    margin:0 0 24px;
    font-weight:600;
}

.email-rule{
    display:none;
    background:#fff4e6;
    border:1.5px solid var(--sand);
    border-left:5px solid var(--burgundy);
    border-radius:10px;
    padding:12px 15px;
    margin-bottom:18px;
    color:var(--burgundy);
    font-size:13px;
    font-weight:800;
    align-items:center;
    gap:9px;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:16px;
}

.full{
    grid-column:1 / 3;
}

.input-wrap{
    position:relative;
}

.input-wrap i.input-icon{
    position:absolute;
    left:19px;
    top:50%;
    transform:translateY(-50%);
    color:#a47d73;
    font-size:17px;
    z-index:3;
    pointer-events:none;
}

.textarea-wrap i.input-icon{
    top:22px;
    transform:none;
}

.input-wrap input,
.input-wrap select,
.input-wrap textarea{
    width:100%;
    border:1.5px solid var(--sand);
    background:#fff8f1;
    border-radius:0;
    padding:0 42px 0 54px;
    color:var(--dark);
    font-size:15px;
    font-weight:600;
    outline:none;
    font-family:'Inter',sans-serif;
}

.input-wrap input,
.input-wrap select{
    height:58px;
}

.input-wrap textarea{
    min-height:115px;
    resize:none;
    padding-top:20px;
}

.input-wrap input[type="email"]{
    font-size:14px;
    letter-spacing:-.2px;
}

.input-wrap input::placeholder,
.input-wrap textarea::placeholder{
    color:#8f7770;
    opacity:1;
}

.input-wrap input:focus,
.input-wrap select:focus,
.input-wrap textarea:focus{
    border-color:var(--burgundy);
    box-shadow:0 0 0 4px rgba(77,14,19,.08);
}

.toggle-pw{
    position:absolute;
    right:18px;
    top:50%;
    transform:translateY(-50%);
    background:transparent;
    border:none;
    color:#a47d73;
    font-size:18px;
    cursor:pointer;
    padding:0;
}

.register-btn{
    width:100%;
    height:60px;
    border:none;
    border-radius:0;
    background:var(--burgundy);
    color:white;
    font-size:17px;
    font-weight:900;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    transition:.25s;
    margin-top:2px;
}

.register-btn:hover{
    background:#2b070a;
    transform:translateY(-2px);
}

.auth-link{
    margin-top:25px;
    text-align:center;
    color:#6f4d45;
    font-weight:700;
}

.auth-link a{
    color:var(--burgundy);
    font-weight:900;
    text-decoration:none;
}

.error-box,
.success-box{
    padding:14px 16px;
    border-radius:10px;
    margin-bottom:18px;
    font-weight:700;
}

.error-box{
    background:#f8d7da;
    color:#842029;
    border-left:4px solid #842029;
}

.success-box{
    background:#d1e7dd;
    color:#0f5132;
    border-left:4px solid #0f5132;
}

@media(max-width:980px){
    .register-wrapper{
        grid-template-columns:1fr;
    }

    .register-left{
        display:none;
    }

    .register-right{
        padding:38px 25px;
    }
}

@media(max-width:650px){
    .form-grid{
        grid-template-columns:1fr;
    }

    .full{
        grid-column:1;
    }
}
</style>
</head>

<body>

<div class="register-wrapper">

    <div class="register-left">
        <div class="left-content">

            <div class="logo-box">🍪</div>

            <h1>
                BakeChain
                <span>SCM</span>
            </h1>

            <p>
                Bakery supply chain management for suppliers, raw materials,
                production batches, inventory, orders, deliveries and QR traceability.
            </p>

            <div class="steps">
                <div class="step"><div class="step-num">1</div> Create your account</div>
                <div class="step"><div class="step-num">2</div> Select your role</div>
                <div class="step"><div class="step-num">3</div> Access your panel</div>
            </div>

            <div class="left-footer">
                <div class="footer-line"></div>
                <h4>SMART BAKERY SUPPLY CHAIN</h4>
            </div>

        </div>
    </div>

    <div class="register-right">

        <div class="form-box">

            <h2>Create Account</h2>
            <p>Register with valid details to access your panel.</p>

            <div id="emailHint" class="email-rule">
                <i class="bi bi-info-circle-fill"></i>
                <span id="emailHintText"></span>
            </div>

            <?php if($success != "") { ?>
                <div class="success-box">
                    <i class="bi bi-check-circle"></i>
                    <?php echo $success; ?>
                </div>
            <?php } ?>

            <?php if($error != "") { ?>
                <div class="error-box">
                    <i class="bi bi-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php } ?>

            <form method="POST" autocomplete="on">

                <div class="form-grid">

                    <div class="input-wrap">
                        <i class="bi bi-person input-icon"></i>
                        <input type="text" name="name" placeholder="Full Name"
                               value="<?php echo htmlspecialchars($old_name); ?>"
                               required autocomplete="name">
                    </div>

                    <div class="input-wrap">
                        <i class="bi bi-telephone input-icon"></i>
                        <input type="text" name="phone" placeholder="Phone Number"
                               value="<?php echo htmlspecialchars($old_phone); ?>"
                               pattern="[0-9]{10}" minlength="10" maxlength="10"
                               title="Phone must be exactly 10 digits"
                               required autocomplete="tel">
                    </div>

                    <div class="input-wrap full">
                        <i class="bi bi-person-badge input-icon"></i>
                        <select name="role" id="roleSelect" required>
                            <option value="">Select Role</option>
                            <option value="admin" <?php if($old_role=="admin") echo "selected"; ?>>Admin</option>
                            <option value="supplier" <?php if($old_role=="supplier") echo "selected"; ?>>Supplier</option>
                            <option value="production_manager" <?php if($old_role=="production_manager") echo "selected"; ?>>Production Manager</option>
                            <option value="delivery_partner" <?php if($old_role=="delivery_partner") echo "selected"; ?>>Delivery Partner</option>
                            <option value="customer" <?php if($old_role=="customer") echo "selected"; ?>>Customer</option>
                        </select>
                    </div>

                    <div class="input-wrap full">
                        <i class="bi bi-envelope input-icon"></i>
                        <input type="email" name="email" id="regEmail"
                               placeholder="Email Address"
                               value="<?php echo htmlspecialchars($old_email); ?>"
                               required autocomplete="off">
                    </div>

                    <div class="input-wrap full">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" name="password" id="reg-password"
                               placeholder="Password" minlength="4" required autocomplete="new-password">
                        <button type="button" class="toggle-pw"
                                onclick="togglePw('reg-password', this)">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>

                    <div class="input-wrap full textarea-wrap">
                        <i class="bi bi-geo-alt input-icon"></i>
                        <textarea name="address" placeholder="Address" required><?php echo htmlspecialchars($old_address); ?></textarea>
                    </div>

                    <div class="full">
                        <button type="submit" name="register" class="register-btn">
                            <i class="bi bi-person-plus"></i>
                            Register
                        </button>
                    </div>

                </div>

            </form>

            <div class="auth-link">
                Already have an account?
                <a href="login.php">Login</a>
            </div>

        </div>

    </div>

</div>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

const internalRoles = ['admin', 'supplier', 'production_manager', 'delivery_partner'];
const roleSelect = document.getElementById('roleSelect');
const emailInput = document.getElementById('regEmail');
const emailHint = document.getElementById('emailHint');
const emailHintText = document.getElementById('emailHintText');

function updateEmailRule() {
    const role = roleSelect.value;

    if (!role) {
        emailHint.style.display = 'none';
        emailInput.removeAttribute('pattern');
        emailInput.placeholder = 'Email Address';
        return;
    }

    if (internalRoles.includes(role)) {
        emailInput.pattern = '[a-zA-Z0-9._%+\\-]+@cookicraft\\.com';
        emailInput.placeholder = 'yourname@cookicraft.com';
        emailHintText.textContent = 'Staff accounts must use a @cookicraft.com email address.';
        emailHint.style.display = 'flex';
    } else {
        emailInput.removeAttribute('pattern');
        emailInput.placeholder = 'Email Address';
        emailHintText.textContent = 'Customers can register with any valid email address.';
        emailHint.style.display = 'flex';
    }
}

roleSelect.addEventListener('change', updateEmailRule);
updateEmailRule();
</script>

</body>
</html>