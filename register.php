<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Register</h1>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <form method="POST" action="auth.php">
            <div class="form-group">
                <input type="text" name="lname" class="form-control" required placeholder="Last Name">
            </div>
            <div class="form-group">
                <input type="text" name="fname" class="form-control" required placeholder="First Name">
            </div>
            <div class="form-group">
                <input type="email" name="email" class="form-control" required placeholder="Email">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>
            <div class="form-group">
                <input type="password" name="confirm" class="form-control" required placeholder="Confirm Password">
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>
