<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập hệ thống</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font đẹp hơn -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f9c5d1, #f6a9c4, #f7dce7);
            background-size: 300% 300%;
            animation: gradientMove 10s ease infinite;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-card {
            width: 420px;
            background: #fff;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            animation: fadeIn 0.8s ease-in-out;
            border: 2px solid #f7d6e0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        h3 {
            font-weight: 600;
            color: #d56c91;
        }

        .form-control {
            border-radius: 12px;
            border: 1.5px solid #f3c2cf;
        }

        .form-control:focus {
            border-color: #e08aad;
            box-shadow: 0 0 8px rgba(218, 97, 140, 0.4);
        }

        .btn-pink {
            background: #e38db3;
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px;
            border-radius: 12px;
            transition: 0.3s ease;
        }

        .btn-pink:hover {
            background: #d56c91;
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(213,108,145,0.4);
        }

        .small-link {
            font-size: 14px;
            color: #d56c91;
            text-decoration: none;
        }

        .small-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="login-card">

    <h3 class="text-center mb-4">Đăng nhập hệ thống</h3>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger text-center p-2">Email hoặc mật khẩu không đúng!</div>
    <?php endif; ?>

    <form action="<?= BASEURL ?>?act=login_process" method="POST">

        <div class="mb-3">
            <label class="mb-1">Email đăng nhập</label>
            <input type="email" name="email" class="form-control" required placeholder="Nhập email của bạn">
        </div>

        <div class="mb-3">
            <label class="mb-1">Mật khẩu</label>
            <input type="password" name="mat_khau" class="form-control" required placeholder="Nhập mật khẩu của bạn">
        </div>

        <button class="btn btn-pink w-100 mt-2">Đăng nhập</button>

    </form>

    <div class="text-center mt-3">
        <a href="#" class="small-link">Quên mật khẩu?</a>
    </div>

</div>

</body>
</html>
