<div class="doctors">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <div class="doc-head">
        <h2>My Subjects</h2>
    </div>
    <div class="table">
        <table class="animated fadeInDown">
            <thead>
                <th>Subject Name</th>
                <th>Pass Mark</th>
                <th>Mark Obtained</th>
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

        getData();

        function getData() {

            // Show loading message
            $('.tbody').html(`
                <tr>
                    <td colSpan="3" class="loading">
                        Loading...
                    </td>
                </tr>`);

            let currentUser = JSON.parse(window.localStorage.getItem('user'))

            // Getting Data From Database (Marks)
            $.ajax({
                url: "{{ route('MarksWithCourses', '') }}/" + currentUser.id,
                type: 'GET',

            }).then(function(res) {

                // Clear Loading Message
                $('.tbody').empty();

                res.forEach(item => {
                    $('.tbody').append(`
                        <tr>
                            <td>${item.name}</td>
                            <td>${item.minmark}</td>
                            <td>${item.mark}</td>
                        </tr>`);
                });

            }).fail(function() {
                $('.tbody').html(`<tr><td colSpan="3">Failed to load data</td></tr>`);
            });

        }

    })

</script>
