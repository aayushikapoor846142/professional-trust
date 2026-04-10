/**
 * Transaction Types Chart
 * Custom HTML5 Canvas implementation for transaction types distribution
 */
class TransactionTypesChart {
    constructor(canvasId, data) {
        this.canvas = document.getElementById(canvasId);
        this.ctx = this.canvas.getContext('2d');
        this.data = data;
        this.width = this.canvas.width;
        this.height = this.canvas.height;
        
        this.init();
    }

    init() {
        this.drawChart();
        this.drawLegend();
    }

    drawChart() {
        const centerX = this.width / 2;
        const centerY = this.height / 2 - 30; // More offset for legend
        const radius = Math.min(centerX, centerY) - 50;
        
        // Clear canvas
        this.ctx.clearRect(0, 0, this.width, this.height);
        
        // Calculate total
        const total = this.data.reduce((sum, item) => sum + item.value, 0);
        
        // Colors for different transaction types
        const colors = [
            '#007bff', // Primary
            '#28a745', // Success
            '#ffc107', // Warning
            '#dc3545', // Danger
            '#6f42c1', // Purple
            '#fd7e14', // Orange
            '#20c997', // Teal
            '#e83e8c'  // Pink
        ];
        
        let currentAngle = -Math.PI / 2; // Start from top
        
        this.data.forEach((item, index) => {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            const color = colors[index % colors.length];
            
            // Draw slice
            this.drawSlice(centerX, centerY, radius, currentAngle, sliceAngle, color);
            
            // Draw slice border with increased width
            this.ctx.strokeStyle = '#ffffff';
            this.ctx.lineWidth = 3;
            this.ctx.stroke();
            
            currentAngle += sliceAngle;
        });
        
        // Draw center circle (donut effect)
        this.ctx.beginPath();
        this.ctx.arc(centerX, centerY, radius * 0.5, 0, 2 * Math.PI);
        this.ctx.fillStyle = '#ffffff';
        this.ctx.fill();
        
        // Draw center text
        this.ctx.fillStyle = '#6c757d';
        this.ctx.font = 'bold 14px Arial';
        this.ctx.textAlign = 'center';
        this.ctx.textBaseline = 'middle';
        this.ctx.fillText('Total', centerX, centerY - 8);
        
        this.ctx.font = 'bold 18px Arial';
        this.ctx.fillStyle = '#495057';
        this.ctx.fillText(this.formatNumber(total), centerX, centerY + 8);
    }

    drawSlice(centerX, centerY, radius, startAngle, sliceAngle, color) {
        this.ctx.beginPath();
        this.ctx.moveTo(centerX, centerY);
        this.ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
        this.ctx.closePath();
        this.ctx.fillStyle = color;
        this.ctx.fill();
    }

    drawLegend() {
        const legendY = this.height - 50;
        const legendSpacing = 180; // Increased spacing
        let legendX = 50;
        let currentRow = 0;
        const maxItemsPerRow = 2; // Limit items per row for better layout
        
        this.data.forEach((item, index) => {
            const colors = [
                '#007bff', '#28a745', '#ffc107', '#dc3545',
                '#6f42c1', '#fd7e14', '#20c997', '#e83e8c'
            ];
            const color = colors[index % colors.length];
            
            // Calculate position for multi-row layout
            const row = Math.floor(index / maxItemsPerRow);
            const col = index % maxItemsPerRow;
            const x = legendX + (col * legendSpacing);
            const y = legendY + (row * 25);
            
            // Draw legend color box with border
            this.ctx.fillStyle = color;
            this.ctx.fillRect(x, y - 8, 14, 14);
            
            // Draw border around legend box
            this.ctx.strokeStyle = '#e9ecef';
            this.ctx.lineWidth = 1;
            this.ctx.strokeRect(x, y - 8, 14, 14);
            
            // Draw legend text
            this.ctx.fillStyle = '#6c757d';
            this.ctx.font = '12px Arial';
            this.ctx.textAlign = 'left';
            
            const percentage = ((item.value / this.data.reduce((sum, i) => sum + i.value, 0)) * 100).toFixed(1);
            const legendText = `${item.label} (${percentage}%)`;
            this.ctx.fillText(legendText, x + 20, y + 2);
        });
    }

    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    updateData(newData) {
        this.data = newData;
        this.init();
    }
}

// Initialize chart when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const transactionTypesCanvas = document.getElementById('transactionTypesChart');
    if (transactionTypesCanvas) {
        // Get data from the page
        const transactionTypesData = window.transactionTypesData || [
            { label: 'Subscriptions', value: 45 },
            { label: 'One-time Payments', value: 30 },
            { label: 'Refunds', value: 15 },
            { label: 'Credits', value: 10 }
        ];
        
        // Store chart instance globally for updates
        window.transactionTypesChart = new TransactionTypesChart('transactionTypesChart', transactionTypesData);
    }
}); 