<div class="message-content cds-chatBodyFullView pt-0" id="scrollDiv">
    <form id="clear-messages">
        @csrf
        <div style="display:none" id="selectAllDiv" class="select-all-checkbox">
            <div class="cds-clearBox">
                <label class="cds-checkbox ">
                    <input type="checkbox" id="selectAll" class="checkbox" />
                    <span class="checkmark"></span>
                    <span class="selectAll">Select All</span>
                </label>
            </div>
            <div class="cds-action-btn">
                <button id="cancelClear" type="button" class="btn btn-dark btn-sm">Cancel Clear</button>
                <button id="clearChatBtn" type="submit" class="CdsTYButton-btn-primary btn-sm">Clear Selected Messages</button>
            </div>
        </div>

        <div class="messages_read" id="messages_read">
            @if($chat_empty)
            @include("admin-panel.01-message-system.message-centre.empty-chat")
            @else
            @include('components.skelenton-loader.message-skeletonloader')
            @endif
        </div>
    </form>

    <div id="block_msg{{$chat->id}}" class="blocked-chat" style="color:black">
        @if($chat->blocked_chat==1 )
        <h3>Chat has been blocked</h3>
        @endif
    </div>
    <!-- Typing message animation -->
</div> 