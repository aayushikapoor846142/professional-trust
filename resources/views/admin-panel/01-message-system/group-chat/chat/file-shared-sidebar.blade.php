 <!-- files shared in group sidebar -->
    <!-- <div class="chat-profile-sidebar" id="filesidebar"> -->
        <div class="chat-profile-card rounded">
            <div class="chat-profile-title">
                <h2>Files Shared In Group</h2>
            </div>
            <!-- Group Profile Header -->
            <div class="chat-profile-header text-center p-4 group-info" id="group-name-edit">
                <div class="group-info-icon">
                    <h2 class="chat-profile-name" id="groupName">{{$group->name}}</h2>
                </div>
                <div class="group-info-actionbtn"></div>
            </div>

            <!-- Accordion -->
            <div class="accordion chat-profile-accordion" id="chatProfileAccordion">
                <!-- Attached Files Section -->

                <div class="accordion-item" style="display: block">
                    <h2 class="accordion-header" id="filesHeader">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                            Files
                        </button>
                    </h2>
                    <div id="filesContent" class="accordion-collapse collapse show" aria-labelledby="filesHeader"
                        data-bs-parent="#chatProfileAccordion">
                        <div class="accordion-body p-0">
                            @php
                            $groupAttachments=groupAttachments($group->id,'1')??[];
                            @endphp
                            @if(!empty($groupAttachments))
                            <div class="group-file-search">
                                <div class="group-search-block">
                                    <span class="search-icon">
                                        <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" class="form-control" id="search-file-input"
                                        placeholder="Search Files Here">
                                    <a href="javascript:;" class="clear-text"
                                        onclick="fileSearchInputs('{{$group->id}}','clear')">
                                        <i class="fa-times fa-regular fa-magnifying-glass"></i>
                                    </a>
                                </div>
                                <div class="group-action-btn">
                                    <button type="button" onclick="fileSearchInputs('{{$group->id}}')"
                                        class="btn btn-success" data-bs-toggle="tooltip" title="Search">
                                        <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                         
                            @endif
                            <div id="attachments-container">
                                @include('admin-panel.01-message-system.group-chat.chat.partials.attachments',['groupAttachments'=>$groupAttachments])
                            </div>
                            @if(!empty($groupAttachments) && $groupAttachments->hasMorePages())
                            <button id="load-more" class="CdsTYButton-btn-primary load-more-btn"
                                data-page="{{ $groupAttachments->currentPage() + 1 }}">Load More</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </div> -->
