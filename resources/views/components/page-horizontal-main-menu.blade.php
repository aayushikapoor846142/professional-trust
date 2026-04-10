<link rel="stylesheet" href="{{ asset('assets/css/27-CDS-page-submenu.css') }}">
@if(empty($sub_menus))
<!-- Title Section -->
@php 
/* 
$page_arr = [
    'page_title' => 'Groups Listing',
    'page_description' => 'Navigate through the available options below',
    'page_type' => 'group-list',
];
*/
@endphp
<section class="CDSDashboardSubmenu-title-section">
    <div class="CDSDashboardSubmenu-title-container">
        <div class="CDSDashboardSubmenu-title-content">
            <h1>
                @if(isset($page_arr['page_title']) && !empty($page_arr['page_title']))
                    {{ $page_arr['page_title'] }}
                @else
                    {{ $parentTitle }}
                @endif
            </h1>
            @if(isset($page_arr['page_description']))
            <p>{!! $page_arr['page_description'] !!}</p>
            @endif
        </div>  	
		
    </div>
    @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'support-tickets')
    <div class="CDSDashboardSubmenu-links">
        <button type="button" onclick="openCustomPopup(this)" data-href="{{ route('panel.tickets.create-modal') }}"
            class="CdsTicket-btn CdsTYButton-btn-primary" @if($page_arr['ticket']==5) disabled @endif>
            <i class="fas fa-plus"></i> Raise New Ticket
        </button>
    </div>
    @endif
    @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'add-articles')
        <a href="{{ baseUrl('/articles') }}" class="CdsTYButton-btn-primary">
            <i class="fa-left fa-solid me-1" aria-hidden="true"></i>
            Back
        </a>
    @endif
    @if(isset($page_arr['page_type']) && $page_arr['page_type'] == 'articles')
        @if(isset($page_arr['canAddArticle']) && $page_arr['canAddArticle'])
            <div class="CDSDashboardSubmenu-links">
                <a href="{{ baseUrl('articles/add') }}" class="CdsTYButton-btn-primary">
                    <i class="fa-plus fa-solid me-1"></i>
                    Add New
                </a>
            </div>
        @else
            <div class="CDSDashboardSubmenu-links">
                <button class="btn btn-secondary" disabled>
                    <i class="fa-plus fa-solid me-1"></i>
                    Add New (Access Restricted)
                </button>
            </div>
        @endif
    @endif
</section>
<!-- Additional inline styles to ensure sticky behavior -->
<style>
    /* Ensure placeholder has no default styling that could interfere */
    .CDSDashboardSubmenu-submenu-placeholder {
        display: none;
        width: 100%;
    }
    
   
</style>
@endif