<div class="chat animated fadeInDown">
    <div id='edit-message' class="edit-message-overlay">

    </div>
    <div class="left-menu">
        <div class="search-bar">
            <button class="chat-btn" id="newGroup">
                New Group
            </button>
        </div>

        <ul class="names">

        </ul>
    </div>
    {{-- hidden next to chat-content --}}
    <div class= "chat-content hidden animated fadeInDown" id='holder'>
        <div class="main-holder">
            <div class="head">
                <div class="field">
                    <div class="icon">
                        <img src="/images/pic.jpg" alt="img" />
                    </div>
                    <div class="texts">
                        <p class="chat-name" id="chatName">Name</p>
                    </div>
                </div>
            </div>
            <div class="body" id="messageHolder">
                {{-- <div class="sender">
                    <p class="sender-message">
                        Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Exercitationem facilis esse
                        similique harum, ullam tempora nostrum
                        cupiditate quia dolor nobis omnis repellendus,
                        eligendi architecto illo animi optio soluta
                        earum placeat!
                    </p>
                </div>
                <div class="reciever">
                    <p class="reciever-message">
                        Lorem ipsum dolor sit amet consectetur
                        adipisicing elit. Doloribus nesciunt voluptas
                        aliquid earum tempore quod nemo sapiente
                        voluptatem quas, soluta, id, illo aliquam enim
                        incidunt adipisci quidem molestiae consequuntur
                        odio.
                    </p>
                </div> --}}
            </div>
            <div class="footer">
                <input type="text" placeholder="Type Message" id="sendMessage"/>
                <button id="sendMessageButton">Send</button>
            </div>
        </div>
    </div>
</div>



