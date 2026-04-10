  <!-- New Chat Panle -->
  <input type="hidden" value="" id="reply_to_id">
  <input type="hidden" value="{{$group_id}}" id="get_group_id">
  <input type="hidden" value="{{baseUrl('group/send-msg/'.$group_id)}}" id="geturl">
  <div class="message-header">
      <div class="message-header-block">
          <div class="message-title">
          <div onclick="backToChats()">
                  <i class="fa-solid fa-angle-left"></i>
              </div>
              @if($group->group_image)
              <img src="{{ groupChatDirUrl($group->group_image, 't') }}" alt="Doris">
              @else
              @php
              $initial = strtoupper(substr($group->name, 0, 1)); // Extracts the first letter and converts to uppercase

              @endphp
              <div class="group-icon" data-initial="{{$initial}}"></div>
              @endif
              <p class="chat-name">

              <h3>{{$group->name}}</h3>
              </p>
          </div>
          <div class="message-action-btn">
              <div class="search-chats">
                  <i class="fa-regular fa-magnifying-glass" onclick="toggleChatsSearch()"></i>
              </div>
              <div onclick="toggleSidebar()">
                  <i class="fa-regular fa-user"></i>

              </div>
              <i class="fa-solid fa-user-plus modal-toggle" data-modal="addMember" onclick="openMembersModal()"></i>
            
          </div>
      </div>

      <div class="search-chats-toggle" id="chatsSearch" style="display: none;">
          <input type="text" id="search_input" placeholder="Search here..." />
          <button class="search-up" onclick="searchUp()">
              <i class="fa-solid fa-angle-up"></i>
          </button>
          <button class="search-down" onclick="searchDown()">
              <i class="fa-solid fa-angle-up"></i>
          </button>
          <button onclick="searchChatMessages()" id="searchbtn">
              <i class="fa-regular fa-magnifying-glass"></i>

          </button>
          <button onclick="toggleChatsSearch()">
              <i class="fa-solid fa-xmark"></i>
          </button>
      </div>



                </div>
                <div class="message-content">
                    <div class="messages_read" id="messages_read">
                        @include('admin-panel.01-message-system.group-chat.chat.chat_ajax')
                    </div>
                  
                    <input type="hidden" name="last_message_id" id="last_message_id" value="{{ LastMessageId() }}">

      <div class="typing-chat" style="display: none;">
          <div class="typechat-message">Typing</div>
          <div class="typing-indicator">
              <span></span>
              <span></span>
              <span></span>
          </div>
      </div>
  </div>
  <div class="reply-message " id="reply_quoted_msg" style="display: none">
      <div class="reply-icons">
          <i class="fa-solid fa-turn-up"></i>
          <i class="fa-solid fa-xmark"></i>
      </div>
      <p class="quoted-message">Reply quoted message</p><span class="username" id="myreply">MY Reply</span>
  </div>
  <div class="message-input" id="sendmsg">
      <p class="lead emoji-picker-container">
          <input type="text" placeholder="Enter Message" id="sendmsgg" data-emojiable="true" value="" name="send_msg">
      </p>
      <!-- <i class="fa-sharp fa-solid fa-face-smile"></i> -->
      <div class="message-upload-file modal-toggle" data-modal="uploadModal">
          <!-- <img src="{{ url('assets/message/images/message-upload.svg')}}" class="img-fluid" alt="send" /> -->
          <i class="fas fa-upload"></i>

      </div>
      <button id="sendBtn1">
          <i class="fa-duotone fa-solid fa-play"></i>
      </button>
  </div>
  <!--right sidebar -->
  <div class="chat-profile-sidebar">
      <div class="chat-profile-card rounded">
          <div class="chat-profile-title">
              <h2>Group Info</h2>
              <button class="close-btn" onclick="toggleSidebar()"><i class="fa-sharp fa-regular fa-xmark"></i></button>

          </div>
          <!-- Profile Header -->
          <div class="chat-profile-header text-center p-4">
              @if($group->group_image)
              <img class="chat-profile-picture" src="{{ groupChatDirUrl($group->group_image, 't') }}" alt="Doris">

              @else
              @php
              $initial = strtoupper(substr($group->name, 0, 1));
              @endphp
              <div class="group-icon" data-initial="{{$initial}}"></div>
              @endif


              <h2 class="chat-profile-name mt-3">{{$group->name}}</h2>
              <!--                         <p class="chat-profile-status text-success">Active</p>
 -->
              <p class="chat-profile-description mt-3">
              </p>
          </div>

          <!-- Accordion -->
          <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
              <!-- About Section -->
              <div class="accordion-item">
                  <h2 class="accordion-header" id="aboutHeader">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#aboutContent" aria-expanded="true" aria-controls="aboutContent">
                          About
                      </button>
                  </h2>
                  <div id="aboutContent" class="accordion-collapse collapse" aria-labelledby="aboutHeader" data-bs-parent="#chatProfileAccordion">
                      <div class="accordion-body">
                          This is the About section content.
                      </div>
                  </div>
              </div>
              <!-- Attached Files Section -->
              <div class="accordion-item" style="display: none">
                  <h2 class="accordion-header" id="filesHeader">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                          Attached Files
                      </button>
                  </h2>
                  <div id="filesContent" class="accordion-collapse collapse" aria-labelledby="filesHeader" data-bs-parent="#chatProfileAccordion">
                      <div class="accordion-body">
                          This is the Attached Files section content.
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <script>
    
      $(function() {
          // Initializes and creates emoji set from sprite sheet
          window.emojiPicker = new EmojiPicker({
              emojiable_selector: '[data-emojiable=true]',
              assetsPath: 'assets/emoji-img',
              popupButtonClasses: 'fa fa-smile-o'
          });
          // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
          // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
          // It can be called as many times as necessary; previously converted input fields will not be converted again
          window.emojiPicker.discover();
      });
  </script>