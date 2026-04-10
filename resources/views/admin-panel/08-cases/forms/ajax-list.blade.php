@if(count($records) > 0)
  
    @foreach($records as $key => $record)
    <div class="CdsCustomForm-list-item position-relative" data-type="{{ $record->form_type == 'step_form' ? 'step' : 'single' }}" data-unique-id="{{ $record->unique_id }}">
        <div class="CdsCustomForm-checkbox-wrapper">
            {!! FormHelper::formCheckbox([
                'id' => 'row-' . $key,
                'value' => $record->unique_id,
                 'checkbox_class' => 'row-checkbox custom-control-input case-checkbox'
            ]) !!}
            {{--<input type="checkbox" class="CdsCustomForm-checkbox row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">--}}
        </div>
        
        <div class="CdsCustomForm-item-content">
            <h3 class="CdsCustomForm-item-title">{{ $record->name ?? 'Untitled Form' }}</h3>
            <div class="CdsCustomForm-item-meta">
                <span class="CdsCustomForm-meta-item">
                    @if($record->form_type == 'step_form')
                        <span class="CdsCustomForm-form-type-badge CdsCustomForm-step">🏷️ Step Form</span>
                    @else
                        <span class="CdsCustomForm-form-type-badge CdsCustomForm-single">🏷️ Single Form</span>
                    @endif
                </span>
                <span class="CdsCustomForm-meta-item">
                    👤 {{ $record->user->first_name ?? '' }} {{ $record->user->last_name ?? '' }}
                </span>
                <span class="CdsCustomForm-meta-item">
                    📅 {{ \Carbon\Carbon::parse($record->created_at)->diffForHumans() }}
                </span>
                <span class="CdsCustomForm-meta-item">
                    📊 {{ $record->responses_count ?? 0 }} responses
                </span>
            </div>
        </div>
        
        <div class="CdsCustomForm-item-actions">
              @if(checkPrivilege([
                                'route_prefix' => 'panel.forms',
                                'module' => 'professional-forms',
                                'action' => 'reply'
                            ]))
            <button class="CdsCustomForm-action-btn CdsCustomForm-edit" title="View Reply" onclick="window.location.href='{{ baseUrl('forms/view-reply/'.$record->unique_id) }}'">
                ✏️
            </button>
                  @endif

            @if(checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'edit'
            ]))
            <button class="CdsCustomForm-action-btn CdsCustomForm-edit" title="Edit" onclick="window.location.href='{{ baseUrl('forms/edit/'.$record->unique_id) }}'">
                ✏️
            </button>
            @endif
            
            @if(checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'render-form'
            ]))
            <button class="CdsCustomForm-action-btn CdsCustomForm-view" title="View" onclick="window.location.href='{{ baseUrl('forms/render-form/'.$record->unique_id) }}'">
                👁️
            </button>
            @endif
            
            @if(checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'send-form'
            ]))
            <button class="CdsCustomForm-action-btn CdsCustomForm-share" title="Send Form" onclick="showPopup('{{ baseUrl('forms/send-mail/'.$record->unique_id) }}')">
                📤
            </button>
            @endif
            
            @if(checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'delete'
            ]) || checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'edit'
            ]) || checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'render-form'
            ]) || checkPrivilege([
                'route_prefix' => 'panel.forms',
                'module' => 'professional-forms',
                'action' => 'send-form'
            ]))
            <div class="btn-group">
                <button class="CdsCustomForm-action-btn dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" style="border: none; background: transparent;">
                    ⋮
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.forms',
                        'module' => 'professional-forms',
                        'action' => 'edit'
                    ]))
                    <li>
                        <a class="dropdown-item" href="{{ baseUrl('forms/edit/'.$record->unique_id) }}">
                            <i class="fa fa-edit me-2"></i> Edit Form
                        </a>
                    </li>
                    @endif
                    
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.forms',
                        'module' => 'professional-forms',
                        'action' => 'render-form'
                    ]))
                    <li>
                        <a class="dropdown-item" href="{{ baseUrl('forms/render-form/'.$record->unique_id) }}">
                            <i class="fa fa-eye me-2"></i> View Form
                        </a>
                    </li>
                    @endif
                    
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.forms',
                        'module' => 'professional-forms',
                        'action' => 'send-form'
                    ]))
                    <li>
                        <a class="dropdown-item" href="javascript:;" onclick="showPopup('{{ baseUrl('forms/send-mail/'.$record->unique_id) }}')">
                            <i class="fa fa-paper-plane me-2"></i> Send Form
                        </a>
                    </li>
                    @endif
                    
                    <li><hr class="dropdown-divider"></li>
                       
                    @if(checkPrivilege([
                        'route_prefix' => 'panel.forms',
                        'module' => 'professional-forms',
                        'action' => 'duplicate'
                    ]))
                    <li>
                        <a class="dropdown-item" href="{{ baseUrl('forms/duplicate/'.$record->unique_id) }}">
                            <i class="fa fa-copy me-2"></i> Duplicate
                        </a>
                    </li>
                       @endif

                    @if(checkPrivilege([
                        'route_prefix' => 'panel.forms',
                        'module' => 'professional-forms',
                        'action' => 'delete'
                    ]))
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('forms/delete-form/'.$record->unique_id) }}">
                            <i class="fa fa-trash me-2"></i> Delete
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
            @endif
        </div>
    </div>
    @endforeach
@else
    <div class="CdsCustomForm-empty-state">
        <div class="CdsCustomForm-empty-icon">📋</div>
        <h3>No forms found</h3>
        <p>@if(request()->get('search')) Try adjusting your search criteria @else Create your first form to get started @endif</p>
    </div>
@endif

<script>
$(document).ready(function() {
    // Initialize tooltips if needed
    $('[title]').tooltip();
    
    // Animation on load
    $('.CdsCustomForm-list-item').each(function(index) {
        $(this).css('animation-delay', (index * 0.05) + 's');
    });
});
</script>