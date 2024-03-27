<!-- resources/views/employee/show.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
</head>
<body>
    <h1>Employee Details</h1>
    
    <div>
        <strong>ID:</strong> {{ $employeeData['id'] }}
    </div>
    <div>
        <strong>Name:</strong> {{ $employeeData['name'] }}
    </div>
    <div>
        <strong>Email:</strong> {{ $employeeData['email'] }}
    </div>
    <!-- Add more fields as needed -->
</body>
</html>
