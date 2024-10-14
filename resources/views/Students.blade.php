<div class="doctors">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <div class="doc-head">
        <h2>Manage Students</h2>
        <button class="add-user">
            Add New Student
        </button>
    </div>
    <div class="table">
        <table class="animated fadeInDown">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Active</th>
                <th>Options</th>
            </thead>
            <tbody class="tbody">

            </tbody>
        </table>
    </div>
    {{-- <div class="notification"></div> --}}
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        getStudents();

        function getStudents() {

            // Show loading message
            $('.tbody').html(`
                <tr>
                    <td colSpan="5" class="loading">
                        Loading...
                    </td>
                </tr>`);

            // Getting Students From Database
            $.ajax({
                url: "{{ route('admin.students.index') }}",
                type: 'GET',
            }).then(function(res) {
                // Clear Loading Message
                $('.tbody').empty();

                res.forEach(student => {
                    $('.tbody').append(`
                        <tr>
                            <td>${student.id}</td>
                            <td>${student.name}</td>
                            <td>${student.email}</td>
                            <td>${student.active ? 'Yes' : 'No'}</td>
                            <td>
                                <button class="edit-user" data-id="${student.id}">Edit</button>
                                <button class="delete-user" data-id="${student.id}">Delete</button>
                            </td>
                        </tr>`);
                });

            }).fail(function() {
                $('.tbody').html(`<tr><td colSpan="5">Failed to load data</td></tr>`);
            });
        }

        // Delete Student
        $(document).on('click', '.delete-user', function(e) {

            e.preventDefault();

            let studentId = $(this).data('id');

            // Confirm deletion
            if (confirm('Are You Sure You Want To Delete This Studnet?')) {
                $.ajax({
                    url: "{{route('admin.students.destroy', '')}}/" + studentId, // Adjust the URL according to your route
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token here
                    }

                }).then(function(res) {

                    // setInterval(() => {
                    //     $('.notification').html(`<h3>Deleted Successfully</h3>`)
                    // }, 5000);

                    getStudents();

                })
                .fail(function(res){

                    // $('.notification').html(`<h3>Failed</h3>`)
                })

            }
        })

        // Edit Student
        $(document).on('click', '.edit-user', function(e) {

            e.preventDefault();

            // Add Show Class To Overlay And The Form
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close Edit Form
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            })


            let studentId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.students.show', '') }}/" + studentId,
                type: 'GET'

            }).then(function(res) {

                let activateButton = res.active ? '' : `<button type="button" data-id='${res.id}' id='activate'>Activate</button>`;

                let editForm = `
                    <div id='edit-user-con' class="edit-message-content show">
                        <div class="head">
                            <h3>Edit Student</h3>
                            <span id='close-edit-user'>x</span>
                        </div>
                        <div class="serrors">
                            <!-- <h3>Error</h3> -->
                        </div>
                        <div class="form">
                            <form action="" id='sumbitEditForm'>
                                <div>
                                    <label>Name:</label>
                                    <input type="text" name='name' id='editName' value="${res.name}">
                                </div>
                                <div>
                                    <label>Email:</label>
                                    <input type="email" name="email" id='editEmail' value="${res.email}">
                                </div>
                                <input name='id' type="hidden" id="hiddendata" value="${res.id}">
                                <button type="submit" class='edit-submit'>Edit</button>
                                ${activateButton}
                            </form>
                        </div>
                    </div>
                `;

                // Display the form
                $('#edit-message').html(editForm);

            }).fail(function(res) {


            });
        });

        // Activate Student
        $(document).on('click', '#activate', function(e) {

            e.preventDefault();

            let studentId = $(this).data('id');

            $.ajax({
                url: "{{ route('admin.students.activate', '') }}/" + studentId,
                type: 'put',
                data: studentId,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure CSRF token is included
                }
            }).then(function(res) {

                // Remove Show Class From Overlay And The Form
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');

                getStudents(); // Refresh the list of students

            }).fail(function(res) {

                $('.notification').html(`<h3>Failed to update student</h3>`);
            });

        })


        // Submit Edit Form
        $(document).on('submit', '#sumbitEditForm', function(e) {

            e.preventDefault();

            let formData = $(this).serialize(); // Get form data

            let studentId = $('#hiddendata').val(); // Extract student ID

            // Send the updated data to the server
            $.ajax({
                url: "{{ route('admin.students.update', '') }}/" + studentId, // Adjust this to match your update route
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Ensure CSRF token is included
                }
            }).then(function(res) {

                // Remove Show Class From Overlay And The Form
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');

                getStudents(); // Refresh the list of students

            }).fail(function(res) {

                let errorDiv = $('.serrors');

                let data = res.responseJSON;

                if (data.error) {
                    errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                    return;
                }
            });
        })

        // Add New Student
        $(document).on('click', '.add-user', function(e) {

            e.preventDefault();

            // Add Show Class To Overlay And The Form
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close Add Student Form
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            })

            let editForm = `
                <div id='edit-user-con' class="edit-message-content show">
                    <div class="head">
                        <h3>Add Student</h3>
                        <span id='close-edit-user'>x</span>
                    </div>
                    <div class="serrors">
                        <!-- <h3>Error</h3> -->
                    </div>
                    <div class="form">
                        <form action="" id='sumbitAddForm'>
                            <div>
                                <label>Name:</label>
                                <input type="text" name='name' id='editName'>
                            </div>
                            <div>
                                <label>Email:</label>
                                <input type="email" name="email" id='editEmail'>
                            </div>
                            <div>
                                <label>Password:</label>
                                <input type="text" name='password' id='editPhone'>
                            </div>
                            <button type="submit" class='edit-submit'>Add</button>
                        </form>
                    </div>
                </div>
            `;

            // Display the form
            $('#edit-message').html(editForm);

        })

        // Submit Add Form
        $(document).on('submit', '#sumbitAddForm', function(e) {

            e.preventDefault();

            let formData = $(this).serialize(); // Get form data

            // Send the updated data to the server
            $.ajax({
                url: "{{ route('admin.students.store') }}",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(function(res) {

                // Remove Show Class From Overlay And The Form
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');

                getStudents(); // Refresh the list of students

            }).fail(function(res) {

                let errorDiv = $('.serrors');

                let data = res.responseJSON;
                if (data.error) {
                    errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                    return;
                }
            });
        })

    });

</script>
