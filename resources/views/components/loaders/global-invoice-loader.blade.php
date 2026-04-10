<style>
/* Invoice Skeleton Loader Styles */
.invoice-skeleton-loader {
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

@keyframes skeleton-pulse {
    0% { opacity: 0.6; }
    100% { opacity: 1; }
}

.invoice-skeleton-item {
    padding: 20px 25px;
    border-bottom: 1px solid #f7fafc;
    transition: all 0.3s;
    position: relative;
}

.invoice-skeleton-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr auto;
    gap: 20px;
    align-items: center;
}

.invoice-skeleton-customer {
    display: flex;
    align-items: center;
    gap: 12px;
}

.invoice-skeleton-avatar {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #e2e8f0;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-customer-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.invoice-skeleton-name {
    height: 16px;
    width: 120px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-email {
    height: 14px;
    width: 180px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-invoice-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.invoice-skeleton-invoice-number {
    height: 16px;
    width: 100px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-invoice-date {
    height: 14px;
    width: 80px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-gateway {
    height: 16px;
    width: 90px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-link {
    height: 32px;
    width: 80px;
    background: #e2e8f0;
    border-radius: 6px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-amount {
    height: 18px;
    width: 70px;
    background: #e2e8f0;
    border-radius: 4px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-status {
    height: 28px;
    width: 60px;
    background: #e2e8f0;
    border-radius: 14px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

.invoice-skeleton-actions {
    height: 32px;
    width: 32px;
    background: #e2e8f0;
    border-radius: 6px;
    animation: skeleton-pulse 1.5s ease-in-out infinite alternate;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .invoice-skeleton-content {
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
    }
    
    .invoice-skeleton-tax-value,
    .invoice-skeleton-date-value {
        display: none;
    }
}

@media (max-width: 768px) {
    .invoice-skeleton-content {
        grid-template-columns: 1fr 1fr 1fr auto;
    }
    
    .invoice-skeleton-amount-value,
    .invoice-skeleton-tax-value,
    .invoice-skeleton-date-value {
        display: none;
    }
}

@media (max-width: 767px) {
    .invoice-skeleton-content {
        grid-template-columns: auto;
        gap: 15px;
    }
    
    .invoice-skeleton-customer {
        justify-content: center;
    }
    
    .invoice-skeleton-invoice-info,
    .invoice-skeleton-gateway,
    .invoice-skeleton-link,
    .invoice-skeleton-amount,
    .invoice-skeleton-status {
        text-align: center;
    }
}
</style>

<!-- Invoice Skeleton Loader -->
<div class="invoice-skeleton-loader">
    <!-- Skeleton Item 1 -->
    <div class="invoice-skeleton-item">
        <div class="invoice-skeleton-content">
            <!-- Customer Details -->
            <div class="invoice-skeleton-customer">
                <div class="invoice-skeleton-avatar"></div>
                <div class="invoice-skeleton-customer-info">
                    <div class="invoice-skeleton-name"></div>
                    <div class="invoice-skeleton-email"></div>
                </div>
            </div>
            
            <!-- Invoice Info -->
            <div class="invoice-skeleton-invoice-info">
                <div class="invoice-skeleton-invoice-number"></div>
                <div class="invoice-skeleton-invoice-date"></div>
            </div>
            
            <!-- Payment Gateway -->
            <div class="invoice-skeleton-gateway"></div>
            
            <!-- Copy Link -->
            <div class="invoice-skeleton-link"></div>
            
            <!-- Amount -->
            <div class="invoice-skeleton-amount"></div>
            
            <!-- Status -->
            <div class="invoice-skeleton-status"></div>
            
            <!-- Actions -->
            <div class="invoice-skeleton-actions"></div>
        </div>
    </div>

    <!-- Skeleton Item 2 -->
    <div class="invoice-skeleton-item">
        <div class="invoice-skeleton-content">
            <!-- Customer Details -->
            <div class="invoice-skeleton-customer">
                <div class="invoice-skeleton-avatar"></div>
                <div class="invoice-skeleton-customer-info">
                    <div class="invoice-skeleton-name"></div>
                    <div class="invoice-skeleton-email"></div>
                </div>
            </div>
            
            <!-- Invoice Info -->
            <div class="invoice-skeleton-invoice-info">
                <div class="invoice-skeleton-invoice-number"></div>
                <div class="invoice-skeleton-invoice-date"></div>
            </div>
            
            <!-- Payment Gateway -->
            <div class="invoice-skeleton-gateway"></div>
            
            <!-- Copy Link -->
            <div class="invoice-skeleton-link"></div>
            
            <!-- Amount -->
            <div class="invoice-skeleton-amount"></div>
            
            <!-- Status -->
            <div class="invoice-skeleton-status"></div>
            
            <!-- Actions -->
            <div class="invoice-skeleton-actions"></div>
        </div>
    </div>

    <!-- Skeleton Item 3 -->
    <div class="invoice-skeleton-item">
        <div class="invoice-skeleton-content">
            <!-- Customer Details -->
            <div class="invoice-skeleton-customer">
                <div class="invoice-skeleton-avatar"></div>
                <div class="invoice-skeleton-customer-info">
                    <div class="invoice-skeleton-name"></div>
                    <div class="invoice-skeleton-email"></div>
                </div>
            </div>
            
            <!-- Invoice Info -->
            <div class="invoice-skeleton-invoice-info">
                <div class="invoice-skeleton-invoice-number"></div>
                <div class="invoice-skeleton-invoice-date"></div>
            </div>
            
            <!-- Payment Gateway -->
            <div class="invoice-skeleton-gateway"></div>
            
            <!-- Copy Link -->
            <div class="invoice-skeleton-link"></div>
            
            <!-- Amount -->
            <div class="invoice-skeleton-amount"></div>
            
            <!-- Status -->
            <div class="invoice-skeleton-status"></div>
            
            <!-- Actions -->
            <div class="invoice-skeleton-actions"></div>
        </div>
    </div>

    <!-- Skeleton Item 4 -->
    <div class="invoice-skeleton-item">
        <div class="invoice-skeleton-content">
            <!-- Customer Details -->
            <div class="invoice-skeleton-customer">
                <div class="invoice-skeleton-avatar"></div>
                <div class="invoice-skeleton-customer-info">
                    <div class="invoice-skeleton-name"></div>
                    <div class="invoice-skeleton-email"></div>
                </div>
            </div>
            
            <!-- Invoice Info -->
            <div class="invoice-skeleton-invoice-info">
                <div class="invoice-skeleton-invoice-number"></div>
                <div class="invoice-skeleton-invoice-date"></div>
            </div>
            
            <!-- Payment Gateway -->
            <div class="invoice-skeleton-gateway"></div>
            
            <!-- Copy Link -->
            <div class="invoice-skeleton-link"></div>
            
            <!-- Amount -->
            <div class="invoice-skeleton-amount"></div>
            
            <!-- Status -->
            <div class="invoice-skeleton-status"></div>
            
            <!-- Actions -->
            <div class="invoice-skeleton-actions"></div>
        </div>
    </div>

    <!-- Skeleton Item 5 -->
    <div class="invoice-skeleton-item">
        <div class="invoice-skeleton-content">
            <!-- Customer Details -->
            <div class="invoice-skeleton-customer">
                <div class="invoice-skeleton-avatar"></div>
                <div class="invoice-skeleton-customer-info">
                    <div class="invoice-skeleton-name"></div>
                    <div class="invoice-skeleton-email"></div>
                </div>
            </div>
            
            <!-- Invoice Info -->
            <div class="invoice-skeleton-invoice-info">
                <div class="invoice-skeleton-invoice-number"></div>
                <div class="invoice-skeleton-invoice-date"></div>
            </div>
            
            <!-- Payment Gateway -->
            <div class="invoice-skeleton-gateway"></div>
            
            <!-- Copy Link -->
            <div class="invoice-skeleton-link"></div>
            
            <!-- Amount -->
            <div class="invoice-skeleton-amount"></div>
            
            <!-- Status -->
            <div class="invoice-skeleton-status"></div>
            
            <!-- Actions -->
            <div class="invoice-skeleton-actions"></div>
        </div>
    </div>
</div>