// Admin Dashboard Enhanced JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard animations
    initializeAnimations();
    
    // Initialize real-time updates
    initializeRealTimeUpdates();
    
    // Initialize interactive elements
    initializeInteractiveElements();
    
    // Initialize performance monitoring
    initializePerformanceMonitoring();
    
    console.log('Admin Dashboard enhanced JavaScript loaded successfully!');
});

// Animation system
function initializeAnimations() {
    // Animate stats cards with staggered timing
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        const delay = parseInt(card.dataset.animationDelay) || (index * 100);
        
        setTimeout(() => {
            card.classList.add('animate-in');
            animateStatNumber(card);
        }, delay);
    });
    
    // Animate dashboard cards
    const dashboardCards = document.querySelectorAll('.dashboard-card');
    dashboardCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('animate-in');
        }, 600 + (index * 200));
    });
    
    // Animate game items
    const gameItems = document.querySelectorAll('.game-item');
    gameItems.forEach((item, index) => {
        const delay = parseInt(item.dataset.animationDelay) || (index * 50);
        setTimeout(() => {
            item.classList.add('animate-in');
        }, 1000 + delay);
    });
}

// Animate stat numbers counting up
function animateStatNumber(card) {
    const numberElement = card.querySelector('.stat-number');
    if (!numberElement) return;
    
    const finalValue = parseInt(numberElement.textContent.replace(/,/g, '')) || 0;
    const duration = 2000; // 2 seconds
    const startTime = performance.now();
    
    function updateNumber(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutQuart = 1 - Math.pow(1 - progress, 4);
        const currentValue = Math.floor(finalValue * easeOutQuart);
        
        numberElement.textContent = currentValue.toLocaleString();
        
        if (progress < 1) {
            requestAnimationFrame(updateNumber);
        } else {
            numberElement.textContent = finalValue.toLocaleString();
        }
    }
    
    numberElement.textContent = '0';
    requestAnimationFrame(updateNumber);
}

// Real-time updates simulation
function initializeRealTimeUpdates() {
    // Simulate real-time data updates
    setInterval(() => {
        updateSystemStatus();
        updateMiniCharts();
    }, 30000); // Update every 30 seconds
    
    // Initial chart setup
    setTimeout(initializeMiniCharts, 2000);
}

// Update system status indicators
function updateSystemStatus() {
    const indicators = document.querySelectorAll('.status-indicator');
    indicators.forEach(indicator => {
        // Simulate status check (in real app, this would be an API call)
        const isOnline = Math.random() > 0.1; 
        
        indicator.className = 'status-indicator ' + (isOnline ? 'online' : 'warning');
        
        const statusText = indicator.parentElement.querySelector('.status-description');
        if (statusText) {
            statusText.textContent = isOnline ? 'Operational' : 'Checking...';
        }
    });
}

// Initialize mini charts
function initializeMiniCharts() {
    const charts = document.querySelectorAll('.mini-chart');
    charts.forEach(chart => {
        createMiniChart(chart);
    });
}

// Create animated mini charts
function createMiniChart(chartElement) {
    // Generate sample data
    const dataPoints = 20;
    const data = Array.from({ length: dataPoints }, (_, i) => {
        const base = 50;
        const trend = i * 2; // Upward trend
        const noise = (Math.random() - 0.5) * 20;
        return Math.max(10, Math.min(90, base + trend + noise));
    });
    
    // Create SVG chart
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '100%');
    svg.setAttribute('height', '100%');
    svg.setAttribute('viewBox', '0 0 200 60');
    
    // Create path
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    const pathData = generatePathData(data, 200, 60);
    
    path.setAttribute('d', pathData);
    path.setAttribute('fill', 'none');
    path.setAttribute('stroke', 'rgba(16, 185, 129, 0.6)');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');
    
    // Add gradient fill
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
    gradient.setAttribute('id', `gradient-${Date.now()}-${Math.random()}`);
    gradient.setAttribute('x1', '0%');
    gradient.setAttribute('y1', '0%');
    gradient.setAttribute('x2', '0%');
    gradient.setAttribute('y2', '100%');
    
    const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop1.setAttribute('offset', '0%');
    stop1.setAttribute('stop-color', 'rgba(16, 185, 129, 0.3)');
    
    const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop2.setAttribute('offset', '100%');
    stop2.setAttribute('stop-color', 'rgba(16, 185, 129, 0.05)');
    
    gradient.appendChild(stop1);
    gradient.appendChild(stop2);
    defs.appendChild(gradient);
    
    // Create filled area
    const area = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    const areaData = generateAreaData(data, 200, 60);
    area.setAttribute('d', areaData);
    area.setAttribute('fill', `url(#${gradient.getAttribute('id')})`);
    
    svg.appendChild(defs);
    svg.appendChild(area);
    svg.appendChild(path);
    
    // Clear existing content and add chart
    chartElement.innerHTML = '';
    chartElement.appendChild(svg);
    
    // Animate chart drawing
    const pathLength = path.getTotalLength();
    path.style.strokeDasharray = pathLength;
    path.style.strokeDashoffset = pathLength;
    
    setTimeout(() => {
        path.style.transition = 'stroke-dashoffset 2s ease-in-out';
        path.style.strokeDashoffset = 0;
    }, 100);
}

