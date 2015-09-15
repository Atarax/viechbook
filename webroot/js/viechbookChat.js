ViechbookChat = function() {

    /**
     *
     * @param conversationId
     */
    this.toggleMinimizeMaximizeWindow = function(conversationId) {
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
    };

    /**
     *
     * @param text
     * @returns {*}
     */
    this.replaceEmojis = function(text) {
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
    };

    /**
     *
     * @param userId
     */
    this.openConversationWindowByUser = function(userId) {
        var that = this;

        $.getJSON( "/conversations/get_or_create_by_user/" + userId, function( response ) {
            /** open new window */
            that.openConversationWindow(response.conversation_id, response.conversation_name);
            /** update conversations */
            that.updateConversationsBox();
        });
    };

    /**
     *
     * @param conversationId
     */
    this.updateChatBox = function( conversationId ) {
        var that = this;

        $.getJSON( "/messages/get_by_conversation/" + conversationId, function( messages ) {
            var chatWindow = $("#chat-conversation-window-" + conversationId);
            var chatBox = chatWindow.children(".chat-conversation-window-message-containter");

            chatBox.html("");
            var foreignParticipants = [];

            // display messages
            $.each(messages, function (index, message) {
                var areWeSender = message.user_id == VIECHBOOK.currentUser.id;
                var bubbleClass = areWeSender ? "chat-message-bubble-right" : "chat-message-bubble-left";
                var bubble = $("<div/>", {
                    "class" : "chat-message-bubble " + bubbleClass
                });

                var escaped = $("<div/>").text(message.content).html();
                var withEmojisReplaced = that.replaceEmojis(escaped);

                bubble.html( withEmojisReplaced );
                chatBox.append(bubble);
            });

            chatBox.scrollTop( chatBox.prop("scrollHeight") );
        });
    };

    /**
     *
     */
    this.updateConversationsBox = function() {
        var that = this;

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

                that.addChatConversationElement(
                    conversation.id,
                    peopleListId,
                    usernames,
                    showNotifyTooltip
                );
            });
        });
    };

    /**
     * add a news element to a box container
     * @param conversationId
     * @param elementId the id of the container
     * @param title the title of the element
     * @returns {*|jQuery|HTMLElement} the created element
     */
    this.addChatConversationElement = function(conversationId, elementId, title, showNotifyBadge) {
        var that = this;

        var li = $("<li>", {
            role : 'presentation',
            class : 'chat-conversation-element'
        });

        var button = $("<a>", {
            text: title
        });

        button.click( function() {
                that.openConversationWindow(conversationId,title);
        });

        li.append(button);

        if(showNotifyBadge){
            li.children().append("<span class='badge'>!</span>");
        }

        var newsContainer = $('#'+elementId);
        newsContainer.append(li);
    };

    /**
     *
     * @param conversationId
     */
    this.closeConversationWindow = function(conversationId) {
        var conversationWindow = $("#chat-conversation-window-" + conversationId);
        conversationWindow.hide();
        $('.chat-message-editor-textarea').focus();

        /** notify server */
        $.getJSON('/conversations/close_conversation_window/' + conversationId );
    };

    /**
     *
     * @param conversationId
     */
    this.sendMessage = function(conversationId) {
        var that = this;
        var chatInput = $("#chat-message-conversation-" + conversationId);
        var content = chatInput.val();
        chatInput.val("");

        $.post("/conversations/add_message/" + conversationId,{
            content: content
        }, function() {
            that.updateChatBox(conversationId);
        });
    };


    this.openConversationWindow = function(conversationId, title, notifyServer) {
        var that = this;

        /** default value */
        if(notifyServer == null) {
            notifyServer = true;
        }
        /** first check if the window is not already open */
        var found = false;

        $('.chat-conversation-window').each(function(index, element) {
            element = $(element);

            var id = element.attr('id');
            var foundId = 'chat-conversation-window-' + conversationId;

            if(id == foundId) {
                /** check if minimized */
                if( element.hasClass('chat-conversation-window-closed') ) {
                    /** then maximize */
                    that.toggleMinimizeMaximizeWindow(conversationId);
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
            that.closeConversationWindow(conversationId);
        });

        /** make click on head minimize the window */
        var headline = dummy.children('.chat-conversation-window-headline');
        headline.on('click', function() {
            console.debug('gooo');
            that.toggleMinimizeMaximizeWindow(conversationId);
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
                that.sendMessage(conversationId);
            }
        });

        /** esc somehow only works on keydown */
        dummy.on('keydown', function(event) {
            var charCode = (typeof event.which === "number") ? event.which : event.keyCode;

            /** escape - close window */
            if(charCode == 27){
                that.closeConversationWindow(conversationId);
            }
        });

        textarea.on('focus', function(){
            clearNotifications(conversationId);
        });

        /** set the conversation-title */
        dummy.children('.chat-conversation-window-headline').html(title);

        /** load messages etc */
        this.updateChatBox(conversationId);

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
};

