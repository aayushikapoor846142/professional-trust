@php 
$editor_id = "cds-editor-".mt_rand();
$editor_id2 = "cds-editor-".mt_rand();
@endphp
<div class="modal-dialog" style="max-width: none !important;width: 95%;" >
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" >{{$pageTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12">
                    <div class="shortcode-container " role="group" aria-label="Button group example">
                        @foreach(editorShortcode() as $name => $shortcode)
                        <div class="item-btn">
                            <label for="item-{{str_slug($name)}}">
                                <input data-name="{{$name}}" data-shortcode="{{$shortcode}}" id="item-{{str_slug($name)}}" type="radio" class="shortcode" name="shortcode" value="{{$name}}" />
                                <span class="text-dark">
                                    {{$name}}
                                </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="shortcode-form shortcode_type_1">
                        <div class="my-3">
                        {!! FormHelper::formTextarea(['name'=>"description",
                        'id'=>$editor_id,
                        "label"=>"Enter Content",
                        'class'=>"noval cds-texteditor",
                        'value'=>''
                        ]) !!}
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" onclick="shortCodeType1()" class="CdsTYButton-btn-primary">Generate Shortcode</button>
                        </div>
                    </div>
                    <div class="shortcode-form shortcode_type_2">
                        <div class="my-3">
                            <div class="search-container">
                                {!! FormHelper::formInputText([
                                    'id'=> 'searchInput',
                                    "label"=>"Search Images",
                                ]) !!}
                                <!-- <input type="text" class="form-control" id="searchInput" placeholder="Search..." /> -->
                                <ul id="suggestionsList" class="suggestions hidden"></ul>
                            </div>
                        </div>

                        <div class="my-4">
                            <div class="col-md-12">
                                {!! FormHelper::formInputText([
                                    'id'=> 'group_title',
                                    "label"=>"Group Title",
                                ]) !!}
                            </div>
                            <table id="images-list" class="table">
                                <thead>
                                <tr>
                                    <th width="10%"></th>
                                    <th>
                                        Image Shorcode
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" onclick="shortCodeType2()" class="CdsTYButton-btn-primary">Generate Shortcode</button>
                        </div>
                    </div>
                    <div class="shortcode-form shortcode_type_3">
                        
                        <div class="my-4">
                            <label>Enter Links</label>
                            <table id="reference-links" class="table">
                                <thead>
                                <tr>
                                    <th>
                                        Link
                                    </th>
                                    <th>
                                        Name
                                    </th>
                                    <th>
                                        <button type='button'onclick='addRefLink()' class='CdsTYButton-btn-primary remove-image btn-sm'><i class='fa fa-plus'></i></button></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="ref-link form-control" />
                                        </td> 
                                        <td>
                                            <input type="text" class="ref-name form-control" />
                                        </td> 
                                        <td>
                                            <button type='button'onclick='removeImage(this)' class='CdsTYButton-btn-primary CdsTYButton-border-thick remove-image btn-sm'><i class='fa fa-times'></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" onclick="shortCodeType3()" class="CdsTYButton-btn-primary">Generate Shortcode</button>
                        </div>
                    </div>
                    <div class="shortcode-form shortcode_type_4">
                        {!! FormHelper::formInputText([
                            'id'=>"special_title",
                            "label"=>"Title",
                            ]) !!}
                        {{--<div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" id="special_title" placeholder="Enter Group Title" />
                        </div>--}}
                        <div class="my-3">
                        {!! FormHelper::formTextarea(['name'=>"description",
                        'id'=>$editor_id2,
                        "label"=>"Enter Content",
                        'class'=>"noval cds-texteditor",
                        'value'=>''
                        ]) !!}
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="button" onclick="shortCodeType4()" class="CdsTYButton-btn-primary">Generate Shortcode</button>
                        </div>
                    </div>
                    <div class="shortcode-form shortcode_type_5">
                        <div class="col-md-12 text-center">
                            <button type="button" onclick="shortCodeType5()" class="CdsTYButton-btn-primary">Generate Shortcode</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                <div class="gen-shortcode">
                        <code id="generated-shortcode"></code>
                    </div>
                </div>
               
            </div>
        </div>
    </div>
</div>
  
