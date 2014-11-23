<div class="area-top clearfix">
    <div class="pull-left header">
        <h3 class="title">
            <i class="icon-envelope"></i>
            Messages
        </h3>
    </div>
</div>

<div class="row">

<div class="col-md-4">
    <div class="container padded">
        <div class="input-group addon-left">
            <a class="input-group-addon" href="#">
                <i class="icon-search"></i>
            </a>
            <input type="text" placeholder="Search Conversations...">
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <span class="title">Conversations</span>
        </div>
        <div id="conversations" class="box-content scrollable" style="height: 424px; overflow-y: auto"> </div>
    </div>
</div>

<div class="col-md-8">

    <!-- find me in partials/small_chat -->
    <div class="container padded">
        <div class="box">
        <div class="box-header">
            <div class="title" id="chat-box-title">Choose Conversation</div>
        </div>

        <div id="chat-box-div" class="box-content" style="overflow-y: auto; height: 410px;">
            <ul class="chat-box" id="chat-box">
            </ul>
        </div>

        <div class="box-footer flat padded">
            <div class="input-group addon-right">
                <input type="text" id="chat-input" placeholder="type here..."/>
                <ul class="input-group-addon">
                    <li>
                        <span class="pull-right">
                            <form>
                                <input type="submit" id="send-button" href="#" class="btn btn-default btn-sm" value="Send">
                            </form>
                        </span>
                     <!--   <button id="chat-send-button" class="btn btn-blue">Send</button> -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        updateConversationsBox();

        viech.register('newmessages', function() {
            updateConversationsBox();
        })

        viech.register('newmessages', function() {
            if(currentConversationId != null) {
                updateChatBox(currentConversationId);
            }
        })
    });

    function updateChatBox( conversationId ) {
        currentConversationId = conversationId;
        $.getJSON( "/messages/getByConversation/" + conversationId, function( messages ) {
            var chatBox = $("#chat-box");
            chatBox.html("");

            var foreignParticipants = [];

            $("#send-button").off();
            $("#send-button").click( function() {
               sendMessage(conversationId);
            });

            $("#chat-input").off();
            $("#chat-input").keyup( function(event) {
                if(event.keyCode == 13) {
                    sendMessage(conversationId);
                }
            });

            // set title
            $.getJSON( "/conversations/getParticipants/" + conversationId, function( users ) {
                $.each( users, function(index, user) {
                    if( user.id != <?= $currentUser['id'] ?> ) {
                        foreignParticipants.push(user.username);
                    }
                });
                if( foreignParticipants.length > 0 ) {
                    $("#chat-box-title").text("Chat with " + foreignParticipants.join(","));
                }
                else {
                    $("#chat-box-title").text("Chat");
                }
            });


            // display messages
            $.each(messages, function (index, message) {
                var areWeSender = message.user_id == <?= $currentUser['id'] ?>;
                var liClass = areWeSender ? "arrow-box-left" : "arrow-box-right gray"

                var li = $("<li/>", {
                    "class" : liClass
                });

                var avatarDiv = $("<div/>", {
                    "class" : "avatar"
                });

                var avatarImg = $("<img/>", {
                    "class" : "avatar-small",
                    "src" : "/img/avatar1.jpg"
                });

                li.append(avatarDiv);
                avatarDiv.append(avatarImg);

                var infoDiv = $("<div/>", {
                    "class": "info"
                });

                var nameSpan = $("<span/>", {
                    "class": "name",
                    "html": "<strong>" + message.user.username + "</strong>"
                });

                infoDiv.append(nameSpan);

                li.append(infoDiv);

                var escaped = $("<div/>").text(message.content).html();
                infoDiv.after( escaped );


                chatBox.append(li);
            });

            var chatBoxDiv = chatBox.parent();
            chatBoxDiv.scrollTop( chatBoxDiv.prop("scrollHeight") );
        });
    }

    var currentConversationId = null;

    function updateConversationsBox() {
        $.getJSON( "/conversations/listAll", function( conversations ) {
            if(conversations.length == 0) {
                addBoxNewsElement(
                    'conversations',
                    'No Conversations :(',
                    'Maybe you should talk to someone :P'
                );
            }

            var conversationsContainer = $("#conversations");
            conversationsContainer.html('');

            $.each(conversations, function( index, conversation) {
                var users = [];

                $.each(conversation.withUsers, function(index, user) {
                    users.push(user.username);
                } )
                var usernames = users.join(",");

                addBoxNewsElement(
                    'conversations',
                    usernames + ' (' + conversation.unreadMessageCount + ')',
                    conversation.lastMessage.content,
                    'updateChatBox("' + conversation.id + '");'
                );

            });
        });
    }


    /**
     * add a news element to a box container
     * @param id the id of the container
     * @param title the title of the element
     * @param text the element text
     * @param titleOnClick here you can pass an onclick-javascript event as string (ugly)
     * @returns {*|jQuery|HTMLElement} the created element
     */
    function addBoxNewsElement(id, title, text, titleOnClick) {
        if(titleOnClick == undefined) titleOnClick = '';

        var sectionDiv = $("<div/>", {
            "class" : 'box-section'
        });

        var contentDiv = $("<div/>", {
            "class" : 'box-content'
        });

        var titleDiv = $("<div/>", {
            "class" : 'news-title'
        });

        titleDiv.append("<a href='#' onclick='" + titleOnClick + "'>" + title + "</a>");

        var textDiv = $("<div/>", {
            "class" : 'news-text',
            "text" : text
        });

        var newsContainer = $('#'+id);

        contentDiv.append(titleDiv);
        contentDiv.append(textDiv);
        sectionDiv.append(contentDiv);
        newsContainer.append(sectionDiv);

        return sectionDiv;
    }

    function sendMessage(conversationId) {
        var content = $("#chat-input").val();

        $.post("/conversations/addMessage/" + conversationId,{
                content: content
            }, function() {
                updateChatBox(conversationId);
                $("#chat-input").val("");
        });
    }
</script>
