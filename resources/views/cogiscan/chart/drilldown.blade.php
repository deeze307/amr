<?php
    $prodchart = 'drillchart'.rand(0,99999);
?>


<div style="border:2px solid #efefef;margin-bottom:2px;">
   {{-- <button id="{{ $prodchart }}drillUp" tooltip="Subir nivel" class="btn btn-xs btn-default"><span class="fa fa-mail-reply"></span></button>--}}
    <div id="{{ $prodchart }}container" style="width: 95%;height:{{ isset($height) ? $height : '300' }}px;"></div>
</div>
<script>
    $(function () {
        Highcharts.setOptions({
            lang: {
                drillUpText: 'Volver',
                weekdays: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                shortMonths: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic']
            },
            global: {
                useUTC: false
            }
        });

        var chart =  new Highcharts.Chart({
            chart: {
                renderTo: '{{ $prodchart }}container',
                type: 'column'
            },
            title: {
                text: '{{ $user }}'
            },
            subtitle: {
                text: '{{ isset($info['totalCargado']) ? "Total: ".$info['totalCargado'] : 'desconocido' }}'
            },
            xAxis: {
                type: "datetime"
                /* dateTimeLabelFormats: {
                 day: '%H'
                 },*/
                //tickInterval: moment.duration(1, 'hour').asMilliseconds()
            },
            yAxis: {
                title: {
                    text: 'Cargas'
                }
            },
            legend: {
                enabled: false
            },
            credits: {
                enabled: false
            },

            tooltip: {
                useHTML: true,
                formatter: function () {
                    var fecha = '<b>' + Highcharts.dateFormat('%e/%m', this.x) +'</b> ';
                    var lista = [];

                    var partnumbers = this.point.partnumbers;
                    var lpn = this.point.lpn;

                    if(partnumbers!=undefined)
                    {
                        lista.push("<b>PARTNUMBERS</b>");
                        $.each( partnumbers, function( i, val ) {
                            lista.push(val);
                        });

                        lista = lista.join("<br>");
                    }

                    if(lpn!=undefined)
                    {
                        lista.push("<b>LPN</b>");
                        $.each( lpn, function( i, val ) {
                            lista.push(val.lpn +' - '+val.partnumber);
                        });

                        lista = lista.join("<br>");
                    }


                    var div = '<div > ' + fecha + ' <br> '+lista+'</div>';
                    return div;
                }
            },

            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        style: {
                            "fontSize": "20px",
                            "fontWeight": "bold",
                            "color": "contrast",
                            "textOutline": "1px 1px contrast"
                        },
                        formatter: function() {
                            if (this.y != 0) {
                                return this.y;
                            } else {
                                return null;
                            }
                        }
                    }
                }
            },

            series: [
                {
                    name: 'Cargas',
                    data: [
                        @foreach($info['porFecha'] as $fecha => $total)
                        {
                            x:  moment("{{ $fecha }}", "YYYY-MM-DD").valueOf(),
                            y: {{ $total }},
                            drilldown: 'drill{{ $fecha }}',
                            partnumbers: ['PART1','PART2','PART3']
                        },
                        @endforeach
                    ]
                }
            ],

            drilldown: {
                series: [
                    <?php
                        $lastDrill = null;
                    ?>
                    @foreach($info['detalle'] as $fechaConHora => $data)

                        <?php
                            list($fecha,$hora) = explode(" ",$fechaConHora);
                        ?>
                        @if($lastDrill != $fecha)

                            @if($lastDrill != null)
                                ]}, // FIN FECHA
                            @endif
                            // ************  NUEVA FECHA ***********
                            {
                                name: 'Cargas',
                                id: 'drill{{ $fecha }}',
                            data: [
                            <?php $lastDrill = $fecha; ?>
                        @endif
                            {
                                x:  moment("{{ $fechaConHora }}", "YYYY-MM-DD HH").valueOf(),
                                y: {{ count($data) }},
                                lpn: [
                                    @foreach($data as $detalleCargaIndex => $detalleCarga)
                                    {
                                        lpn: "{{ $detalleCarga->ITEM_ID }}",
                                        partnumber: "{{ $detalleCarga->PART_NUMBER }}"
                                    },
                                    @endforeach
                                ]
                            },
                    @endforeach

                            ]} // FIN FECHA
                ]
            }
        });

        $('#{{ $prodchart }}drillUp').click(function() {
            if (chart.drilldownLevels.length > 0) {
                chart.drillUp();
            }
        });
    });


</script>