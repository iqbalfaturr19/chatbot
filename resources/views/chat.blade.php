<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chatbot AI</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
    <style>
        body {
            background-color: #ffffff;
            display: flex;
        }

        .chat-header {
            display: flex;
            align-items: center;
            gap: 120px;
            padding: 0px 10px;
        }

        
        .chat-new-btn {
            width: 40px;
            height: 40px;
            border-radius: 20%;
            border: none;
            background-color: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
            color: #6e757a;
        }

        .chat-new-btn:hover {
            background-color: #f3f4f6;
        }

        .chat-new-btn:active {
            background-color: #e5e7eb;
        }

        .chat-new-btn i {
            font-size: 18px;
            color: #6b7280;
        }

        .chat-user-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
        }


        .sidebar {
            width: 250px;
            background: #f9f9f9;
            color: white;
            padding: 20px;
            height: 100vh;
            overflow-y: auto;
        }

        .chat-container {
            flex: 1;
            padding: 20px;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 10px;
        }
        
        .message {
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            max-width: 80%;
        }

        .user-message {
            background-color: #f9f9f9;
            color: #312735;
            align-self: flex-end;
            text-align: right;
        }

        .bot-message {
            background-color: #ffffff;
            align-self: flex-start;
        }

        .input-group {
            margin-top: 10px;
        }

        .session-bubble {
            display: inline-flex;
            align-items: center;
            background-color: #f9f9f9;
            color: black;
            padding: 10px 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: relative;
            max-width: 250px;
            word-wrap: break-word;
            font-size: 14px;
        }

        .session-bubble:hover {
            background: rgb(230, 230, 235);
        }

        .session-date-header{
            color:#312735;
            margin-left:5px;
            margin-top: 20px;
            font-weight: bold;
            font-size:12px;
        }

        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
            color: black;
            border-radius: 50%;
            padding: 2px 5px;
            font-size: 10px;
            line-height: 12px;
            text-align: center;
            cursor: pointer;
        }

        .session-bubble:hover .delete-btn {
            display: inline-block;
        }


        .chat-input-container {
            display: flex;
            flex-direction: column;
            justify-content: center; /* Pusat vertikal */
            align-items: center; /* Pusat horizontal */
            background: #ffffff;
        }

        .input-container {
            display: flex;
            align-items: center;
            background: #f1f1f1;
            border-radius: 20px;
            padding: 5px 15px;
            flex-grow: 1;
        }

        #message {
            border: none;
            background: transparent;
            flex-grow: 1;
            outline: none;
        }

        .upload-btn {
            cursor: pointer;
            font-size: 18px;
            margin-left: 10px;
        }

        button {
            margin-left: 10px;
            border-radius: 50%;
            padding: 8px 12px;
        }

        #chat-input {
            align-items: center;
            background-color: #fff;
            padding: 10px;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            border: 1px solid #ccc;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        #chat-input input[type="text"] {
            /* border: none;
            outline: none; */
            font-size: 16px;
            padding: 10px;
            width: 100%;
            color: black;
            margin-bottom: 15px;
        }

        #chat-input .attachment-icon {
            margin-left: 10px;
            cursor: pointer;
        }

        .chat-image {
            max-width: 200px;
            border-radius: 10px;
            margin-top: 5px;
        }
        /* Container untuk menata tombol */
        .button-container {
            display: flex;
            align-items: center;
            gap: 475px; /* Jarak antar tombol */
        }

        /* Styling tombol upload */
        #uploadButton {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            color: #6b7280;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
        }

        /* Hover dan efek klik */
        #uploadButton:hover {
            background-color: #f3f4f6;
        }

        #uploadButton:active {
            background-color: #e5e7eb;
        }

        /* Styling tombol kirim */
        #sendButton {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background-color: black;
            color: white;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s, box-shadow 0.2s;
        }

        /* Hover dan efek klik */
        #sendButton:hover {
            background-color: #333;
        }

        #sendButton:active {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="chat-header">
        <img src="{{ asset('images/logoBBG.png') }}" alt="User Icon" class="chat-user-img">
        <button class="chat-new-btn" onclick="startNewChat()">
            <i class="fa-solid fa-pen-to-square"></i>
        </button>
    </div>
    <ul id="sessionList" class="list-group"></ul>
