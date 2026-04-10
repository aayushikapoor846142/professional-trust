@extends('admin-panel.layouts.app')

@section('content')
<!-- Content -->
<div class="container-fluid">

    <div class="chat-container">
        <div class="chat-sidebar">
            <div class="chat-tabs">
                <button class="chat-tab" data-tab="tab1">
                    <i class="fa-solid fa-user"></i>
                </button>
                <button class="chat-tab active" data-tab="tab2">
                    <i class="fa-brands fa-rocketchat"></i>
                </button>
                <button class="chat-tab " data-tab="tab3">
                    <i class="fa-solid fa-user-group"></i>

                </button>

            </div>

            <div class="chat-content">
                <div id="tab1" class="chat-tab-content">
                    <h2>Profile</h2>
                </div>
                <div id="tab2" class="chat-tab-content active">
                    <div class="chat-list" id="chatList">
                        <div class="chat-title">
                            <h2>Chats</h2>
                        </div>
                        <div class="chat-header">
                            <input type="text" placeholder="Search messages or users">
                        </div>
                        <div class="chats-online-block">
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                            <div class="chats-online">
                                <div class="chat-online-img">
                                <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">

                                </div>
                                <p>Patrick</p>
                            </div>
                        </div>
                        <div class="recent-chats">
                            <h3>Recent</h3>
                            <div class="chat-item">
                                <div class="chat-avatar">
                                    <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                                </div>
                                <div class="chat-info">
                                    <p class="chat-name">Doris Brown</p>
                                    <p class="chat-preview">Nice to meet you</p>
                                </div>
                                <span class="chat-time">10:12 AM</span>
                            </div>
                            <div class="chat-item">
                                <div class="chat-avatar">
                                    <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                                </div>
                                <div class="chat-info">
                                    <p class="chat-name">Doris Brown</p>
                                    <p class="chat-preview">Nice to meet you</p>
                                </div>
                                <span class="chat-time">10:12 AM</span>
                            </div>
                            <div class="chat-item">
                                <div class="chat-avatar">
                                    <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                                </div>
                                <div class="chat-info">
                                    <p class="chat-name">Doris Brown</p>
                                    <p class="chat-preview">Nice to meet you</p>
                                </div>
                                <span class="chat-time">10:12 AM</span>
                            </div>
                            <div class="chat-item">
                                <div class="chat-avatar">
                                    <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                                </div>
                                <div class="chat-info">
                                    <p class="chat-name">Doris Brown</p>
                                    <p class="chat-preview">Nice to meet you</p>
                                </div>
                                <span class="chat-time">10:12 AM</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab3" class="chat-tab-content ">
                    <h2>User Group</h2>
                </div>

            </div>
        </div>


        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            <div class="message-header">
                <div class="message-title">
                    <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    <p class="chat-name">Doris Brown</p>
                </div>
                <div class="message-action-btn">
                    <i class="fa-regular fa-magnifying-glass"></i>
                    <i class="fa-sharp fa-light fa-phone"></i>
                    <i class="fa-regular fa-video"></i>
                    <i class="fa-regular fa-user"></i>  
                    <div class="dropdown chat-dropdown">
                                <button class="CdsTYButton-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#option1">Option 1</a></li>
                                    <li><a class="dropdown-item" href="#option2">Option 2</a></li>
                                    <li><a class="dropdown-item" href="#option3">Option 3</a></li>
                                </ul>
                            </div>
                </div>

            </div>
            <div class="message-content">
                <div class="sent-block">
                    <div class="sent-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">
                        <div class="text-message-block">
                            <div class="message sent">
                                <p>Good morning</p>
                                <span>10:00</span>
                            </div>
                            <div class="dropdown chat-dropdown">
                                <button class="CdsTYButton-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#option1">Option 1</a></li>
                                    <li><a class="dropdown-item" href="#option2">Option 2</a></li>
                                    <li><a class="dropdown-item" href="#option3">Option 3</a></li>
                                </ul>
                            </div>

                        </div>
                        <div class="sender-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
                <div class="received-block">
                    <div class="received-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">


                        <div class="textreceive-message-block">
                            <div class="message received">
                                <p>Good morning, How are you?</p>
                                <span>10:02</span>
                            </div>
                            <div class="dropdown chat-dropdown">
                                <button class="CdsTYButton-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-ellipsis"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#option1">Option 1</a></li>
                                    <li><a class="dropdown-item" href="#option2">Option 2</a></li>
                                    <li><a class="dropdown-item" href="#option3">Option 3</a></li>
                                </ul>
                            </div>

                        </div>
                        <div class="receiver-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
                <div class="sent-block">
                    <div class="sent-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">
                        <div class="message sent">
                            <p>Good morning</p>
                            <span>10:00</span>
                        </div>
                        <div class="sender-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
                <div class="received-block">
                    <div class="received-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">

                        <div class="message received">
                            <p>Good morning, How are you?</p>
                            <span>10:02</span>
                        </div>
                        <div class="receiver-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
                <div class="sent-block">
                    <div class="sent-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">
                        <div class="message sent">
                            <p>Good morning</p>
                            <span>10:00</span>
                        </div>
                        <div class="sender-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
                <div class="received-block">
                    <div class="received-icon">
                        <img src="{{url('/assets/frontend/images/avatars/av1-small.png')}}" alt="Doris">
                    </div>
                    <div class="message-block">

                        <div class="message received">
                            <p>Good morning, How are you?</p>
                            <span>10:02</span>
                        </div>
                        <div class="receiver-name">
                            <p class="">Doris Brown</p>

                        </div>
                    </div>

                </div>
            </div>
            <div class="message-input">
                <input type="text" placeholder="Enter Message">
                <i class="fa-regular fa-face-smile"></i>
                <button>
                    <i class="fa-duotone fa-solid fa-play"></i>
                </button>
            </div>
        </div>
    </div>

</div>

<!-- End Content -->
@endsection

@section('javascript')
<script>
    // tabs //
    document.addEventListener("DOMContentLoaded", () => {
        const chatTabs = document.querySelectorAll(".chat-tab");
        const chatContents = document.querySelectorAll(".chat-tab-content");

        chatTabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                // Remove active class from all tabs
                chatTabs.forEach((t) => t.classList.remove("active"));
                // Hide all chat tab contents
                chatContents.forEach((content) => content.classList.remove("active"));

                // Add active class to the clicked tab
                tab.classList.add("active");

                // Show the corresponding chat tab content
                const target = document.getElementById(tab.dataset.tab);
                target.classList.add("active");
            });
        });
    });
</script>
@endsection