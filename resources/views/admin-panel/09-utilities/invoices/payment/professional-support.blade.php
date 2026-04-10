@extends('admin-panel.layouts.app')
@section('content')

<section class="cdsTYMainsite-support01-section-wrap">
    <div class="cdsTYMainsite-support01-header-wrapper">
        <!-- Left Side: Image -->

        <!-- Centered Content -->
        <div class="cdsTYMainsite-support01-header-cover">
            <!-- Right Side: Text -->
            <div class="cdsTYMainsite-support01-text-container">
                <div class="cds-content">
                    <form id="payment-form" method="post" action="{{ baseUrl('invoices/pay-for-support') }}">
                        @csrf 
                        @include("admin-panel.09-utilities.invoices.payment.support-via-stripe")
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    const tabLinks = document.querySelectorAll('.CDSMainsite-general-content-external-tab-buttons-desktop a');
    const tabLinks2 = document.querySelectorAll('.CDSMainsite-general-content-external-tab-buttons-mb a');
    const tabsContainer = document.getElementById('tabsComponent');
    const tabContents = tabsContainer.querySelectorAll('.CDSMainsite-general-content-external-tab-content');
    const loader = tabsContainer.querySelector('.CDSMainsite-general-content-external-loader');

    let resetTimeout;
    let activeTabIndex = 0;

    function activateTab(index) {
        if (index === activeTabIndex) return; // 🔒 prevent reloading the same tab

        clearTimeout(resetTimeout);
        activeTabIndex = index;

        tabsContainer.classList.add('loading');

        setTimeout(() => {
            tabLinks.forEach(link => link.classList.remove('active'));
            tabLinks[index].classList.add('active');

            tabLinks2.forEach(link => link.classList.remove('active'));
            tabLinks2[index].classList.add('active');

            tabContents.forEach((tab, i) => {
                tab.classList.remove('active');
                if (i === index) {
                    tab.classList.add('active');
                }
            });

            tabsContainer.classList.remove('loading');
        }, 500);
    }

    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(link.getAttribute('data-tab'));
            activateTab(index);
        });
    });
    tabLinks2.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const index = parseInt(link.getAttribute('data-tab'));
            activateTab(index);
        });
    });

    // Detect real clicks (not scrolls)
</script>


@endsection
