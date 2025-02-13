<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chatbot AI</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #343a40;
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
            background-color: #007bff;
            color: white;
            align-self: flex-end;
            text-align: right;
        }

        .bot-message {
            background-color: #e9ecef;
            align-self: flex-start;
        }

        .input-group {
            margin-top: 10px;
        }

        .session-bubble {
            display: inline-flex;
            align-items: center;
            background-color: #343a40;
            color: white;
            padding: 10px 15px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            position: relative;
            max-width: 250px;
            word-wrap: break-word;
        }

        .session-bubble:hover {
            background-color: rgb(123, 139, 155);
        }

        .delete-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            display: none;
            color: white;
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
            background: #f8f9fa;
        }

        .chat-input {
            background: #fff;
            padding: 8px;
            border-radius: 25px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
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
    </style>
</head>
<body>

<div class="sidebar">
    <button class="btn btn-light w-100 mb-3" onclick="startNewChat()">Chat Baru</button>
    <h4>Riwayat Chat</h4>
    <ul id="sessionList" class="list-group"></ul>
</div>

<div class="chat-container">
    <h4 class="text-center">Chatbot AI</h4>
    <div class="chat-box d-flex flex-column" id="chatbox"></div>
    <div class="chat-input-container">
        <div id="chatForm" enctype="multipart/form-data" class="chat-input d-flex align-items-center">
            <div class="input-container d-flex align-items-center flex-grow-1">
                <input type="text" id="message" class="form-control" placeholder="Ketik pesan..." />
                <!-- <label for="fileInput" class="upload-btn">
                    📎
                    <input type="file" id="fileInput" class="d-none" accept="image/*, .pdf, .doc, .docx, .txt" />
                </label> -->
            </div>
            <button class="btn btn-default" onclick="sendMessage()">➤</button>
        </div>
    </div>
</div>

<script>
    let sessionId = localStorage.getItem('chat_session') || '';
    console.log(sessionId);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    function startNewChat() {
        $.post('/chat/start')
        .done(function(data) {
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

        $('#chatbox').append('<div class="message user-message">' + message + '</div>');
        $('#message').val('');
        var loadingIndicator = $('<div class="message bot-message loading">Bot sedang mengetik...</div>');
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

    function loadChatHistory(sessionId) {
        $.get(`/chat/history/${sessionId}`)
            .done(function (data) {
                $('#chatbox').html('');

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

                data.forEach(session => {
                    let sessionBubble = $('<div>')
                        .addClass('session-bubble')
                        .attr('data-session-id', session.session_id);

                    let sessionText = $('<span>').text(session.title);
                    let deleteButton = $('<span>')
                        .addClass('delete-btn')
                        .text('X')
                        .click(function (event) {
                            event.stopPropagation(); // Mencegah klik pada bubble session
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
                                    // console.log(`Session dengan ID ${sessionId} dihapus.`);
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
                    });

                    sessionList.append(sessionBubble);
                });
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                console.error("Error saat mengambil sesi chat:", textStatus, errorThrown);
            });
    }

    $(document).ready(function() {
        sessionId = "";
        if (!sessionId) startNewChat();
        loadSessions();
    });
</script>

</body>
</html>
