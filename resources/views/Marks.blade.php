<div class="dashboard">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <h1>Marks</h1>
    <div class="card-holder animated fadeInDown">
        <div class="card" id="addMark">
            <h3>Set Mark</h3>
            <div class="info">
                <div class="icon">
                    <i class="fa-solid fa-plus"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(function(){

        // Add Mark Functionality
        $('#addMark').on('click',function(){

            // Add Show Class To Overlay And The Form
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close Edit Form
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            })

            let MarkForm = `
                <div id='edit-user-con' class="edit-message-content show">
                    <div class="head">
                        <h3>Set Mark</h3>
                        <span id='close-edit-user'>x</span>
                    </div>
                    <div class="serrors"></div>
                    <div class="form">
                        <form id='submitMarkForm'>
                            <div>
                                <label>Student:</label>
                                <select id='studentDropdown' name='student_id' required>
                                    <option value="">Select a student</option>
                                </select>
                            </div>
                            <div id="subjectContainer" style="display:none;">
                                <label>Subject:</label>
                                <select id='subjectDropdown' name='subject_id' required>
                                    <option value="">Select a subject</option>
                                </select>
                            </div>
                            <div id="markContainer" style="display:none;">
                                <label>Enter Mark:</label>
                                <input type="number" name="mark" id='markInput' required>
                            </div>
                            <button type="submit" class='edit-submit'>Submit Mark</button>
                        </form>
                    </div>
                </div>
            `;

            $('#edit-message').html(MarkForm);

            // Fetch all students and populate the dropdown
            $.ajax({
                url: "{{ route('admin.students.index') }}",
                type: 'GET'

            }).then(function (res) {

                let studentOptions = res.map(student =>
                    `<option value="${student.id}">${student.name}</option>`
                ).join('');

                $('#studentDropdown').append(studentOptions);

            }).fail(function () {

                alert('Failed to load students.');
            });
        });

        // On Student Selection, Load Subjects
        $(document).on('change', '#studentDropdown', function () {
            let studentId = $(this).val();

            if (studentId) {
                $.ajax({
                    url: `{{route('admin.subjectByStudent','')}}/` + studentId, // Adjust route as needed
                    type: 'GET'

                }).then(function (res) {

                    $('#subjectDropdown').empty();
                    $('#subjectDropdown').append(`<option value="">Select a subject</option>`);


                    let subjectOptions = res.subjects.map(subject =>
                        `<option value="${subject.subject.id}">${subject.subject.name}</option>`
                    ).join('');
                    $('#subjectDropdown').append(subjectOptions);
                    $('#subjectContainer').show(); // Show subject dropdown

                }).fail(function () {
                    alert('Failed to load subjects for this student.');
                });

            } else {

                $('#subjectContainer').hide();
                $('#markContainer').hide();

            }
        });

        // On Subject Selection, Show Mark Input
        $(document).on('change', '#subjectDropdown', function () {
            let subjectId = $(this).val();
            if (subjectId) {
                $('#markContainer').show();
            } else {
                $('#markContainer').hide();
            }
        });

        // Submit Mark Form
        $(document).on('submit', '#submitMarkForm', function (e) {
            e.preventDefault();

            let studentId = $('#studentDropdown').val();
            let subjectId = $('#subjectDropdown').val();
            let mark = $('#markInput').val();

            $.ajax({
                url: "{{ route('admin.marks.store') }}", // Adjust route accordingly
                type: 'POST',
                data: {
                    student_id: studentId,
                    subject_id: subjectId,
                    mark: mark
                },
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(function () {

                alert('Mark added successfully!');
                $('#edit-message').removeClass('show');

            }).fail(function (res) {

                let errorDiv = $('.serrors');
                let data = res.responseJSON;
                if (data.error) {
                    errorDiv.addClass('show').html(`<h3>${data.error}</h3>`);
                }

            });
        });

    });

</script>
