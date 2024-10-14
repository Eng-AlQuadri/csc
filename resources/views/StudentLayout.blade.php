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
    <title>Student</title>
</head>
<body>
    <div class="defaultLayout">
        <div class="upperBar">
            <div class="holder">
                <span class="icon" id="icon">
                    <i class="fa-solid fa-bars"></i>
                </span>
                <span class="settingsHolder">
                    <h4>Student</h4>
                    <div class="logoutIcon">
                        {{-- <i class="fa-solid fa-right-from-bracket"> --}}
                    </div>
                </span>
            </div>
        </div>
        <div class= "aside" id='aside'>
            <ul>
                <li class="load-view">
                    <span class="icon"><i class="fa-solid fa-school"></i></i></span>
                    <span class="title">School System</span>
                </li>
                <li class="load-view" data-view="MyCourses">
                    <span class="icon"><i class="fa-solid fa-marker"></i></span>
                    <span class="title">My Subjects</span>
                </li>
                <li class="load-view" data-view="StudentAccount">
                    <span class="icon"><i class="fa-solid fa-person"></i></span>
                    <span class="title">My Account</span>
                </li>
                <li class="load-view" data-view="logout">
                    <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                    <span class="title">Logout</span>
                </li>

            </ul>
        </div>
        <div class="pageContent" id="content">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function(){

            // Adjusting Layout
            $('#icon').on('click',function(){

                if($('.defaultLayout #aside').hasClass('closed')) {

                    $('.defaultLayout #aside').removeClass('closed')
                    $('.defaultLayout #content').removeClass('opened')

                }else{

                    $('.defaultLayout #aside').addClass('closed');
                    $('.defaultLayout #content').addClass('opened')
                }
            })

            // Getting Views
            $('.load-view').on('click',function(){

                // Get The Clicked View
                let viewName = $(this).data('view');

                // Logout Functionality
                if(viewName === 'logout') {
                    $.ajax({
                        url: "{{ route('logout') }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel's CSRF token for security
                        }
                    }).then(function(res) {

                        window.localStorage.removeItem('cscSchool.token');
                        window.localStorage.removeItem('user');
                        window.location.href = "{{ route('login') }}";

                    }).fail(function(res) {
                        window.alert('failed');
                    })

                }

                $.ajax({
                    url: "{{ route('student.load-view', '') }}/" + viewName,
                    type: 'GET'

                }).then(function(res) {

                    $('.pageContent').html(res);
                })
            })

        })
    </script>
</body>
</html>