</div>

<div class="chat-container">
    <h4 class="" style="color:#6e757a; font-size:20px;">Chatbot AI</h4>
    <div class="chat-box d-flex flex-column" id="chatbox"></div>
    <div class="chat-input-container">
        <div id="chat-input">
            <div class="row">
                <div class="col-md-12">
                    <input type="text" id="message" placeholder="Ketik pesan">
                    <input type="hidden" id="session_id" name="session_id">
                </div>
                <div class="col-md-12">
                    <div class="button-container">
                        <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                        <button id="uploadButton" onclick="document.getElementById('imageUpload').click()">+</button>
                        <button id="sendButton" onclick="sendMessage()">➤</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let sessionId = localStorage.getItem('chat_session') || '';
    
    // console.log(sessionId);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function startNewChat() {
        $.post('/chat/start')
        .done(function(data) {
            currentSession = data.session_id;
            sessionId = data.session_id;
            localStorage.setItem('chat_session', sessionId);
            $('#chatbox').html('');
            loadSessions();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error saat memulai chat baru:", textStatus, errorThrown);
        });
    }

    function sendMessage() {
        var message = $('#message').val().trim();
        if (!message) {
            console.warn("Pesan tidak boleh kosong.");
            return;
        }
        var sessionIdCek = $('#session_id').val();
        if (sessionIdCek) {
            sessionId = sessionIdCek;
        }
        // console.log(sessionId);
        $('#chatbox').append('<div class="message user-message">' + message + '</div>');
        $('#message').val('');
        var loadingIndicator = $('<div class="message bot-message loading">Tunggu Sebentar...</div>');
        $('#chatbox').append(loadingIndicator);
        $.post('/chat', {message: message, session_id: sessionId})
        .done(function(data) {
            loadingIndicator.remove();
            $('#chatbox').append('<div class="message bot-message">' + data.response + '</div>');
            $('#message').val('');
            loadSessions();
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            console.error("Error saat mengirim pesan:", textStatus, errorThrown);
        });
    }

    // function sendMessage() {
    //     var message = $('#message').val().trim();
    //     var imageFile = $('#imageUpload')[0].files[0];

    //     if (!message && !imageFile) {
    //         console.warn("Pesan atau gambar harus diisi.");
    //         return;
    //     }

    //     var formData = new FormData();
    //     formData.append('session_id', sessionId);
    //     if (message) {
    //         formData.append('message', message);
    //     }
    //     if (imageFile) {
    //         formData.append('image', imageFile);
    //     }

    //     var chatbox = $('#chatbox');

    //     // 1️⃣ **Tambahkan bubble chat pengguna terlebih dahulu**
    //     if (message) {
    //         chatbox.append('<div class="message user-message">' + message + '</div>');
    //     }

    //     if (imageFile) {
    //         var imageURL = URL.createObjectURL(imageFile);
    //         chatbox.append('<div class="message user-message"><img src="' + imageURL + '" class="chat-image"></div>');
    //     }

    //     $('#message').val('');
    //     $('#imageUpload').val('');

    //     // 2️⃣ **Tambahkan loading indicator SETELAH pesan pengguna**
    //     var loadingIndicator = $('<div class="message bot-message loading">Tunggu Sebentar...</div>');
    //     chatbox.append(loadingIndicator);

    //     $.ajax({
    //         url: '/chat',
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function (data) {
    //             loadingIndicator.remove();
    //             if (data.response) {
    //                 chatbox.append('<div class="message bot-message">' + data.response + '</div>');
    //             }
    //             if (data.image_url) {
    //                 chatbox.append('<div class="message bot-message"><img src="' + data.image_url + '" class="chat-image"></div>');
    //             }
    //             loadSessions();
    //         },
    //         error: function (jqXHR, textStatus, errorThrown) {
    //             console.error("Error saat mengirim pesan:", textStatus, errorThrown);
    //         }
    //     });
    // }


    function loadChatHistory(sessionId) {
        $.get(`/chat/history/${sessionId}`)
            .done(function (data) {
                $('#chatbox').html('');
                
                $('#session_id').val(sessionId);
                // console.log(sessionId);

                data.forEach(chat => {
                    let userMessage = $('<div>').addClass('message user-message').text(chat.user_message);
                    let botResponse = $('<div>').addClass('message bot-message').text(chat.bot_response);
                    $('#chatbox').append(userMessage, botResponse);
                });
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error saat mengambil riwayat chat:", textStatus, errorThrown);
            });
    }

    function loadSessions() {
        $.get('/chat/sessions')
            .done(function (data) {
                if (!Array.isArray(data)) {
                    console.error("Data yang diterima bukan array:", data);
                    return;
                }

                let sessionList = $('#sessionList');
                sessionList.html('');

                let today = new Date();
                let yesterday = new Date();
                yesterday.setDate(today.getDate() - 1);

                let sevenDaysAgo = new Date();
                sevenDaysAgo.setDate(today.getDate() - 7);

                let sessionGroups = {
                    today: [],
                    yesterday: [],
                    last7Days: []
                };

                data.forEach(session => {
                    let sessionDate = new Date(session.created_at);

                    if (sessionDate.toDateString() === today.toDateString()) {
                        sessionGroups.today.push(session);
                    } else if (sessionDate.toDateString() === yesterday.toDateString()) {
                        sessionGroups.yesterday.push(session);
                    } else if (sessionDate >= sevenDaysAgo) {
                        sessionGroups.last7Days.push(session);
                    }
                });

                function appendSessionSection(title, sessions) {
                    if (sessions.length > 0) {
                        sessionList.append(`<div class="session-date-header">${title}</div>`);
                        sessions.forEach(session => {
                            let sessionBubble = $('<div>')
                                .addClass('session-bubble')
                                .attr('data-session-id', session.session_id);

                            let sessionTitleArray = session.title.split(' ');
                            let sessionTitle = sessionTitleArray.slice(0, 4).join(' ');

                            let sessionText = $('<span>').text(sessionTitle);
                            let deleteButton = $('<span>')
                                .addClass('delete-btn')
                                .text('X')
                                .click(function (event) {
                                    event.stopPropagation();
                                    let sessionId = $(this).parent().attr('data-session-id');
                                    let sessionElement = $(this).parent();

                                    if (!confirm("Apakah Anda yakin ingin menghapus sesi ini?")) {
                                        return;
                                    }
                                    $.ajax({
                                        url: '/delete-session',
                                        type: 'POST',
                                        data: { session_id: sessionId },
                                        success: function () {
                                            sessionElement.remove();
                                            localStorage.removeItem('chat_session');
                                            if (currentSession === sessionId) {
                                                $('#chatbox').html('<p style="text-align: center; color: #aaa;">Chat telah dihapus</p>');
                                            }
                                        },
                                        error: function (xhr) {
                                            console.error("Gagal menghapus session:", xhr.responseText);
                                        }
                                    });
                                });

                            sessionBubble.append(sessionText).append(deleteButton);

                            sessionBubble.click(function () {
                                let sessionId = $(this).attr('data-session-id');
                                localStorage.setItem('chat_session', sessionId);
                                loadChatHistory(sessionId);
                                currentSession = sessionId;
                            });

                            sessionList.append(sessionBubble);
                        });
                    }
                }

                appendSessionSection("Hari ini", sessionGroups.today);
                appendSessionSection("Kemarin", sessionGroups.yesterday);
                appendSessionSection("7 Hari Lalu", sessionGroups.last7Days);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error saat mengambil sesi chat:", textStatus, errorThrown);
            });
    }


    $(document).ready(function() {
        // Tombol upload gambar
        $('#uploadButton').click(function () {
            $('#imageUpload').click();
        });
        
        sessionId = "";
        localStorage.removeItem('session_id');
        if (!sessionId) startNewChat();
        loadSessions();
    });
</script>

</body>
</html>
