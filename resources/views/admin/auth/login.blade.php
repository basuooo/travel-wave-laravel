<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="card admin-card p-4" style="max-width:420px;width:100%">
        <h1 class="h3 mb-3">Travel Wave Admin</h1>
        <p class="text-muted">Sign in to manage the website content.</p>
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('admin.login.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>
