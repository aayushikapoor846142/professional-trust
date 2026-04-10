@extends('admin-panel.layouts.app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <section class="cds-t25n-content-professional-profile-section">
                <div class="cds-t25n-content-professional-profile-container-main-body">
                    <div class="cds-t25n-content-professional-profile-container-main-body-information">
                        <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                            <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                <div class="cds-t25n-content-professional-profile-container-main-body-information-exp">
                                    <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise-container">
                                        <link href="{{ url('assets/plugins/chatapp/emojis/css/style.css?v='.mt_rand()) }}" rel="stylesheet" />
                                        <link href="{{ url('assets/plugins/chatapp/chatapp.css?v='.mt_rand()) }}" rel="stylesheet" />
                                        <div class="cdsTYDashboard-feed-main-container">
                                            <div class="feed-left-side" style="display:{{$discussionUId == ''?'block':'none'}}">
                                                <div class="feed-content">
                                                    <div id="feed-list-tab" class="active chat-tab-content">
                                                        <div class="dsTYDashboard-feed-create-body">
                                                            <div class="cdsTYDashboard-feed-create">                                                                    
                                                                <div class="feed-create-form">
                                                                    <form id="discussionCreateForm" class="js-validate" action="{{ baseUrl('manage-discussion-threads/save/thread') }}"        method="post" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="row cds-feeds-post-content">
                                                                            <div class="col-md-12 col-sm-6">
                                                                                {!! FormHelper::formInputText([ 'name'=>"topic_title", 'required'=>true, 'id'=>"topic_title", "label"=>"Enter Topic Title"]) !!}
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6">
                                                                                {!! FormHelper::formTextarea(['name'=>"short_description", 'id'=>"short_description", 'required'=>true, "label"=>"Enter Short Description", 'class'=>"noval cds-texteditor"])
                                                                                !!}
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6">
                                                                                {!! FormHelper::formTextarea(['name'=>"description", 'id'=>"description", 'required'=>true, "label"=>"Enter Description", 'class'=>"noval cds-texteditor", ])
                                                                                !!}
                                                                            </div>

                                                                            <div class="col-md-12 col-sm-6">
                                                                                <div class="cds-selectbox">
                                                                                    {!! FormHelper::formSelect([ 'name' => 'discussion_category', 'id' => 'discussion_category', 'label' => 'Select Discussion Category ', 'class' =>
                                                                                    'select2-input', 'required' => true, 'options' => $categories, 'value_column' => 'id', 'label_column' => 'name', 'selected' => '', 'is_multiple' => false ])
                                                                                    !!}
                                                                                </div>
                                                                            </div>

                                                                            

                                                                            <div class="col-md-12 col-sm-6">
                                                                                <div class="cds-selectbox">
                                                                                    {!! FormHelper::formSelect([ 'name' => 'type', 'id' => 'type', 'label' => 'Select Discussion Type', 'class' => 'select2-input', 'required' => true,
                                                                                    'options' => FormHelper::groupType(), 'value_column' => 'value', 'label_column' => 'label', 'selected' => '', 'is_multiple' => false ]) !!}
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-12 col-sm-6 members-area" style="display:none">
                                                                                <div class="multi-selectbox">
                                                                                    {!! FormHelper::formSelect([ 'name' => 'selected_members[]', 'id' => 'selected_members', 'label' => 'Select Members ', 'class' => 'select2-input
                                                                                    cds-multiselect add-multi', 'options' => $members ?? [], 'value_column' => 'id', 'label_column' => 'name', 'is_multiple' => true ]) !!}

                                                                                    <span class="text-danger selected_members"></span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-12 col-sm-6 allow-join-member" style="display:none">
                                                                                <div class="form-check cds-ty-dashboard-articles-segments-pref">
                                                                                    {!! FormHelper::formCheckbox(['name' => "allow_join_request", 'value' => 1, 'id' => "allow_join_request"]) !!}
                                                                                    <label class="form-check-label" for="allow_join_request">Allow Member to Join</label>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div id="thumbnail-preview" style="margin-top: 10px;">
                                                                            <!-- Thumbnail will be displayed here -->
                                                                        </div>
                                                                        <a href="javascript:;" class="open-dropzone-post">
                                                                            <div class="cdsTYDashboard-feed-post-media-btn-wrap">
                                                                                <div class="cds-feeds-post-actionbtn-content">
                                                                                    <div class="form-group js-form-message">
                                                                                        <label for="media" class="col-form-label input-label position-relative">
                                                                                            <img src="{{url('assets/images/icons/gallery-icon.svg') }}" alt="Gallery Icon" />
                                                                                            Media
                                                                                            <!-- <input type="file" name="media" id="media" class="form-control cds-feeds-image-add"
                                                                                                        aria-label="Choose image" accept=".jpg,.jpeg,.png,.gif,.bmp,.svg,.webp"
                                                                                                        data-msg="Please select an image file (jpeg, png, etc.)"> -->
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </a>

                                                                        {!! FormHelper::formDropzone([ 'name' => 'file', 'id' => 'discussion-media-dropzone', 'class' => 'discussion-media-dropzone', 'required' => false, 'max_files' => 6, ])
                                                                        !!}
                                                                        <input type="hidden" name="file" id="file" />
                                                                        <div class="cdsTYDashboard-feed-post-action-btn-wrap">
                                                                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Post Discussion</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="categoryId" value="@if(isset($categoryId)) {{ $categoryId }} @else null @endif">
                
            </section>
        </div>
    </div>
</div>
@php
$loader_html = minify_html(view("components.skelenton-loader.discussion-comment-loader")->render());
@endphp
@endsection
@push('scripts')
<!-- <script src="{{url('assets/js/custom-editor.js')}}"></script> -->
<script src="{{ url('assets/js/discussion-board.js?v='.mt_rand()) }}"></script>

<script type="text/javascript">
var timestamp = "{{time()}}";
var users = '';
var loader_html = '{!! $loader_html !!}';
@if(($chat_members ?? '') != '')
users = {
    !!$chat_members!!
};
@endif
$(document).ready(function(){
   $(document).on("change","#type",function(){
        if($(this).val() == 'private'){
            $(".members-area").show();
            $(".allow-join-member").show();
        }else{
            $(".members-area").hide();
            $(".allow-join-member").hide();
          
        }
   }) 
})
document.addEventListener('DOMContentLoaded', async function() {
   @if($discussionUId != '')
       await loadDiscussionContentAjax("{{$discussionUId}}", "{{$discussion_id}}");
       await initializeDiscussionContent("{{$discussion_id}}");
       await initializeDiscussionSocket("{{$discussion_id}}");
   @endif
});
</script>
@endpush
