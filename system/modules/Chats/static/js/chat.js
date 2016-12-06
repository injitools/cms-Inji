inji.Chats = {};
inji.Chats.chats = [];
inji.Chats.get = function (id, params) {
  var chatElement = $('#' + id);
  if (chatElement.data('chats-index') === undefined) {
    inji.Chats.chats.push({
      element: chatElement,
      timer: null,
      chatId: chatElement.data('chat-id'),
      msgCount: chatElement.data('msg-count'),
      perPage: 20,
      lastEventDate: chatElement.data('last-event-date'),
      reverse: params.reverse ? true : false,
      scrollable: true,
      members: {},
      checkoutMembers: function () {
        var members = this.members;
        $.each($('[data-chat_member_status'), function () {
          var el = $(this);
          if (members[el.data('chat_member_status')]) {
            if (el.data('ontext')) {
              el.html(el.data('ontext'));
            }
            if (el.data('onclass')) {
              if (el.data('offclass') && el.hasClass(el.data('offclass'))) {
                el.removeClass(el.data('offclass'));
              }
              el.addClass(el.data('onclass'));
            }
          } else {
            if (el.data('offtext')) {
              el.html(el.data('offtext'));
            }
            if (el.data('offclass')) {
              if (el.data('onclass') && el.hasClass(el.data('onclass'))) {
                el.removeClass(el.data('onclass'));
              }
              el.addClass(el.data('offclass'));
            }
          }
        })
      },
      deleteMsg: function (id, chatId) {
        inji.Server.request({
          url: 'chats/deleteMsg/' + id,
          success: function () {
            $('#chat' + chatId + 'msg' + id).remove();
          }
        });
      },
      banUser: function (msgId, chatId) {
        inji.Server.request({
          url: 'chats/banUser/' + msgId,
          success: function (msgIds) {
            for (var key in msgIds) {
              $('#chat' + chatId + 'msg' + key).remove();
            }
          }
        });
      },
    });
    var index = inji.Chats.chats.length - 1
    chatElement.data('chats-index', index);
    inji.Chats.init(index);
    return inji.Chats.chats[index];
  }
  return inji.Chats.chats[chatElement.data('chats-index')];
}
inji.Chats.init = function (chatIndex) {
  var updater = function () {
    inji.Server.request({
      url: 'chats/events/' + inji.Chats.chats[chatIndex].chatId,
      data: {
        lastEventDate: inji.Chats.chats[chatIndex].lastEventDate
      },
      success: function (data) {
        inji.Chats.chats[chatIndex].members = data.members;
        inji.Chats.chats[chatIndex].checkoutMembers();
        for (key in data.messages) {
          var msg = data.messages[key];
          inji.Chats.chats[chatIndex].lastEventDate = msg.message.chat_message_date_create;
          var template = inji.Chats.chats[chatIndex].element.find('.chats-chat-message-template').html();
          template = template.replace(/message-id/g, msg.message.chat_message_id);
          template = template.replace(/message-userId/g, msg.message.chat_message_user_id);
          template = template.replace(/message-date_create/g, msg.message.chat_message_date_create);
          template = template.replace(/user-firstName/g, msg.userFirstName);
          template = template.replace(/user-fullName/g, msg.fullUserName);
          template = template.replace(/user-photo/g, msg.userPhoto);
          template = template.replace(/message-text/g, msg.message.chat_message_text);
          var messageList = inji.Chats.chats[chatIndex].element.find('.chats-chat-messageList');
          if (inji.Chats.chats[chatIndex].reverse) {
            messageList.append(template);
            if (inji.Chats.chats[chatIndex].scrollable) {
              var height = messageList.height() + messageList.scrollTop() + parseInt(messageList.css('paddingTop')) + parseInt(messageList.css('paddingBottom'));
              messageList.scrollTop(messageList[0].scrollHeight);
            }
          } else {
            messageList.prepend(template);
          }
        }

        var allMsgs = inji.Chats.chats[chatIndex].element.find('.chats-chat-messageList>*');
        if (allMsgs.length > 20) {
          var i = 0;
          if (inji.Chats.chats[chatIndex].reverse) {
            var diff = allMsgs.length - 20;
            while (i <= diff && i >= 0) {
              $(allMsgs[i]).remove();
              i--;
            }
          } else {
            i = allMsgs.length + 1;
            while (i > 20 && allMsgs[i]) {
              $(allMsgs[i]).remove();
              i++;
            }
          }
        }
      }
    });
  };
  updater();
  inji.Chats.chats[chatIndex].timer = setInterval(updater, 5000);
}
inji.Chats.sendForm = function (form, id) {
  var chat = inji.Chats.get(id);
  var formData = new FormData(form);
  inji.Server.request({
    url: '/chats/sendForm/' + chat.element.data('chat-id'),
    type: 'POST',
    data: formData,
    processData: false,
  });
}