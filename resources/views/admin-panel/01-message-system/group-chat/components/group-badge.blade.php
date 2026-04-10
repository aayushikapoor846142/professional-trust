@props(['group'])

@if($group->type == 'Private')
    <div class="group-badge private-group">
        {{ $group->type }}
    </div>
@elseif($group->type == 'Public')
    <div class="group-badge public-group">
        {{ $group->type }}
    </div>
@endif 