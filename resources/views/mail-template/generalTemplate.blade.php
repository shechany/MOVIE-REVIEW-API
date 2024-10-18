<!DOCTYPE html>
<html>
<head>
    <title>Email from {{ config('app.name') }}</title>
</head>
<body>
    <h1>New Signup</h1>
    <p>Dear admin, there is a new signup, the details are below.</p>
    
    <p>Fullname: {{$name}}</p>
</body>
</html>