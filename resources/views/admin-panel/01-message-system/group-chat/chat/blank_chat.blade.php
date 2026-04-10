<div class="message-content">
  <div class="welcome-chat">
    <div class="chat-avatar">
      <img src="{{ auth()->user()->profile_image ? userDirUrl(auth()->user()->profile_image, 't') : 'assets/images/default.jpg' }}" alt="Doris">
    </div>
    <h5>Welcome</h5>
    <h6>{{auth()->user()->first_name.' '.auth()->user()->last_name}}</h6>
  </div>
</div>