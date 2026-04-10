
@include("admin-panel.02-feeds.components.header-search") 
<div class="CDSFeed-sidebar-overlay" onclick="closeFeedSidebar()"></div>

@push("scripts")
<script>
    
// Sidebar Toggle
function toggleFeedSidebar() {
    const sidebar = document.querySelector('.CDSFeed-sidebar');
    const overlay = document.querySelector('.CDSFeed-sidebar-overlay');
    const menuBtn = document.querySelector('.CDSFeed-mobile-menu-btn');

    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
    menuBtn.classList.toggle('active');
}

function closeFeedSidebar() {
    const sidebar = document.querySelector('.CDSFeed-sidebar');
    const overlay = document.querySelector('.CDSFeed-sidebar-overlay');
    const menuBtn = document.querySelector('.CDSFeed-mobile-menu-btn');

    sidebar.classList.remove('active');
    overlay.classList.remove('active');
    menuBtn.classList.remove('active');
}

</script>
@endpush