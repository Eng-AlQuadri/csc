<div class="dashboard">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <h1>Subjects</h1>
    <div class="card-holder animated fadeInDown">
        <div class="card" id="addSubject">
            <h3>Add Subject</h3>
            <div class="info">
                <div class="icon">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </div>
        </div>
        <div class="card" id="assignSubject">
            <h3>Assign Subject To Student</h3>
            <div class="info">
                <div class="icon">
                    <i class="fa-solid fa-person-circle-plus"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(function(){

        // Add Subject Functionality
        $('#addSubject').on('click',function(){

            // Add Show Class To Overlay And The Form
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close Edit Form
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            })

            let AddForm = `
                <div id='edit-user-con' class="edit-message-content show">
                    <div class="head">
                        <h3>Add Subject</h3>
                        <span id='close-edit-user'>x</span>
                    </div>
                    <div class="serrors">
                        <!-- <h3>Error</h3> -->
                    </div>
                    <div class="form">
                        <form action="" id='sumbitAddSubjectForm'>
                            <div>
                                <label>Name:</label>
                                <input type="text" name='name' id='editName'>
                            </div>
                            <div>
                                <label>Min-Mark:</label>
                                <input type="text" name="minmark" id='editEmail'>
                            </div>
                            <button type="submit" class='edit-submit'>Add</button>
                        </form>
                    </div>
                </div>
            `;

            // Display the form
            $('#edit-message').html(AddForm);
        })

        // Submit Add Subject Form
        $(document).on('submit', '#sumbitAddSubjectForm', function(e) {

            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.subjects.store') }}",
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }

            }).then(function(res) {

                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');

            }).fail(function(res) {

                let errorDiv = $('.serrors');

                let data = res.responseJSON;
                if (data.error) {
                    errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                    return;
                }
            })
        })

        // Assign Subject Functionality
        $('#assignSubject').on('click', function() {

            // Show the overlay and the form container
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close the form when clicking on the close button
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            });

            $.ajax({
                url: "{{ route('admin.assign.subjects.students') }}",
                type: 'get'

            }).then(function(res){

                let subjectsOptions = res.subjects.map(subject=>
                    `<option value="${subject.id}">${subject.name}</option>`
                ).join('');

                let studentsOptions = res.students.map(student =>
                    `<option value="${student.id}">${student.name}</option>`
                ).join('');


                let Form = `
                        <div id='edit-user-con' class="edit-message-content show">
                            <div class="head">
                                <h3>Assign Subject To Student</h3>
                                <span id='close-edit-user'>x</span>
                            </div>
                            <div class="serrors">
                                <!-- <h3>Error</h3> -->
                            </div>
                            <div class="form">
                                <form action="" id='submitAssignSubjectToStudentForm'>
                                    <div>
                                        <label>Subject:</label>
                                        <select name='subject_id' id='subjectDropdown' required>
                                            <option value="">Select a subject</option>
                                            ${subjectsOptions}
                                        </select>
                                    </div>
                                    <div>
                                        <label>Student:</label>
                                        <select name="student_id" id='studentDropdown' required>
                                            <option value="">Select a student</option>
                                            ${studentsOptions}
                                        </select>
                                    </div>
                                    <button type="submit" class='edit-submit'>Assign</button>
                                </form>
                            </div>
                        </div>
                    `;

                // Display the form
                $('#edit-message').html(Form);

            }).fail(function(res) {
                alert('Failed to load students or subjects');
            })
        });

        // Submit the form to assign the subject to the student
        $(document).on('submit', '#submitAssignSubjectToStudentForm', function(e) {

            e.preventDefault();

            $.ajax({
                url: "{{ route('admin.assign.subject') }}", // Adjust this route as needed
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }

            }).then(function(){

                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');

            }).fail(function(res){

                let errorDiv = $('.serrors');

                let data = res.responseJSON;
                if (data.error) {
                    errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                    return;
                }
            })
        });
    });


</script>