<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
<script src="https://js.pusher.com/7.2/pusher.min.js" defer></script>
<script>

    $(function() {

        const pusher = new Pusher("{{config('broadcasting.connections.pusher.key')}}", {cluster:'ap2'});
        pusher.logToConsole = true;

        const channel = pusher.subscribe('public');
        console.log(channel);


        channel.bind('chat', function(data) {
            console.log('New message received:', data);
            const messageHolder = $('#messageHolder');
            const newMessage = $(`<div class="reciever"><p class="reciever-message">${data.message.content}</p></div>`);
            messageHolder.append(newMessage);

        })

        let currentContact;

        let currentGroup;

        getUsers();

        // Get Users
        function getUsers() {

            const contactsList = $('.names');

            currentUser = JSON.parse(window.localStorage.getItem('user'));

            // Getting Students From Database
            $.ajax({

                url: "{{ route('student.shared.users') }}",
                type: 'GET'

            }).then(function(contacts) {

                contacts.forEach(contact => {

                    if(contact.role === 'admin') {
                        const contactItem = $(`
                            <li data-id=${contact.id} data-name=${contact.name} id='contact'>
                                <div class="field">
                                    <div class="icon">
                                        <img src="{{ url('images/pic.jpg') }}" alt="img">
                                    </div>
                                    <div class="texts">
                                        <p class="chat-name">${contact.name}</p>
                                    </div>
                                </div>
                            </li>
                        `);

                        contactsList.append(contactItem);
                    }

                    const contactItem = $(`
                        <li data-id=${contact.id} data-name=${contact.name} id='contact'>
                            <div class="field">
                                <div class="icon">
                                    <img src="{{ url('images/pic.jpg') }}" alt="img">
                                </div>
                                <div class="texts">
                                    <p class="chat-name">${contact.name}</p>
                                    <p class='status'>Subjects: ${contact.subjects.map(s => s.name).join(' | ')}</p>
                                </div>
                            </div>
                        </li>
                    `);

                    contactsList.append(contactItem);
                })

            }).fail(function() {

            });

            // Getting groups from database
            $.ajax({

                url: "{{ route('student.getGroups', '')}}/" + currentUser.id,
                type: 'GET'

            }).then(function(contacts) {

                contacts.forEach(contact => {

                    const contactItem = $(`
                        <li data-gid=${contact.id} data-name=${contact.name} id='contact'>
                            <div class="field">
                                <div class="icon">
                                    <img src="{{ url('images/pic.jpg') }}" alt="img">
                                </div>
                                <div class="texts">
                                    <p class="chat-name">${contact.name}</p>
                                </div>
                            </div>
                        </li>
                    `);

                    contactsList.append(contactItem);
                })

                }).fail(function() {

            });
        }

        // Open Chat

        $(document).on('click', '#contact', function(e) {

            currentContact = null;

            currentGroup = null;

            const headName = $('#chatName');

            const chatName = $('#content');

            const messageHolder = $('#messageHolder');

            let theMessage;

            const currentUser = JSON.parse(window.localStorage.getItem('user'));

            if($(this).data('gid')){
                currentGroup = $(this).data('gid');
            }
            else{
                currentContact = $(this).data('id');
            }

            // Open Chatting
            $('#holder').removeClass('hidden');

            if(currentContact) { // Getting Messages For Persons

                // Chat Name On Header
                $.ajax({
                    url: "{{ route('students.show', '') }}/" + currentContact,
                    type: 'GET'

                }).then(function(res) {

                    headName.html(res.name);
                })

                // Get Messages
                $.ajax({

                    url: "{{ route('student.message.index', '') }}/" + currentContact,
                    type: 'GET'

                }).then(function(messages) {

                    // Clear Chat Body
                    messageHolder.empty();

                    if(messages.length === 0) {
                        let messageError = '<h3 style="text-align:center; color:teal">No Messages</h3>'
                        messageHolder.append(messageError);
                        return;
                    }


                    // Adding Messages On Screen
                    messages.forEach(message => {

                        theMessage = $(`

                            <div class="${message.sender_id === currentUser.id ? 'sender' : 'reciever'}">
                                <p class="${message.sender_id === currentUser.id ? 'sender-message' : 'reciever-message'}">
                                    ${message.content}
                                </p>
                            </div>

                        `);

                        messageHolder.append(theMessage);
                    })
                })

            } else if(currentGroup) { //Getting Messages For Group

                // Chat Name On Header
                $.ajax({
                    url: "{{ route('students.group.name', '') }}/" + currentGroup,
                    type: 'GET'

                }).then(function(res) {

                    headName.html(res);
                })

                // Get Messages
                $.ajax({

                    url: "{{ route('student.message.group', '') }}/" + currentGroup,
                    type: 'GET'

                }).then(function(messages) {

                    // Clear Chat Body
                    messageHolder.empty();

                    if(messages.length === 0) {
                        let messageError = '<h3 style="text-align:center; color:teal">No Messages</h3>'
                        messageHolder.append(messageError);
                        return;
                    }


                    // Adding Messages On Screen
                    messages.forEach(message => {

                        theMessage = $(`

                            <div class="${message.sender_id === currentUser.id ? 'sender' : 'reciever'}">
                                <p class="${message.sender_id === currentUser.id ? 'sender-message' : 'reciever-message'}">
                                    ${message.content}
                                </p>
                            </div>

                        `);

                        messageHolder.append(theMessage);
                    })
                })
            }



        })

        // Send Message Functionality
        $(document).on('click', '#sendMessageButton', function (e) {

            // sending messages for persons
            if(currentContact) {

                e.preventDefault();

                const sender_id = JSON.parse(window.localStorage.getItem('user'));

                const message = $('#sendMessage').val();

                if(message.length === 0)
                    return;

                $.ajax({
                    url: "{{ route('student.message.store') }}",
                    type: 'POST',
                    data: {
                        sender_id: sender_id.id,
                        reciever_id: currentContact,
                        content: message.trim(),
                        status: 'sent'
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }

                }).then(function() {

                    const newMessage = $(`
                        <div class="sender">
                            <p class="sender-message">${message.trim()}</p>
                        </div>
                    `);
                    $('#messageHolder').append(newMessage);
                    $('#sendMessage').val(''); // Clear input
                })

            } else if (currentGroup) { // Sending Messages To Group

                e.preventDefault();

                const sender_id = JSON.parse(window.localStorage.getItem('user'));

                const message = $('#sendMessage').val();

                if(message.length === 0)
                    return;

                $.ajax({
                    url: "{{ route('student.message.store') }}",
                    type: 'POST',
                    data: {
                        sender_id: sender_id.id,
                        group_id: currentGroup,
                        content: message.trim(),
                        status: 'sent'
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }

                }).then(function() {

                    const newMessage = $(`
                        <div class="sender">
                            <p class="sender-message">${message.trim()}</p>
                        </div>
                    `);
                    $('#messageHolder').append(newMessage);
                    $('#sendMessage').val(''); // Clear input
                })
            }

        })

        // Create New Group
        $('#newGroup').on('click', function() {

            let currentUser = JSON.parse(window.localStorage.getItem('user'));

            // Show the overlay and the form container
            $('#edit-message').addClass('show');
            $('#edit-user-con').addClass('show');

            // Close the form when clicking on the close button
            $(document).on('click', '#close-edit-user', function() {
                $('#edit-message').removeClass('show');
                $('#edit-user-con').removeClass('show');
            });

            $.ajax({

                url: "{{ route('student.shared.users') }}",
                type: 'GET'

            }).then(function(res){

                let studentsOptions = res.filter(student => student.role !== 'admin').map(student =>
                    `<label>
                        <input id='specialInput' type="checkbox" name="students[]" value="${student.id}" />
                        ${student.name}
                    </label>`
                ).join('');

                let Form = `
                        <div id='edit-user-con' class="edit-message-content show">
                            <div class="head">
                                <h3>New Group</h3>
                                <span id='close-edit-user'>x</span>
                            </div>
                            <div class="serrors">
                                <!-- <h3>Error</h3> -->
                            </div>
                            <div class="form">
                                <form action="" id='createGroup'>
                                    <div>
                                        <label>Group Name:</label>
                                        <input type="text" name='group_name' id='groupName'>
                                    </div>
                                    <div class="dropdown">
                                        <button type='button' id='openSelection'>Select Studnets</button>
                                        <div class="dropdown-content hidden">
                                            ${studentsOptions}
                                        </div>
                                    </div>
                                    <button type="submit" class='edit-submit'>Create</button>
                                </form>
                            </div>
                        </div>
                    `;

                    // Display the form
                $('#edit-message').html(Form);

                // Open Selection
                $('#openSelection').on('click',function(e) {

                    e.preventDefault();
                    if($('.dropdown-content').hasClass('hidden'))
                        $('.dropdown-content').removeClass('hidden');
                    else
                        $('.dropdown-content').addClass('hidden');
                })

            }).fail(function(res) {
                alert('Failed to load students or subjects');
            })
        });

        // Create Group Functionality
        $(document).on('submit', '#createGroup', function(e) {

            e.preventDefault();

            // Collect selected students
            let selectedStudents = [];

            $('input[name="students[]"]:checked').each(function () {
                selectedStudents.push($(this).val());
            });

            selectedStudents.push(1);  // admin

            selectedStudents.push(currentUser.id); // Current User

            let groupName = $('#groupName').val();

            // Prepare data to send
            let data = {
                students: selectedStudents,
                group_name: groupName
            };

            $.ajax({

                url: "{{ route('student.groups.store') }}",
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }

            }).then(function(){

                $('#edit-message').removeClass('show');

                $('#edit-user-con').removeClass('show');

                alert('Group Created Successfully!');

            }).fail(function(res) {

                alert(res);
            })

        });

    })


</script>
