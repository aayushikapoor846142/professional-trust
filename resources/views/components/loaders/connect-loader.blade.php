<style>
    .skeleton-loader {
        animation: skeleton-loading 1.5s ease-in-out infinite alternate;
    }
    
    @keyframes skeleton-loading {
        0% {
            background-color: #f0f0f0;
        }
        100% {
            background-color: #e0e0e0;
        }
    }
    
    .skeleton-card {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .skeleton-profile {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        margin: 0 auto 15px;
        skeleton-loader: true;
    }
    
    .skeleton-title {
        height: 20px;
        width: 70%;
        margin: 0 auto 10px;
        border-radius: 4px;
        skeleton-loader: true;
    }
    
    .skeleton-text {
        height: 14px;
        width: 90%;
        margin: 0 auto 8px;
        border-radius: 4px;
        skeleton-loader: true;
    }
    
    .skeleton-text-short {
        height: 14px;
        width: 60%;
        margin: 0 auto 15px;
        border-radius: 4px;
        skeleton-loader: true;
    }
    
    .skeleton-button {
        height: 36px;
        width: 100px;
        border-radius: 6px;
        margin: 5px;
        skeleton-loader: true;
    }
    
    .skeleton-button-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: auto;
    }
</style>

<div class="row">
    @for($i = 0; $i < 6; $i++)
        <div class="col-xl-4 col-md-6 col-lg-6 mb-4">
            <div class="skeleton-card">
                <div class="text-center">
                    <div class="skeleton-profile skeleton-loader"></div>
                </div>
                <div class="skeleton-title skeleton-loader"></div>
                <div class="skeleton-text skeleton-loader"></div>
                <div class="skeleton-text-short skeleton-loader"></div>
                <div class="skeleton-button-container">
                    <div class="skeleton-button skeleton-loader"></div>
                    <div class="skeleton-button skeleton-loader"></div>
                </div>
            </div>
        </div>
    @endfor
</div>
