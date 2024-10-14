<div class="chat animated fadeInDown">
    <div class="left-menu">
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
                <input type="text" placeholder="Type Message" />
                <button >Send</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(function() {

        getUsers();

        // Get Users
        function getUsers() {

            const contactsList = $('.names');

            // Getting Students From Database
            $.ajax({

                url: "{{ route('admin.students.index') }}",
                type: 'GET'

            }).then(function(contacts) {

                contacts.forEach(contact => {
                    // const statusClass = contact.status === 0 ? "sign" : "sign active";
                    // const statusText = contact.status === 0 ? "Offline" : "Online";

                    // <p class="status">
                    //     <span class="${statusClass}"></span> ${statusText}
                    // </p>

                    const contactItem = $(`
                        <li data-id=${contact.id} id='contact'>
                            <div class="field">
                                <div class="icon">
                                    <img src="{{ url('images/pic.jpg') }}" alt="img">
                                </div>
                                <div class="texts">
                                    <p class="chat-name">${contact.name} (${contact.role})</p>

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

            const head = $('.head');

            const chatName = $('#content');

            const messageHolder = $('#messageHolder');

            let theMessage;

            const currentUser = JSON.parse(window.localStorage.getItem('user'));

            let currentContact = $(this).data('id');

            // Open Chatting
            $('#holder').removeClass('hidden');

            // Chat Name On Header ...Later

            // Get Messages
            $.ajax({

                url: "{{ route('admin.message.index', '') }}/" + currentContact,
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
        })

        // Send Message Functionality



    })


</script>
