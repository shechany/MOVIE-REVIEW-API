<!DOCTYPE html>
<html>
<head>
    <title>Email from {{ config('app.name') }}</title>
</head>
<body>
    <h1>New Movie</h1>
    <p>Dear user, there is a new movie added to our collection.</p>
    
    <p>Movie title: {{$title}}</p>
    <p>Description: {{$description}}</p>
    <p>Release Date: {{$release_date}}</p>
</body>
</html>