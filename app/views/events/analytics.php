<?php
$title = "Event Analytics - " . htmlspecialchars($event['title']);
// Header is included by the router/renderView function
?>

<div class="bg-gray-50 min-h-screen pb-12">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                     <a href="<?php echo BASE_URL; ?>organizer/dashboard" class="flex items-center text-charcoal-500 hover:text-brand-600 mb-2 transition">
                        <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Back to Dashboard
                    </a>
                    <h1 class="text-2xl font-bold text-charcoal-900">Analytics</h1>
                    <p class="text-charcoal-600 text-sm mt-1"><?php echo htmlspecialchars($event['title']); ?></p>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="p-2 text-charcoal-500 hover:bg-gray-100 rounded-lg transition">
                        <i data-lucide="printer" class="w-5 h-5"></i>
                    </button>
                    <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>" class="bg-white border border-charcoal-200 text-charcoal-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center">
                        View Event <i data-lucide="external-link" class="w-4 h-4 ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <h2 class="text-lg font-bold text-charcoal-900 mb-6">Event Analytics</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Reg -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="text-center">
                    <p class="text-4xl font-bold text-brand-600 mb-1"><?php echo $analytics['total_registrations']; ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">TOTAL REG</p>
                </div>
            </div>

            <!-- Checked In -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="text-center">
                    <p class="text-4xl font-bold text-green-600 mb-1"><?php echo $analytics['checked_in']; ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CHECKED IN</p>
                </div>
            </div>

            <!-- Confirmed -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="text-center">
                    <p class="text-4xl font-bold text-purple-600 mb-1"><?php echo $analytics['confirmed']; ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CONFIRMED</p>
                </div>
            </div>

            <!-- Cancelled -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="text-center">
                    <p class="text-4xl font-bold text-red-500 mb-1"><?php echo $analytics['cancelled']; ?></p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">CANCELLED</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Chart Section -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-charcoal-900">Registration Trend (Last 7 Days)</h3>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="regChart"></canvas>
                </div>
            </div>

            <!-- Ticket Types -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm h-fit">
                <h3 class="font-bold text-charcoal-900 mb-4">Ticket Types</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-charcoal-700">General Admission</span>
                        <span class="text-sm font-bold text-charcoal-900"><?php echo $analytics['total_registrations']; ?></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('regChart').getContext('2d');
        
        // Prepare data from PHP
        const trendData = <?php echo json_encode($analytics['trend']); ?>;
        const labels = trendData.map(item => {
            const date = new Date(item.date);
            return (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
        });
        const dataPoints = trendData.map(item => item.count);

        // Fill in missing dates for smoother line if necessary (basic implementation uses actual data points)
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['No Data'],
                datasets: [{
                    label: 'Registrations',
                    data: dataPoints.length ? dataPoints : [0],
                    borderColor: '#4F46E5', // Brand Blue/Indigo
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, // Smooth curves
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#4F46E5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        padding: 10,
                        cornerRadius: 8,
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F3F4F6',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>


