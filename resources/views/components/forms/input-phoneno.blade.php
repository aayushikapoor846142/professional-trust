@php 
$dropdownButton = 'dropdownButton-'.mt_rand();
$dropdownMenu = 'dropdownMenu-'.mt_rand();
$searchCountry = 'searchCountry-'.mt_rand();
@endphp
<div class="phone-input-container js-form-message mb-3">
    <div class="cds-phonedropdown"> 
        @if(isset($label))
        <label class="small-label">{{$label}} 
             @if(isset($required) && $required)
            <span class="danger">*</span>
            @endif 
        </label>
        @endif
        <div id="{{$dropdownButton}}" class="dropdownButton"> 
            @if($default_country_code  != '')
                @php
                $is_country_code = 0;
                @endphp
                @foreach(countries() as $country)
                    @if($default_country_code == $country->phonecode)
                        @if($is_country_code == 0)
                        @php $is_country_code = 1 @endphp
                        <img src="{{ url('assets/country-code/' . $country->sortname . '.svg') }}" class="img-flag" alt="{{ $country->name }}">
                        {{ $country->sortname }} (+{{ $country->phonecode }}) 
                        @endif
                    @endif
                @endforeach
            @else
                Country Code
            @endif
        </div> 
        <ul id="{{$dropdownMenu}}" class="form-select dropdownMenu hidden"> 
            <li class="ignore-click">
                <input type="text" id="{{$searchCountry}}" class="searchCountry form-control" placeholder="Search country name, code or sortcode..." class="form-control">
            </li>
            @foreach(countries() as $country)
                <li data-country="{{ $country->name }}" data-code="+{{ $country->phonecode }}" data-sortcode="{{ $country->sortname }}" @if($default_country_code == $country->phonecode) class="selected" @endif> 
      <img src="{{ url('assets/country-code/' . $country->sortname . '.svg') }}" class="img-flag" alt="{{ $country->name }}">
                    {{ $country->sortname }} (+{{ $country->phonecode }}) 
                </li>
            @endforeach
        </ul>
        <input type="hidden" class="country-code" name="{{$country_code_name??''}}" value="{{ $default_country_code??'+1' }}">
        
        <input @if(isset($readonly) && $readonly) readonly @endif @if(isset($id)) id="{{$id}}" @endif
                type="text"
                @if(isset($events)) {{implode(" ", $events)}} @endif
                class="phone-number @if(!isset($allow_html) || (isset($allow_html) && $allow_html == false)) html-not-allowed @endif @if(isset($required) && $required) required  @endif"
                name="{{$name??''}}" placeholder="Phone number" aria-label="Email" @if(isset($required) &&
                $required) required @endif data-msg="" value="{{ $value ?? '' }}" />
        
    </div>
</div>

@push("scripts")
<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("{{$searchCountry}}");
    const countryList = document.querySelectorAll(`#${searchInput.closest('ul').id} li:not(:first-child)`);

    searchInput.addEventListener("input", function () {
        const searchText = this.value.toLowerCase().trim();

        countryList.forEach(country => {
            const countryName = country.getAttribute('data-country').toLowerCase();
            const countryCode = country.getAttribute('data-code').toLowerCase();
            const countrySortcode = country.getAttribute('data-sortcode').toLowerCase();
            const countryText = country.textContent.toLowerCase();

            // Search through country name, phone code, sortcode, and display text
            if (countryName.includes(searchText) || 
                countryCode.includes(searchText) || 
                countrySortcode.includes(searchText) || 
                countryText.includes(searchText)) {
                country.style.display = "";
            } else {
                country.style.display = "none";
            }
        });
    });
});
    $("#{{$dropdownButton}}").on("click", function () {
            $("#{{$dropdownMenu}}").toggleClass("hidden");
        });
        // Handle item selection
        $("#{{$dropdownMenu}}").on("click", "li:not(.ignore-click)", function () {
   
            // var selectedText = $(this).text();
            var selectedText = $(this).html();
            var selectedCode = $(this).data("code");
            $("#{{$dropdownButton}}").html(selectedText);
            $("#{{$dropdownMenu}}").addClass("hidden");
            $(this).parents(".phone-input-container").find(".country-code").val(selectedCode);
            $(this).parents(".phone-input-container").find(".searchCountry").val('');
            $(this).parents(".phone-input-container").find(".dropdownMenu li").show();
            // alert("You selected: " + selectedCode);
        });
</script>
@endpush