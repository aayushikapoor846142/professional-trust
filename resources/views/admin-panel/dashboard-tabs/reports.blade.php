

<!-- Dashboard Container -->
<main class="cdsTYDashboard-main-main-content">
    <div class="CdsEarningOverview-container">
        <div class="container mb-4">
            <section class="cdsTYSupportDashboard-point-earned">
                <div class="cdsTYSupportDashboard-point-earned-container">
                    @include("components.points-earn-progress-bar",['bg_user'=>auth()->user()])
                </div>
            </section>        
        </div>
    </div>
</main>
