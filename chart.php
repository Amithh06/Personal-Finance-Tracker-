<?php
// Start the session
session_start(); 
include 'db.php'; // Include the database connection

// Fetch data for the charts
$yearlyChartQuery = "SELECT year, SUM(amount) AS total_amount FROM user_expense WHERE month='em' GROUP BY year";
$yearlyChartResult = mysqli_query($conn, $yearlyChartQuery);

$monthlyChartQuery = "SELECT month, SUM(amount) AS total_amount FROM user_expense WHERE month!='em' GROUP BY month";
$monthlyChartResult = mysqli_query($conn, $monthlyChartQuery);

$expenseCategoryQuery = "SELECT expense_name, SUM(amount) AS total_amount FROM user_expense GROUP BY expense_name";
$expenseCategoryResult = mysqli_query($conn, $expenseCategoryQuery);

$yearlyData = [];
$monthlyData = [];
$categoryData = [];

while ($row = mysqli_fetch_assoc($yearlyChartResult)) {
    $yearlyData[] = $row;
}

while ($row = mysqli_fetch_assoc($monthlyChartResult)) {
    $monthlyData[] = $row;
}

while ($row = mysqli_fetch_assoc($expenseCategoryResult)) {
    $categoryData[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Visualization</title>
    <script src="chart.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #000; /* Black background for trading theme */
            color: #fff; /* White text for high contrast */
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #fff; /* White heading */
        }

        .chart-container {
            width: 80%;
            margin: 20px auto;
        }

        canvas {
            background: #111; /* Dark gray background for charts */
            border: 1px solid #333; /* Subtle border for the chart area */
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(255, 255, 255, 0.2); /* Subtle glow */
        }

        h2 {
            color: #aaa; /* Light gray for subheadings */
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Expense Visualization</h1>

    <div class="chart-container">
        <!-- Yearly Chart -->
        <h2>Yearly Expenses</h2>
        <canvas id="yearlyChart"></canvas>
    </div>

    <div class="chart-container">
        <!-- Monthly Chart -->
        <h2>Monthly Expenses</h2>
        <canvas id="monthlyChart"></canvas>
    </div>

    <div class="chart-container">
        <!-- Pie Chart -->
        <h2>Expense Distribution by Category</h2>
        <canvas id="pieChart"></canvas>
    </div>

    <script>
        // Helper to create gradients for charts
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const yearlyLabels = <?php echo json_encode(array_column($yearlyData, 'year')); ?>;
        const yearlyAmounts = <?php echo json_encode(array_column($yearlyData, 'total_amount')); ?>;

        const monthlyLabelsRaw = <?php echo json_encode(array_column($monthlyData, 'month')); ?>;
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        const monthlyLabels =<?php echo json_encode(array_column($monthlyData, 'month')); ?>; // Handle unexpected values
        const monthlyAmounts = <?php echo json_encode(array_column($monthlyData, 'total_amount')); ?>;

        const categoryLabels = <?php echo json_encode(array_column($categoryData, 'expense_name')); ?>;
        const categoryAmounts = <?php echo json_encode(array_column($categoryData, 'total_amount')); ?>;
        const totalCategoryAmount = categoryAmounts.reduce((sum, value) => sum + parseFloat(value), 0);

        // Yearly Chart
        const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
        const yearlyGradient = createGradient(yearlyCtx, 'rgba(0, 255, 127, 1)', 'rgba(0, 255, 127, 0.2)');
        new Chart(yearlyCtx, {
            type: 'bar',
            data: {
                labels: yearlyLabels,
                datasets: [{
                    label: 'Yearly Expenses',
                    data: yearlyAmounts,
                    backgroundColor: yearlyGradient,
                    borderColor: 'rgba(0, 255, 127, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => `₹ ${context.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    },
                    y: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    }
                }
            }
        });

        // Monthly Chart with Enhanced Visibility
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyGradient = createGradient(monthlyCtx, 'rgba(0, 123, 255, 0.8)', 'rgba(0, 123, 255, 0.2)');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels, // Now shows proper month names
                datasets: [{
                    label: 'Monthly Expenses',
                    data: monthlyAmounts,
                    backgroundColor: monthlyGradient,
                    borderColor: 'rgba(0, 123, 255, 1)', // Bright blue line for visibility
                    borderWidth: 3, // Thickened line
                    pointBackgroundColor: 'rgba(255, 255, 255, 1)', // White points
                    pointBorderColor: 'rgba(0, 123, 255, 1)', // Blue border for contrast
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => `₹ ${context.raw.toLocaleString()}`
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    },
                    y: {
                        ticks: { color: '#aaa' },
                        grid: { color: '#333' }
                    }
                }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryAmounts,
                    backgroundColor: ['#FF4136', '#2ECC40', '#FFDC00', '#0074D9', '#85144B', '#39CCCC'],
                    borderColor: '#000',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                const value = context.raw;
                                const percentage = ((value / totalCategoryAmount) * 100).toFixed(1);
                                return `${context.label}: ₹${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