<script>
var editor1;
var editor2;
var selectedTag;
$(document).ready(function(){
    editor1 = initEditor("{{$editor_id}}");
    editor2 = initEditor("{{$editor_id2}}");
    $(".shortcode").change(function(){
        $(".gen-shortcode").show();
        var shortcode = $(this).data("shortcode");
        $(".shortcode-form").hide();
        $("."+shortcode).show();
        selectedTag = $(this).val();
    })
    $('#generated-shortcode').click(function () {
        const urlField = $(this).html();
        navigator.clipboard.writeText(urlField).then(() => {
            successMessage('URL copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    });
});

function shortCodeType1(){
    var content = editor1.editor.getContent();
    if(content != ''){
        var html = encodeHtml(content);
        var shortcode = "[#"+selectedTag+" content={"+content+"} #]";
        $("#generated-shortcode").html(shortcode);
    }else{

    }
}




var data = {!! $images !!};

var input = document.getElementById('searchInput');
var suggestionsList = document.getElementById('suggestionsList');

var renderSuggestions = (items) => {
suggestionsList.innerHTML = '';
items.forEach(item => {
    var html = "<li>";
    html += "<img src='"+item.file_url+"' />"
    html += "<span>"+item.name+"</span>";
    html += "<button data-name='"+item.name+"' data-url='"+item.file_url+"' data-shortcode='"+item.shortcode+"' onclick='selectImage(this)' type='button'>Choose</button>";
    $("#suggestionsList").append(html);
});
};

input.addEventListener('input', () => {
const query = input.value.toLowerCase();
const filteredData = query 
    ? data.filter(item => item.name.toLowerCase().includes(query)) 
    : data;

if (filteredData.length > 0) {
    renderSuggestions(filteredData);
    suggestionsList.classList.remove('hidden');
} else {
    suggestionsList.classList.add('hidden');
}
});

input.addEventListener('focus', () => {
    renderSuggestions(data); // Show all suggestions on focus
    suggestionsList.classList.remove('hidden');
});

input.addEventListener('blur', () => {
// setTimeout(() => suggestionsList.classList.add('hidden'), 100); // Delay to allow button clicks
});

// Hide suggestions when clicking outside
document.addEventListener('click', (event) => {
    if (!event.target.closest('.search-container')) {
        suggestionsList.classList.add('hidden');
    }
});

function selectImage(e){
    var url = $(e).data("url");
    var name = $(e).data("name");
    var shortcode = $(e).data("shortcode");
    var html = "<tr><td><img width='100%' src='"+url+"' /></td>";
    html +="<td><input type='hidden' value='"+shortcode+"' class='form-control image-shortcode' />"+shortcode+"</td>";
    html +="<td><input type='text' class='form-control image-name' /></td>";
    html +="<td><button type='button'onclick='removeImage(this)' class='CdsTYButton-btn-primary CdsTYButton-border-thick remove-image btn-sm'><i class='fa fa-times'></i></button></td>";
    html += '</tr>';
    $("#images-list tbody").append(html);
    suggestionsList.classList.add('hidden');

}
function removeImage(e){
    $(e).parents('tr').remove();
}

function shortCodeType2(){
    var img_arr = [];
    var group_title = $("#group_title").val();
    $("#images-list tbody tr").each(function(){
        var shortcode = $(this).find(".image-shortcode").val();
        var name = $(this).find(".image-name").val();
        img_arr.push(shortcode+","+name);
    });
    var code = img_arr.join("|");
    var shortcode = "[#"+selectedTag+" groupTitle={"+group_title+"} content={"+code+"} #]";
    $("#generated-shortcode").html(shortcode);

}


function addRefLink(){
    var html ='<tr>';
    html +='<td>';
    html +='<input type="text" class="ref-link form-control" />';
    html +='</td>';
    html +='<td>';
    html +='<input type="text" class="ref-name form-control" />';
    html +='</td>';
    html +='<td>';
    html +='<button type="button" onclick="removeImage(this)" class="CdsTYButton-btn-primary CdsTYButton-border-thick remove-image btn-sm"><i class="fa fa-times"></i></button>';
    html +='</td>';
    html +='</tr>';
    $("#reference-links tbody").append(html);
}

function shortCodeType3(){
    var links = [];
    $("#reference-links tbody tr").each(function(){
        var link = $(this).find(".ref-link").val();
        var name = $(this).find(".ref-name").val();
        if(link != '' & name != ''){
            links.push(link+","+name);
        }
    });
    var code = links.join("|");
    var shortcode = "[#"+selectedTag+" content={"+code+"} #]";
    $("#generated-shortcode").html(shortcode);

}
function shortCodeType4(){
    var special_title = $("#special_title").val();
    var content = editor2.editor.getContent();
    if(content != ''){
        var shortcode = "[#"+selectedTag+" dynamicTitle={"+special_title+"} content={"+content+"} #]";
        $("#generated-shortcode").html(shortcode);
    }
}

function shortCodeType5(){
    var shortcode = "[#"+selectedTag+" #]";
    $("#generated-shortcode").html(shortcode);
}

</script>
<script>
    $(document).ready(function() {
        // $('.form-group input[type="text"], .form-group input[type="email"], .form-group input[type="password"], .form-group input[type="url"], .form-group select, .form-group textarea').each(function() {
        //     $(this).on('focus', function() {
        //         $(this).addClass('focused');
        //     });        
        //     // Remove 'focused' class if the field is empty and loses focus
        //     $(this).on('blur', function() {
        //         if ($(this).val() === '') {
        //             $(this).removeClass('focused');
        //         }
        //     });    
        //     $(this).on('change', function() {
        //         if ($(this).val() === '') {
        //             $(this).removeClass('focused');
        //         }else{
        //             $(this).addClass('focused');
        //         }
        //     });    
        //     // On page load, check if the field has a value
        //     if ($(this).val() !== '') {
        //         $(this).addClass('focused');  // Apply 'focused' class if field is not empty
        //         $(this).focus();               // Focus on the input field if it has a value
        //     }
        // });
    });
</script>
