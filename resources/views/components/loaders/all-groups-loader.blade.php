<style>
/* Skeleton Loader Styles */
.skeleton-loader {
    animation: skeleton-loading 1.5s ease-in-out infinite;
}

@keyframes skeleton-loading {
    0% {
        background-color: #f0f0f0;
    }
    50% {
        background-color: #e0e0e0;
    }
    100% {
        background-color: #f0f0f0;
    }
}

.skeleton-group-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    height: 280px;
    display: flex;
    flex-direction: column;
}

.skeleton-banner {
    height: 80px;
    background: #f0f0f0;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.skeleton-group-image {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.skeleton-request-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    height: 20px;
    background: #e0e0e0;
    border-radius: 10px;
    width: 80px;
}

.skeleton-content {
    padding: 16px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.skeleton-group-name {
    height: 20px;
    background: #f0f0f0;
    border-radius: 4px;
    margin-bottom: 8px;
    width: 80%;
}

.skeleton-description {
    height: 14px;
    background: #f0f0f0;
    border-radius: 4px;
    margin-bottom: 6px;
    width: 100%;
}

.skeleton-description:nth-child(2) {
    width: 70%;
}

.skeleton-members-section {
    margin: 16px 0;
    flex: 1;
}

.skeleton-members-label {
    height: 14px;
    background: #f0f0f0;
    border-radius: 4px;
    width: 60px;
    margin-bottom: 12px;
    display: block;
}

.skeleton-avatars-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.skeleton-avatar-stack {
    display: flex;
    align-items: center;
}

.skeleton-avatar {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f0f0f0;
    margin-right: -6px;
    border: 2px solid #ffffff;
}

.skeleton-group-type {
    height: 14px;
    background: #f0f0f0;
    border-radius: 4px;
    width: 80px;
}

.skeleton-join-button {
    height: 36px;
    background: #f0f0f0;
    border-radius: 6px;
    width: 100%;
    margin-top: auto;
}

/* Multiple skeleton cards */
.skeleton-loader-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.skeleton-group-card:nth-child(2) .skeleton-group-name {
    width: 70%;
}

.skeleton-group-card:nth-child(3) .skeleton-group-name {
    width: 85%;
}

.skeleton-group-card:nth-child(2) .skeleton-description:nth-child(2) {
    width: 60%;
}

.skeleton-group-card:nth-child(3) .skeleton-description:nth-child(2) {
    width: 80%;
}

.skeleton-group-card:nth-child(4) .skeleton-group-name {
    width: 75%;
}

.skeleton-group-card:nth-child(5) .skeleton-group-name {
    width: 65%;
}

.skeleton-group-card:nth-child(4) .skeleton-description:nth-child(2) {
    width: 65%;
}

.skeleton-group-card:nth-child(5) .skeleton-description:nth-child(2) {
    width: 75%;
}
</style>

<div class="skeleton-loader-container">
    <!-- Skeleton Card 1 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
            <div class="skeleton-request-badge"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>

    <!-- Skeleton Card 2 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>

    <!-- Skeleton Card 3 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
            <div class="skeleton-request-badge"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>

    <!-- Skeleton Card 4 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>

    <!-- Skeleton Card 5 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
            <div class="skeleton-request-badge"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>

    <!-- Skeleton Card 6 -->
    <div class="skeleton-group-card skeleton-loader">
        <div class="skeleton-banner">
            <div class="skeleton-group-image"></div>
        </div>
        <div class="skeleton-content">
            <div class="skeleton-group-name"></div>
            <div class="skeleton-description"></div>
            <div class="skeleton-description"></div>
            
            <div class="skeleton-members-section">
                <span class="skeleton-members-label"></span>
                <div class="skeleton-avatars-container">
                    <div class="skeleton-avatar-stack">
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                        <div class="skeleton-avatar"></div>
                    </div>
                    <div class="skeleton-group-type"></div>
                </div>
            </div>
            
            <div class="skeleton-join-button"></div>
        </div>
    </div>
</div>
