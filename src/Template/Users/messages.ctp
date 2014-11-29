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
                        <input type="text" id="chat-input" autocomplete="off" placeholder="type here..."/>
                        <ul class="input-group-addon">
                            <li>
                        <span class="pull-right">
                            <input type="submit" id="send-button" href="#" class="btn btn-default btn-sm" value="Send">
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

        viech.register(<?= \App\Model\Entity\Notification::TYPE_NEW_MESSAGE ?>, function() {
            updateConversationsBox();
        })

        viech.register(<?= \App\Model\Entity\Notification::TYPE_NEW_MESSAGE ?>, function() {
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

            $("#chat-input").click( function() {
                clearNotifications(conversationId);
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
        $.getJSON( "/conversations/listAll", function( response ) {
            var conversations = response.conversations;

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
                var showNotifyTooltip = false;

                /**
                 * create title
                 **/
                $.each(conversation.withUsers, function(index, user) {
                    users.push(user.username);
                } )
                var usernames = users.join(",");

                /**
                 * check if conversation got new messages
                **/
                $.each(response.notifications, function(index, notification) {
                    if(notification.type == <?= \App\Model\Entity\Notification::TYPE_NEW_MESSAGE ?>) {
                        var content = $.parseJSON(notification.content);

                        if( content.conversation_id == conversation.id ) {
                            showNotifyTooltip = true;
                        }


                    }
                });

                addBoxNewsElement(
                    conversation.id,
                    'conversations',
                    usernames,
                    conversation.lastMessage.content,
                    showNotifyTooltip,
                    'updateChatBox(' + conversation.id + '); clearNotifications("' + conversation.id + '");'
                );
            });
        });
    }

    function clearNotifications(conversationId) {
        $.getJSON( "/conversations/clearNotifications/" + conversationId, function( response ) {
            if(response == true) {
                $('#notification-for-conversation-' + conversationId).text('');
            }
        });
    }

    /**
     * add a news element to a box container
     * @param elementId the id of the container
     * @param title the title of the element
     * @param text the element text
     * @param titleOnClick here you can pass an onclick-javascript event as string (ugly)
     * @returns {*|jQuery|HTMLElement} the created element
     */
    function addBoxNewsElement(conversationId, elementId, title, text, notificate, titleOnClick) {
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

        var notificationSpan = $('<span/>', {
            id : 'notification-for-conversation-' + conversationId,
            class : 'label label-dark-red pull-right',
            text : notificate ? '!' : ''
        });

        titleDiv.append("<a href='#' onclick='" + titleOnClick + "'>" + title + "</a>");
        titleDiv.append(notificationSpan);

        var textDiv = $("<div/>", {
            "class" : 'news-text',
            "text" : text
        });

        var newsContainer = $('#'+elementId);

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
