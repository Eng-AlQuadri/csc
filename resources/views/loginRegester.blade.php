@extends('GuestLayout')

@section('title')
    Login
@endsection


@section('content')
    <div class="signup-section">
        <div class="container">
            <form class='signup'>
                @csrf
                <h2>Signup For Free</h2>
                <div class="serrors">
                    <!-- <h3>Error</h3> -->
                </div>
                <div class="field">
                    <label for="">Name</label>
                    <input type="text" name='name' required>
                </div>
                <div class="field">
                    <label for="">Email</label>
                    <input type="email" name='email' required>
                </div>
                <div class="field">
                    <label for="">Password</label>
                    <input type="password" class='pass' name='password' required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field">
                    <label for="">Repeat Password</label>
                    <input type="password" class='pass' name='password_confirmation' required>
                </div>
                <input type="submit" value='Signup' id='submit-btn' name='signup'>
                <div class="question">
                    <p>Already Signed Up? <span data-class='login'>Login Now</span></p>
                </div>
            </form>

            <form class="login">
                @csrf
                <h2>Login Into Your Account</h2>
                <div class="errors">
                    <!-- <h3>Error</h3> -->
                </div>
                <div class="field">
                    <label for="">Email</label>
                    <input type="email" name='email' required>
                </div>
                <div class="field">
                    <label for="">Password</label>
                    <input type="password" class='pass' name='pass' required>
                    <i class="fas fa-eye"></i>
                </div>
                <input type="submit" value='Login' id='submit-btn' name='login'>
                <div class="question">
                    <p>Don't Have Acount? <span data-class = 'signup'>Sign Up Now</span></p>
                    For Testing
                    <br>
                    <br>
                    Email: admin@admin.com
                    <br>
                    <br>
                    Password: password
                    <br>
                    <hr>
                    <br>
                    Email: student@student.com
                    <br>
                    <br>
                    Password: password
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>

        $(function(){

            // Show Signup Section On Page Load
            $('.signup-section form').hide();

            $('.signup').fadeIn(500);

            // Swap Between Two Forms
            $('.signup-section span').on('click',function() {

                $('.signup-section form').hide();

                $('.' + $(this).data('class')).fadeIn(500);

            });

            // Show Password
            $('.fa-eye').hover(function() {

                $('.pass').attr('type','text');

            }, function() {

                $('.pass').attr('type','password');

            });

            // Login Functionality
            $('.login').on('submit', function(e) {

                e.preventDefault();
                let errorDiv = $('.errors');

                // Clear previous errors
                errorDiv.removeClass('show').empty();

                $.ajax({

                    url: '{{ route("login") }}', //
                    type: 'POST',
                    data: $(this).serialize()

                }).then(function(res) {

                    window.localStorage.setItem('cscSchool.token', res.token);
                    window.localStorage.setItem('user', JSON.stringify(res.user));

                    let currentUser = res.user.role;

                    switch(currentUser) {
                        case 'admin':
                            window.location.href = "{{ route('admin') }}";
                            break;
                        case 'student':
                            window.location.href = "{{ route('student') }}";
                            break;
                    }


                }).fail(function(res) {

                    let errorDiv = $('.errors');

                    let data = res.responseJSON;
                    if (data.error) {
                        errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                        return;
                    }
                    // errorDiv.addClass('show').html(`<h3>Error Attempting To Login</h3>`);
                });
            });

            // Signup Functionality
            $('.signup').on('submit',function(e) {

                e.preventDefault();

                let errorDiv = $('.serrors');

                // Clear previous errors
                errorDiv.removeClass('show').empty();

                $.ajax({

                    url: '{{ route("signup") }}',
                    type: 'POST',
                    data: $(this).serialize()

                }).then(function(res) {

                    let errorDiv = $('.serrors');

                    errorDiv.addClass('show').html(`<h3 style="background-color: lightgreen;">${res.error}</h3>`);
                    return;

                }).fail(function(res) {

                    let errorDiv = $('.serrors');

                    let data = res.responseJSON;
                    if (data.error) {
                        errorDiv.addClass('show').html(`<h3 style="background-color: lightcoral;">${data.error}</h3>`);
                        return;
                    }
                })
            })
        });

    </script>
@endsection
