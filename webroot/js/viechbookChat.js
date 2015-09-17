ViechbookChat = function() {

    /**
     *
     * @param conversationId
     */
    this.toggleMinimizeMaximizeWindow = function(conversationId) {
        var conversationWindow = $('#chat-conversation-window-' + conversationId);

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
        text = $("<div/>").html(text).text();

        for(var i in EMOJI_MAPPING) {
            var emojiKey = EMOJI_MAPPING[i][0];
            var emojiCode = EMOJI_MAPPING[i][1];
            var emojiSpan = '<span class="emoji ' + emojiCode + '"></span>';

            /** to be perfect we check the beginning and the end separately and only replace smileys surrounded by whitespaces */
            if(text.substring(0, emojiKey.length) == emojiKey) {
                text = emojiSpan + text.substring(emojiKey.length , text.length);
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
                    showNotifyTooltip,
                    conversation.isOnline
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
    this.addChatConversationElement = function(conversationId, elementId, title, showNotifyBadge, isOnline) {
        var that = this;

        var li = $("<li>", {
            role : 'presentation',
            class : 'chat-conversation-element'
        });

        var img = $("<img>", {
            src : '/img/phi.jpg',
            class: 'chat-profile-picture'
        });

        var button = $("<a>");

        button.click( function() {
                that.openConversationWindow(conversationId,title);
        });

        button.append(img);

        var textDiv = $('<div>', {
            class: 'conversation-element-text',
            html: title
        });

        button.append(textDiv);

        var onlineStatusDiv = $('<div>', {
           class: 'conversation-element-online-status'
        });

        if(isOnline) {
            button.append(onlineStatusDiv);
        }

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
            that.updateConversationsBox();
        });
    };


    function createNewConversationWindow(conversationId, that, title) {
        /** create new chat-window */
        var newWindow = $('<div>', {
            class: 'panel panel-default chat-conversation-window chat-conversation-window-open',
            id: 'chat-conversation-window-' + conversationId
        });

        /** construct the close-button-logic */
        var closeButton = $('<div>', {
            class: 'glyphicon glyphicon-remove-sign chat-conversation-window-close-button',
            id: 'chat-conversation-window-close-button-' + conversationId
        });

        closeButton.click(function () {
            that.closeConversationWindow(conversationId);
        });

        newWindow.append(closeButton);

        /** make click on head minimize the window */
        var headline = $('<div>', {
            id: 'chat-conversation-window-headline-' + conversationId,
            class: 'panel-heading chat-conversation-window-headline',
            html: title
        });
        headline.on('click', function () {
            that.toggleMinimizeMaximizeWindow(conversationId);
        });

        newWindow.append(headline);

        container = $('<div>', {
            id: 'chat-conversation-window-message-container' + conversationId,
            class: 'panel-body chat-conversation-window-message-containter'
        });
        newWindow.append(container);

        var breadcrumb = $('<div>', {
            class: 'chat-conversation-window-breadcrumb'
        });
        newWindow.append(breadcrumb);

        var bottomBar = $('<div>', {
            id: 'chat-conversation-window-bottom-bar-' + conversationId,
            class: 'chat-conversation-window-bottom-bar'
        });
        newWindow.append(bottomBar);

        /** set the correct id of the textarea, so we can identify the message-content later */
        var textarea = $('<textarea>', {
            id: 'chat-message-conversation-' + conversationId,
            class: 'chat-dummy-textarea chat-message-editor-textarea'
        });
        bottomBar.append(textarea);

        /** and also the callback to the sendMessage function */
        textarea.keyup(function (event) {
            var charCode = (typeof event.which === "number") ? event.which : event.keyCode;

            /** 13 -> enter key */
            if (charCode == 13) {
                that.sendMessage(conversationId);
            }
        });

        /** esc somehow only works on keydown */
        newWindow.on('keydown', function (event) {
            var charCode = (typeof event.which === "number") ? event.which : event.keyCode;

            /** escape - close window */
            if (charCode == 27) {
                that.closeConversationWindow(conversationId);
            }
        });

        textarea.on('focus', function () {
            clearNotifications(conversationId);
        });

        /** add it to the chat-window-container */
        var container = $("#chat-window-containter");
        container.append(newWindow);

        return newWindow;
    }

    this.openConversationWindow = function(conversationId, title, notifyServer) {
        var that = this;

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

        /** window not open already, build a new one */
        var newWindow = createNewConversationWindow(conversationId, that, title);

        /** load messages etc */
        this.updateChatBox(conversationId);

        /** show-time and focus */
        newWindow.show();
        $("#chat-message-conversation-" + conversationId).focus();

        /** notify server if necessary */
        if(notifyServer) {
            $.getJSON('/conversations/open_conversation_window/' + conversationId );
        }
    }
};

