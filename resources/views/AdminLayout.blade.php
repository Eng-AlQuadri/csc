@auth

    <script>
        let currentUser = JSON.parse(window.localStorage.getItem('user'));
        if(currentUser.role !== 'admin')
            window.location.href = "{{route('login')}}";
    </script>

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
        <title>Admin</title>
    </head>
    <body>
        <div class="defaultLayout">
            <div class="upperBar">
                <div class="holder">
                    <span class="icon" id="icon">
                        <i class="fa-solid fa-bars"></i>
                    </span>
                    <span class="settingsHolder">
                        <h4>Adminstrator</h4>
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
                    <li class="load-view" data-view="Students">
                        <span class="icon"><i class="fa-solid fa-user-group"></i></span>
                        <span class="title">ٍStudents</span>
                    </li>
                    <li class="load-view" data-view="Subjects">
                        <span class="icon"><i class="fa-solid fa-book"></i></span>
                        <span class="title">Subjects</span>
                    </li>
                    <li class="load-view" data-view="Marks">
                        <span class="icon"><i class="fa-solid fa-marker"></i></span>
                        <span class="title">Marks</span>
                    </li>
                    <li class="load-view" data-view="MyAccount">
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
            {{-- <div class="notification">notification</div> --}}
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
                        url: "{{ route('load-view', '') }}/" + viewName,
                        type: 'GET'

                    }).then(function(res) {

                        $('.pageContent').html(res);
                    })
                })

            })
        </script>
    </body>
    </html>
@else
    <script>window.location.href = "{{route('login')}}"</script>
@endauth