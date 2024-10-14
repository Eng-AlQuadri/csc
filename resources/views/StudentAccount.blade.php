<div class="doctors">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <div class="doc-head">
        <h2>My Account</h2>
    </div>
    <div class="table">
        <table class="animated fadeInDown">
            <thead>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
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

    getStudents();

    function getStudents() {

        // Show loading message
        $('.tbody').html(`
            <tr>
                <td colSpan="4" class="loading">
                    Loading...
                </td>
            </tr>`);

            let currentUser = JSON.parse(window.localStorage.getItem('user'));

        // Getting Students From Database
        $.ajax({
            url: "{{ route('students.show','') }}/" + currentUser.id,
            type: 'GET',
        }).then(function(res) {
            // Clear Loading Message
            $('.tbody').empty();

            $('.tbody').append(`
                <tr>
                    <td>${res.id}</td>
                    <td>${res.name}</td>
                    <td>${res.email}</td>
                    <td>
                        <button class="edit-user" data-id="${res.id}">Edit</button>
                    </td>
                </tr>`);


        }).fail(function() {
            $('.tbody').html(`<tr><td colSpan="4">Failed to load data</td></tr>`);
        });


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
                url: "{{ route('students.show', '') }}/" + studentId,
                type: 'GET'

            }).then(function(res) {

                let activateButton = res.active ? '' : `<button type="button" data-id='${res.id}' id='activate'>Activate</button>`;

                let editForm = `
                    <div id='edit-user-con' class="edit-message-content show">
                        <div class="head">
                            <h3>Edit Account</h3>
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
                            </form>
                        </div>
                    </div>
                `;

                // Display the form
                $('#edit-message').html(editForm);

            }).fail(function(res) {
                window.alert('failed');
            });
        });

        // Submit Edit Form
        $(document).on('submit', '#sumbitEditForm', function(e) {

            e.preventDefault();

            let formData = $(this).serialize(); // Get form data

            let studentId = $('#hiddendata').val(); // Extract student ID

            // Send the updated data to the server
            $.ajax({
                url: "{{ route('students.update', '') }}/" + studentId,
                type: 'PUT',
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

    }

</script>
