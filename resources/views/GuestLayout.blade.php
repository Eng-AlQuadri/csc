@php
    if(auth()->user())
        $role = auth()->user()->role;
    else
        $role = 'empty';
@endphp

@switch($role)
    @case('admin')
        <script>window.location.href = "{{route('admin')}}"</script>
        @break
    @case('student')
        <script>window.location.href = "{{route('student')}}"</script>
        @break
    @default

@endswitch

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ url('css/master.css') }}">
    <link rel="stylesheet" href="{{ url('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/normalize.css') }}">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>@yield('title')</title>
</head>


<body>
    <div class="box">
        @yield('content')
    </div>
</body>
</html>

