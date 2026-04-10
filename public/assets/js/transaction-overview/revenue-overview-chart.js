/**
 * Revenue Overview Chart
 * Custom HTML5 Canvas implementation for revenue trends
 */
class RevenueOverviewChart {
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
        const { labels, datasets } = this.data;
        const padding = 60; // Increased padding for better spacing
        const chartWidth = this.width - (padding * 2);
        const chartHeight = this.height - (padding * 2) - 80; // More space for legend and labels
        
        // Clear canvas
        this.ctx.clearRect(0, 0, this.width, this.height);
        
        // Find min and max values
        const allValues = datasets.flatMap(dataset => dataset.data);
        const minValue = Math.min(...allValues);
        const maxValue = Math.max(...allValues);
        const valueRange = maxValue - minValue;
        
        // Calculate scales
        const xStep = chartWidth / (labels.length - 1);
        const yScale = chartHeight / valueRange;
        
        // Draw grid lines
        this.drawGrid(chartWidth, chartHeight, padding, labels.length, valueRange, yScale);
        
        // Draw datasets
        datasets.forEach((dataset, datasetIndex) => {
            this.drawDataset(dataset, labels, xStep, yScale, padding, chartHeight, datasetIndex);
        });
        
        // Draw axis labels
        this.drawAxisLabels(labels, chartWidth, chartHeight, padding, xStep);
    }

    drawGrid(chartWidth, chartHeight, padding, labelCount, valueRange, yScale) {
        this.ctx.strokeStyle = '#e9ecef';
        this.ctx.lineWidth = 1;
        
        // Horizontal grid lines
        const gridLines = 5;
        for (let i = 0; i <= gridLines; i++) {
            const y = padding + (chartHeight / gridLines) * i;
            this.ctx.beginPath();
            this.ctx.moveTo(padding, y);
            this.ctx.lineTo(padding + chartWidth, y);
            this.ctx.stroke();
        }
        
        // Vertical grid lines
        for (let i = 0; i < labelCount; i++) {
            const x = padding + (chartWidth / (labelCount - 1)) * i;
            this.ctx.beginPath();
            this.ctx.moveTo(x, padding);
            this.ctx.lineTo(x, padding + chartHeight);
            this.ctx.stroke();
        }
    }

    drawDataset(dataset, labels, xStep, yScale, padding, chartHeight, datasetIndex) {
        const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545'];
        const color = colors[datasetIndex % colors.length];
        
        this.ctx.strokeStyle = color;
        this.ctx.fillStyle = color;
        this.ctx.lineWidth = 3;
        
        // Draw line
        this.ctx.beginPath();
        dataset.data.forEach((value, index) => {
            const x = padding + (xStep * index);
            // Ensure y value is within chart bounds
            const yValue = Math.max(0, Math.min(chartHeight, (value - Math.min(...dataset.data)) * yScale));
            const y = padding + chartHeight - yValue;
            
            if (index === 0) {
                this.ctx.moveTo(x, y);
            } else {
                this.ctx.lineTo(x, y);
            }
        });
        this.ctx.stroke();
        
        // Draw points
        dataset.data.forEach((value, index) => {
            const x = padding + (xStep * index);
            // Ensure y value is within chart bounds
            const yValue = Math.max(0, Math.min(chartHeight, (value - Math.min(...dataset.data)) * yScale));
            const y = padding + chartHeight - yValue;
            
            this.ctx.beginPath();
            this.ctx.arc(x, y, 4, 0, 2 * Math.PI);
            this.ctx.fill();
        });
    }

    drawAxisLabels(labels, chartWidth, chartHeight, padding, xStep) {
        this.ctx.fillStyle = '#6c757d';
        this.ctx.font = '12px Arial';
        this.ctx.textAlign = 'center';
        
        // X-axis labels
        labels.forEach((label, index) => {
            const x = padding + (xStep * index);
            const y = padding + chartHeight + 30; // Increased spacing
            this.ctx.fillText(label, x, y);
        });
        
        // Y-axis labels (optional)
        this.ctx.textAlign = 'right';
        this.ctx.font = '10px Arial';
        const maxValue = Math.max(...this.data.datasets[0].data);
        const minValue = Math.min(...this.data.datasets[0].data);
        const range = maxValue - minValue;
        
        for (let i = 0; i <= 5; i++) {
            const value = minValue + (range / 5) * i;
            const y = padding + (chartHeight / 5) * i;
            this.ctx.fillText('$' + value.toLocaleString(), padding - 10, y + 4);
        }
    }

    drawLegend() {
        const legendY = this.height - 20; // Moved up slightly
        const legendSpacing = 120;
        let legendX = 60; // Aligned with chart padding
        
        this.data.datasets.forEach((dataset, index) => {
            const colors = ['#007bff', '#28a745', '#ffc107', '#dc3545'];
            const color = colors[index % colors.length];
            
            // Draw legend color box
            this.ctx.fillStyle = color;
            this.ctx.fillRect(legendX, legendY - 8, 12, 12);
            
            // Draw legend text
            this.ctx.fillStyle = '#6c757d';
            this.ctx.font = '12px Arial';
            this.ctx.textAlign = 'left';
            this.ctx.fillText(dataset.label, legendX + 20, legendY + 2);
            
            legendX += legendSpacing;
        });
    }

    updateData(newData) {
        this.data = newData;
        this.init();
    }
}

// Initialize chart when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    const revenueChartCanvas = document.getElementById('revenueOverviewChart');
    if (revenueChartCanvas) {
        // Get data from the page
        const revenueData = window.revenueChartData || {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Revenue',
                    data: [12000, 19000, 15000, 25000, 22000, 30000]
                }
            ]
        };
        
        // Store chart instance globally for updates
        window.revenueChart = new RevenueOverviewChart('revenueOverviewChart', revenueData);
    }
}); 