@extends('admin-panel.layouts.app')
@section('content')
<style>
            .invoice-box {
            max-width: 800px;
            margin: 2rem auto 0;
            padding: 20px;
            border: 1px solid #eee;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 8px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            /* padding-bottom: 20px; */
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .bill-to {
            text-align: left !important;
            border-left: 1px solid #ddd;
            padding: 13px !important;
        }
        .bill-from {
            text-align: left !important;
            padding: 13px !important;
        }
        .cds-invoice-logo { max-width: 170px;height: auto;background-color: #0a202b;padding: 7px;border-radius: 5px;}
    </style>

<section class="cdsTYMainsite-support01-section-wrap">
    <div class="cdsTYMainsite-support01-header-wrapper">
        <!-- Left Side: Image -->

        <!-- Centered Content -->
        <div class="cdsTYMainsite-support01-header-cover">
            <!-- Right Side: Text -->
            <div class="cdsTYMainsite-support01-text-container">
                <div class="cds-content">
                    <form id="payment-form" method="post" action="{{ baseUrl('invoices/pay/pay-for-global') }}">
                        @csrf 
                        @include("components.invoice-payment.support-via-stripe", ['record' => $record])
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
