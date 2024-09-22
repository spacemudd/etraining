<!DOCTYPE html>
<html>
<head>

</head>

<body class="font-sans antialiased  bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen container mx-auto justify-content-center">
        <img src="{{url('/img/logo-lg.png')}}" alt="Image" width="400" height="200"/>
    </div>
    <h1> Certificate of completion of course: {{$course_name}} </h1>

    <hr>

    <p> This certificate is awarded for {{$trainee_name}} for the successful completion of the course {{$course_name}} </p>

    <footer> Date: {{date('Y/m/d')}}</footer>
</body>
</html>
