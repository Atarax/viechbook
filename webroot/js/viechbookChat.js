function toggleMinimizeMaximizeWindow(conversationId) {
    var conversationWindow = $('#chat-conversation-window-' + conversationId);
    console.debug(conversationWindow);
    /** check if open or closed */
    if( conversationWindow.hasClass('chat-conversation-window-closed') ) {
        /** open */
        conversationWindow.addClass('chat-conversation-window');
        conversationWindow.removeClass('chat-conversation-window-closed');
    }
    else {
        /** close */
        conversationWindow.addClass('chat-conversation-window-closed');
        conversationWindow.removeClass('chat-conversation-window');
    }
}

function replaceEmojis(text) {
    for(var i in EMOJI_MAPPING) {
        var emojiKey = EMOJI_MAPPING[i][0];
        var emojiCode = EMOJI_MAPPING[i][1];
        var emojiSpan = '<span class="emoji ' + emojiCode + '"></span>';

        /** to be perfect we check the beginning and the end separately and only replace smileys surrounded by whitespaces */
        if(text.substring(0, emojiKey.length) == emojiKey) {
            text = emojiSpan + text.substring(emojiKey.length - 1, text.length - emojiKey.length);
        }
        /** the end-check */
        var endOffset = text.length - emojiKey.length - 2;

        if(text.substring(endOffset, endOffset + emojiKey.length + 1) == ' ' + emojiKey) {
            text = text.substring(0, text.length - emojiKey.length - 1) + emojiSpan;
        }

        text = text.replace(' ' + emojiKey + ' ', emojiSpan)
    }
    return text;
}

function openConversationWindowByUser(userId) {
    $.getJSON( "/conversations/get_or_create_by_user/" + userId, function( response ) {
        openConversationWindow(response.conversation_id, response.conversation_name);
        updateConversationsBox();
    });
}

function updateChatBox( conversationId ) {
    $.getJSON( "/messages/get_by_conversation/" + conversationId, function( messages ) {
        var chatWindow = $("#chat-conversation-window-" + conversationId);
        var chatBox = chatWindow.children(".chat-conversation-window-message-containter");

        chatBox.html("");
        var foreignParticipants = [];
        /*
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
         */
        /*
         $("#chat-input").focus( function() {
         clearNotifications(conversationId);
         });*/

        // set title
        /*
         $.getJSON( "/conversations/get_participants/" + conversationId, function( users ) {
         $.each( users, function(index, user) {
         if( user.id != <?php // echo  $currentUser['id'] ?> ) {
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
         */

    // display messages
    $.each(messages, function (index, message) {
        var areWeSender = message.user_id == VIECHBOOK.currentUser.id;
            var bubbleClass = areWeSender ? "chat-message-bubble-right" : "chat-message-bubble-left";
            var bubble = $("<div/>", {
                "class" : "chat-message-bubble " + bubbleClass
            });
            /*
            if(!areWeSender) { // if not we are sender ! xD
                var avatarDiv = $("<div/>", {
                    "class" : "avatar"
                });
                var avatarImg = $("<img/>", {
                    "class" : "avatar-small",
                    "src" : "/img/avatar1.jpg"
                });
                li.append(avatarDiv);
                avatarDiv.append(avatarImg);
            }
            */
            /*
            var infoDiv = $("<div/>", {
                "class": "info"
            });
            var nameSpan = $("<span/>", {
                "class": "name",
                "html": "<strong>" + message.user.username + "</strong>"
            });
            var timeSpan = $("<span/>", {
                "class": "time",
                "text":  jQuery.timeago( message.created )
            });
            */
            /*
            nameSpan.append(timeSpan);
            infoDiv.append(nameSpan);
            bubble.append(infoDiv);
            */
            var escaped = $("<div/>").text(message.content).html();
            var withEmojisReplaced = replaceEmojis(escaped);

            bubble.html( withEmojisReplaced );
            chatBox.append(bubble);
        });

        chatBox.scrollTop( chatBox.prop("scrollHeight") );
    });
}

function updateConversationsBox() {
    $.getJSON( "/conversations/list_all", function( response ) {
        var conversations = response.conversations;
        var peopleListId = "chat-people-list";
        var conversationsContainer = $("#" + peopleListId);

        conversationsContainer.html('');

        $.each(conversations, function( index, conversation) {
            var users = [];
            var showNotifyTooltip = false;
            /**
             * create title
             **/
            $.each(conversation.withUsers, function(index, user) {
                users.push(user.username);
            } );
            var usernames = users.join(",");

            /**
             * check if conversation got new messages
             **/
            $.each(response.notifications, function(index, notification) {
                if(notification.type == 1) {
                    var content = $.parseJSON(notification.content);

                    if( content.conversation_id == conversation.id ) {
                        showNotifyTooltip = true;
                    }
                }
            });

            addChatConversation(
                conversation.id,
                peopleListId,
                usernames,
                showNotifyTooltip
            );
        });
    });
}

