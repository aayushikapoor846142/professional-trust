        <!-- <div class="chat-profile-sidebar" id="filesidebar"> -->
            <div class="chat-profile-card rounded">
                <div class="chat-profile-title">
                    <h2> Files Shared In Chat</h2>
                </div>

                <!-- Accordion -->
                <div class="accordion chat-profile-accordion cds-chatFiles mt-4" id="chatProfileAccordion">
                    <!-- Attached Files Section -->

                    <div class="accordion-item" style="display: block">
                        <h2 class="accordion-header" id="filesHeader">
                            <button class="accordion-button " type="button" data-bs-toggle="collapse"
                                data-bs-target="#filesContent" aria-expanded="false" aria-controls="filesContent">
                                Files
                            </button>
                        </h2>
                        <div id="filesContent" class="accordion-collapse collapse show" aria-labelledby="filesHeader"
                            data-bs-parent="#chatProfileAccordion">
                            <div class="accordion-body p-0">
                                @php
                                $chatAttachments=chatAttachments($chat->id,'1')??[];
                                @endphp
                                @if(!empty($chatAttachments))
                                <div class="group-file-search">
                                    <div class="group-search-block">
                                        <span class="search-icon">
                                            <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                                        </span>
                                        <input type="text" class="form-control" id="search-file-input"
                                            placeholder="Search Files Here">
                                        <a href="javascript:;" class="clear-text"
                                            onclick="fileSearchInputs('{{$chat->id}}','clear')">
                                            <i class="fa-times fa-regular fa-magnifying-glass"></i>
                                        </a>
                                    </div>
                                    <div class="group-action-btn">
                                        <button type="button" onclick="fileSearchInputs('{{$chat->id}}')"
                                            class="btn btn-success" data-bs-toggle="tooltip" title="Search">
                                            <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                                        </button>
                                      
                                    </div>
                                </div>


                                @endif
                                <div id="attachments-container">

                                    @include('admin-panel.01-message-system.message-centre.partials.attachments',['chatAttachments'=>$chatAttachments])

                                </div>
                                @if(!empty($chatAttachments) && $chatAttachments->hasMorePages())
                                <button id="load-more" class="CdsTYButton-btn-primary load-more-btn"
                                    data-page="{{ $chatAttachments->currentPage() + 1 }}">Load More</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- </div> -->