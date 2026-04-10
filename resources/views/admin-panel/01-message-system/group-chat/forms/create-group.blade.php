@props(['members' => collect()])

<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <form id="create-group-form" enctype="multipart/form-data" 
              action="{{ baseUrl('group/create-group') }}" method="post">
            
            <div class="modal-header">
                <h5 class="modal-title">Create New Group</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                @csrf
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="group-name">Group Name *</label>
                        <input type="text" id="group-name" name="name" 
                               class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Group Type *</label>
                        <div class="radio-group">
                            @foreach(FormHelper::groupType() as $type)
                                <label class="radio-label">
                                    <input type="radio" name="group_type" 
                                           value="{{ $type['value'] }}" 
                                           {{ $loop->first ? 'checked' : '' }} required>
                                    {{ $type['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="group-description">Description *</label>
                        <textarea id="group-description" name="description" 
                                  class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Members *</label>
                        <div class="member-search">
                            <input type="text" id="member-search" 
                                   class="form-control" placeholder="Search members...">
                        </div>
                        <div class="member-list" id="member-list">
                            @foreach($members as $member)
                                @include('admin-panel.01-message-system.group-chat.components.member-item', 
                                         ['member' => $member])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="CdsTYButton-btn-primary">Create Group</button>
            </div>
        </form>
    </div>
</div> 