function clearNotifications(conversationId) {
    $.getJSON( "/conversations/clear_notifications/" + conversationId, function( response ) {
        if(response == true) {
            $('#notification-for-conversation-' + conversationId).text('');
        }
    });

    updateConversationsBox()
}
/**
 * add a news element to a box container
 * @param conversationId
 * @param elementId the id of the container
 * @param title the title of the element
 * @returns {*|jQuery|HTMLElement} the created element
 */
function addChatConversation(conversationId, elementId, title, showNotifyBadge) {
    var li = $("<li/>", {
        "role" : 'presentation'
    });

    li.append("<a href='#' onclick='openConversationWindow(" + conversationId + ",\"" + title + "\")'>" + title + "</a>");

        if(showNotifyBadge){
            li.children().append("<span class='badge'>!</span>");
        }

        var newsContainer = $('#'+elementId);
        newsContainer.append(li);
    }

function closeConversationWindow(conversationId) {
    var conversationWindow = $("#chat-conversation-window-" + conversationId);
    conversationWindow.hide();
    $('.chat-message-editor-textarea').focus();

    /** notify server */
    $.getJSON('/conversations/close_conversation_window/' + conversationId );
}

function sendMessage(conversationId) {
    var chatInput = $("#chat-message-conversation-" + conversationId);
    var content = chatInput.val();
    chatInput.val("");

    $.post("/conversations/add_message/" + conversationId,{
        content: content
    }, function() {
        updateChatBox(conversationId);
    });
}

function openConversationWindow(conversationId, title, notifyServer) {
    /** default value */
    if(notifyServer == null) {
        notifyServer = true;
    }

    /** first check if the window is not already open */
    var found = false;

    $('.chat-conversation-window,.chat-conversation-window-closed').each(function(index, element) {
        element = $(element);

        var id = element.attr('id');
        var foundId = 'chat-conversation-window-' + conversationId;

        if(id == foundId) {
            /** check if minimized */
            if( element.hasClass('chat-conversation-window-closed') ) {
                /** then maximize */
                toggleMinimizeMaximizeWindow(conversationId);
            }

            /** already open, so show and set the focus on the textfield */
            $("#chat-conversation-window-" + conversationId).show();
            $("#chat-message-conversation-" + conversationId).focus();
            found = true;
        }

        /** exit foreach loop */
        return true;
    });

    if(found) {
        /** nothing to to anymore */
        return
    }

    /** create new chat-window */
    var dummy = $('#chat-conversation-window-dummy').clone();

    /** set the values of the dummy correctly */
    dummy.attr('id', 'chat-conversation-window-' + conversationId);

    /** construct the close-button-logic */
    var closeButton = dummy.children('.chat-conversation-window-close-button');

    closeButton.click(function() {
        closeConversationWindow(conversationId);
    });

    /** make click on head minimize the window */
    var headline = dummy.children('.chat-conversation-window-headline');
    headline.on('click', function() {
        console.debug('gooo');
        toggleMinimizeMaximizeWindow(conversationId);
    });


    /** set the correct id of the textarea, so we can identify the message-content later */
    var newTextAreaId = ("chat-message-conversation-" + conversationId);
    var textarea = dummy.children('.chat-conversation-window-bottom-bar').children('textarea');
    textarea = $(textarea[0]);

    textarea.attr('id', newTextAreaId);

    /** and also the callback to the sendMessage function */
    textarea.keyup( function(event) {
        var charCode = (typeof event.which === "number") ? event.which : event.keyCode;

        /** 13 -> enter key */
        if(charCode == 13) {
            sendMessage(conversationId);
        }
    });

    /** esc somehow only works on keydown */
    dummy.on('keydown', function(event) {
        var charCode = (typeof event.which === "number") ? event.which : event.keyCode;

        /** escape - close window */
        if(charCode == 27){
            closeConversationWindow(conversationId);
        }
    });

    textarea.on('focus', function(){
        clearNotifications(conversationId);
    });

    /** set the conversation-title */
    dummy.children('.chat-conversation-window-headline').html(title);

    /** load messages etc */
    updateChatBox(conversationId);

    /** add it to the chat-window-container */
    var container = $("#chat-window-containter");
    container.append(dummy);

    /** show-time and focus */
    dummy.show();
    $("#chat-message-conversation-" + conversationId).focus();

    /** notify server if necessary */
    if(notifyServer) {
        $.getJSON('/conversations/open_conversation_window/' + conversationId );
    }


}
