var ctx = document.getElementById('{{ $type }}').getContext('2d');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($chart['x-axis']),
        datasets: [{
            label: '{{ $chart['title'] }}',
            backgroundColor: 'rgb(255, 99, 132)',
            borderColor: 'rgb(255, 99, 132)',
            data: @json($chart['y-axis']),
        }]
    },
    options: {}
});
