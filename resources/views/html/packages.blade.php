@extends('admin-panel.layouts.app')
@section('content')
    <!-- Membership Plan -->
    <section class="cds-packages">
        <div class="container">
            <h2 class="section-title">Membership Plan</h2>
            <!-- Toggle Switch -->
            <div class="cds-switch">
                <span class="left-text">plan 1</span>
                <label class="switch">
                    <input type="checkbox" class="plan-toggle" id="basic-toggle">
                    <span class="slider round"></span>
                </label>
                <span class="right-text">plan 2</span>
            </div>
            <div class="packages-plans">
                <!-- Plan 1 -->
                <div class="plan">
                    <h3 class="plan-title">Basic</h3>
                    <p class="price">$19<span>/month</span></p>
                    <div class="desc">
                        <p class="">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit dolorum tempora architecto similique repellat molestias voluptates expedita. Et quos, neque consequatur veritatis iusto ad illum eius perferendis, alias nemo fugit.</p>
                    </div>
                    <button class="CdsTYButton-btn-primary">Choose Plan</button>
                </div>
                <!-- Plan 2 -->
                <div class="plan featured">
                    <h3 class="plan-title">Standard</h3>
                    <p class="price">$49<span>/month</span></p>
                    <div class="desc">
                        <p class="">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit dolorum tempora architecto similique repellat molestias voluptates expedita. Et quos, neque consequatur veritatis iusto ad illum eius perferendis, alias nemo fugit.</p>
                    </div>
                    <button class="btn">Choose Plan</button>
                </div>
                <!-- Plan 3 -->
                <div class="plan">
                    <h3 class="plan-title">Premium</h3>
                    <p class="price">$99<span>/month</span></p>
                    <div class="desc">
                        <p class="">Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit dolorum tempora architecto similique repellat molestias voluptates expedita. Et quos, neque consequatur veritatis iusto ad illum eius perferendis, alias nemo fugit.</p>
                    </div>
                    <button class="btn">Choose Plan</button>
                </div>
            </div>
        </div>
    </section>
    <!-- # -->
@endsection
@section('javascript')
<script>
</script>
@endsection