<!DOCTYPE html>
<html lang="th">
<head>
    <?php require_once('function/language.php'); ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .login {
            background-color: #c70000;
            color: #ffffff;
        }

        .btn-custom {
            background-color: #FF0000; /* สีแดง */
            color: #FFFFFF; /* ฟอนต์สีขาว */
            border: none;
            transition: background-color 0.3s ease; /* เพิ่มการเปลี่ยนแปลงแบบ smooth */
        }

        .btn-custom:hover,
        .btn-custom:focus {
            background-color: #c70000; /* สีแดงเข้มเมื่อ hover หรือ focus */
            color: #FFFFFF; /* ฟอนต์สีขาว */
        }

        .btn-custom:active {
            background-color: #a50000; /* สีแดงเข้มเมื่อถูกคลิก */
            color: #FFFFFF; /* ฟอนต์สีขาว */
        }

        .form-link {
            color: #F0592E; /* Custom orange color for links */
        }

        .logo-img {
            max-width: 100%; /* ปรับให้รูปปรับขนาดตาม container */
            height: auto;   /* รักษาอัตราส่วนความสูงและความกว้าง */
            display: block;
            margin: 0 auto 20px; /* จัดตำแหน่งตรงกลางและเพิ่มช่องว่างด้านล่าง */
        }

        .card-body {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .card-body {
                padding: 15px;
            }

            .btn-custom {
                font-size: 16px;
                padding: 12px;
            }
        }

        @media (max-width: 576px) {
            .btn-custom {
                font-size: 14px;
                padding: 10px;
            }

            .logo-img {
                max-width: 80%;
            }
        }

        /* Footer Styling */
        footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            color: #555;
            font-size: 14px;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12"> <!-- ใช้ col-lg, col-md เพื่อปรับขนาดฟอร์ม -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4"><?php echo $lang_login ?></h4>
                    <a class="navbar-brand" href="../view/index">
                        <img src="../view/assets/img/logo/logo.png" alt="Logo" class="img-fluid logo-img">
                    </a>
                    <form id="login" method="post">
                        <div class="mb-3">
                            <label for="signin-email" class="form-label"><?php echo $lang_email ?></label>
                            <input type="email" class="form-control" id="signin-email" name="signin-email" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''; ?>" required placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="signin-password" class="form-label"><?php echo $lang_password ?></label>
                            <input type="password" class="form-control" id="signin-password" name="signin-password" required placeholder="Password">
                        </div>

                        <!-- ลบการเก็บรหัสผ่านในคุกกี้ -->
                        <!-- <input type="checkbox" id="remember" name="remember"> Remember Me -->

                        <div class="login">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom" name="login"><?php echo $lang_login ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer Section -->
<footer>
    Made by WE.DEV
</footer>

<script>
$(document).ready(function() {
    // Login form submit
    $('#login').submit(function(e) {
        e.preventDefault();

        let user_email = $('#signin-email').val();
        let user_pass = $('#signin-password').val();
        let remember = $('#remember').is(':checked');

        $.ajax({
            url: 'function/action_login.php',
            type: 'post',
            contentType: 'application/json',  // Ensure JSON is sent
            data: JSON.stringify({
                user_email: user_email,
                user_pass: user_pass,
                remember: remember,
                login: 1
            }),
            success: function(response) {
                console.log(response); // Log the response for debugging
                if (response === 'admin') {
                    if (remember) {
                        // ใช้โทเค็นแทนการเก็บรหัสผ่าน
                        // ตัวอย่าง: เก็บโทเค็นที่เซิร์ฟเวอร์ได้ตั้งไว้แล้ว
                    } else {
                        document.cookie = "remember_me=; max-age=-1; path=/";
                    }
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        window.location.href = "admin/upload_page.php"; // Redirect to admin page
                    }, 1500);
                } else if (response === 'failuser') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ไม่มีบัญชีนี้ในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'failpass') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ถูกต้อง!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'close') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'บัญชีนี้ถูกระงับการใช้งาน',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else if (response === 'invalid_user_type') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'สิทธิ์การใช้งานไม่ถูกต้อง',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    console.log('Unexpected response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
                console.log(xhr.responseText); // Log the actual response
            }
        });
    });
});
</script>

</body>
</html>
