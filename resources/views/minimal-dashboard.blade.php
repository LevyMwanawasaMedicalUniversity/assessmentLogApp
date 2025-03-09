<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3>Minimal Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <p>This is a minimal dashboard to diagnose rendering issues.</p>
                        
                        <div class="alert alert-success">
                            If you can see this page, the basic rendering is working correctly.
                        </div>
                        
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Refresh Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
