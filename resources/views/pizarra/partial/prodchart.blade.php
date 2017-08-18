<style>
    .chart-container {
        position: relative;
        /*margin: auto;*/
        height: 300px;
    }
</style>
<div class="chart-container col-lg-6">
    <canvas id="myChart"></canvas>
</div>
<div class="col-lg-6">
    <div class="box box-info">
        <div class="box-header">
            <h4>Información del Producto Actual</h4>
        </div>
        <div class="box-body">
            <div class="col-lg-6">
                <blockquote>
                    <small>OP:</small>
                    OP-131776
                    <small>Modelo:</small>
                    43LJ5500 -
                    MAI
                    <small>Lote:</small>
                    L102
                </blockquote>
            </div>
            <div class="col-lg-6">
                <div class="small-box bg-aqua-active">
                    <div class="inner">
                        <h3 class="text-center">50 de 100</h3>

                        <div class="progress" style="margin-bottom: 4px; ">

                            <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%; min-width: 4em;">
                                50%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["6 a 7","7 a 8","8 a 9","9 a 10","10 a 11","11 a 12","12 a 13","13 a 14","14 a 15"],
            datasets: [{
                label:'Producción Hora x Hora Turno Mañana',
                data: [17, 113, 97, 113, 108, 107],
                backgroundColor: [
                    'rgba(193,46,12,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(255,221,50,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(60,186,159,0.2)',
                    'rgba(60,186,159,0.2)'
                ],
                borderColor: [
                    'rgba(193,46,12,1)',
                    'rgba(60,186,159,1)',
                    'rgba(255,221,50,1)',
                    'rgba(60,186,159,1)',
                    'rgba(60,186,159,1)',
                    'rgba(60,186,159,1)',
                    'rgba(60,186,159,1)',
                    'rgba(60,186,159,1)',
                    'rgba(60,186,159,1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            maintainAspectRatio:false,
            legend:{
                display:false,
                text:["17", "113", "97", "113", "108", "107"]
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    },
                    gridLines:{
                        display:false
                    }
                }],
                xAxes:[{
                    gridLines:{
                        display:false
                    }
                }]
            },
            events: false,
            tooltips: {
                enabled: false
            },
            hover: {
                animationDuration: 0
            },
            animation: {
                duration: 1,
                onComplete: function () {
                    var chartInstance = this.chart,
                            ctx = chartInstance.ctx;
                    ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    this.data.datasets.forEach(function (dataset, i) {
                        var meta = chartInstance.controller.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = dataset.data[index];
                            ctx.fillText(data, bar._model.x, bar._model.y - 5);
                        });
                    });
                }
            }
        }
    });
</script>