// Generate SVG path data
function generatePathData(data, width, height) {
    const stepX = width / (data.length - 1);
    let path = '';
    
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - (value / 100) * height;
        
        if (index === 0) {
            path += `M ${x} ${y}`;
        } else {
            path += ` L ${x} ${y}`;
        }
    });
    
    return path;
}

// Generate SVG area data
function generateAreaData(data, width, height) {
    const stepX = width / (data.length - 1);
    let path = '';
    
    // Start at bottom left
    path += `M 0 ${height}`;
    
    // Draw to first point
    path += ` L 0 ${height - (data[0] / 100) * height}`;
    
    // Draw the data curve
    data.forEach((value, index) => {
        const x = index * stepX;
        const y = height - (value / 100) * height;
        path += ` L ${x} ${y}`;
    });
    
    // Close the area at bottom right
    path += ` L ${width} ${height} Z`;
    
    return path;
}

// Update mini charts with new data
function updateMiniCharts() {
    const charts = document.querySelectorAll('.mini-chart svg');
    charts.forEach(chart => {
        const parentChart = chart.parentElement;
        createMiniChart(parentChart);
    });
}

// Interactive elements
function initializeInteractiveElements() {
    // Hover effects for stat cards
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px) scale(1.02)';
            card.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.4)';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(-5px) scale(1)';
            card.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.3)';
        });
    });
    
    // Interactive action items
    const actionItems = document.querySelectorAll('.action-item');
    actionItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            const icon = item.querySelector('.action-icon');
            icon.style.transform = 'scale(1.1) rotate(5deg)';
        });
        
        item.addEventListener('mouseleave', () => {
            const icon = item.querySelector('.action-icon');
            icon.style.transform = 'scale(1) rotate(0deg)';
        });
        
        // Click effect
        item.addEventListener('click', (e) => {
            item.style.transform = 'scale(0.98)';
            setTimeout(() => {
                item.style.transform = '';
            }, 150);
        });
    });
    
    // Game item interactions
    const gameItems = document.querySelectorAll('.game-item');
    gameItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            const avatar = item.querySelector('.player-avatar');
            avatar.style.transform = 'scale(1.1)';
            avatar.style.boxShadow = '0 8px 20px rgba(16, 185, 129, 0.4)';
        });
        
        item.addEventListener('mouseleave', () => {
            const avatar = item.querySelector('.player-avatar');
            avatar.style.transform = 'scale(1)';
            avatar.style.boxShadow = 'none';
        });
    });
}

// Performance monitoring
function initializePerformanceMonitoring() {
    // Animate performance bars
    const metricBars = document.querySelectorAll('.metric-fill');
    metricBars.forEach((bar, index) => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-out';
            bar.style.width = targetWidth;
        }, 1500 + (index * 200));
    });
    
    // Monitor page performance
    if ('performance' in window) {
        const navigation = performance.getEntriesByType('navigation')[0];
        const loadTime = navigation.loadEventEnd - navigation.loadEventStart;
        
        console.log(`Dashboard loaded in ${loadTime.toFixed(2)}ms`);
        
        // You could display this in the UI
        const performanceIndicator = document.createElement('div');
        performanceIndicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            border: 1px solid rgba(16, 185, 129, 0.2);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        `;
        performanceIndicator.textContent = `Load: ${loadTime.toFixed(0)}ms`;
        document.body.appendChild(performanceIndicator);
        
        setTimeout(() => {
            performanceIndicator.style.opacity = '1';
        }, 2000);
        
        setTimeout(() => {
            performanceIndicator.style.opacity = '0';
            setTimeout(() => {
                performanceIndicator.remove();
            }, 300);
        }, 5000);
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Alt + D for dashboard
    if (e.altKey && e.key === 'd') {
        e.preventDefault();
        window.location.href = '/admin/dashboard';
    }
    
    // Alt + U for users
    if (e.altKey && e.key === 'u') {
        e.preventDefault();
        window.location.href = '/admin/users';
    }
    
    // Alt + S for statistics
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        window.location.href = '/admin/statistics';
    }
    
    // Escape to refresh
    if (e.key === 'Escape') {
        location.reload();
    }
});

// Utility functions
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function formatDuration(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Add CSS for enhanced interactions
const style = document.createElement('style');
style.textContent = `
    .stat-card {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .action-icon {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .player-avatar {
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .action-item {
        transition: transform 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .loading-shimmer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 1.5s infinite;
    }
`;
document.head.appendChild(style);
