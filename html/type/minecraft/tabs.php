<?php
include './html/langconf.php';
?>
<!DOCTYPE html>
<table class="hidemctab">
    <tr>
        <td class="padding5055">
            <div style="text-align: left;">
                <div class="hbanner">
                    <div class="hchartdiv">
                        <p style="text-align: left;border-bottom: solid;border-bottom: solid; border-color: rgb(70,70,70); margin: 0 0 8px 0; padding: 3px 0;letter-spacing: normal;"><?php echo $motd ?></p>
                        <div class="flex">
                            <div class="items" style="min-width: 80px;">
                                <div class="flex">
                                    <div class="flex">
                                        <svg width="100%" height="100%" viewBox="0 0 123 100" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;">
                                            <g>
                                                <path d="M73.12,0C79.85,0 86.28,1.34 92.15,3.77C98.24,6.29 103.72,9.99 108.31,14.58C112.89,19.16 116.59,24.64 119.11,30.75C121.54,36.62 122.88,43.05 122.88,49.77C122.88,56.5 121.54,62.93 119.11,68.8C116.59,74.89 112.89,80.37 108.3,84.96C103.72,89.54 98.24,93.24 92.13,95.76C86.26,98.19 79.83,99.53 73.11,99.53C66.38,99.53 59.95,98.19 54.08,95.76C47.99,93.24 42.51,89.54 37.93,84.96L37.92,84.95C33.33,80.36 29.64,74.88 27.12,68.8C26.34,66.91 25.67,64.97 25.12,62.98C26.16,63.08 27.22,63.13 28.29,63.13C30.32,63.13 32.3,62.95 34.23,62.6C34.55,63.56 34.9,64.51 35.28,65.44C37.35,70.44 40.39,74.94 44.17,78.72C47.95,82.5 52.46,85.54 57.45,87.61C62.26,89.6 67.55,90.71 73.11,90.71C78.67,90.71 83.95,89.61 88.77,87.61C93.77,85.54 98.27,82.5 102.05,78.72C105.83,74.94 108.87,70.43 110.94,65.44C112.93,60.63 114.04,55.34 114.04,49.78C114.04,44.22 112.94,38.94 110.94,34.12C108.87,29.12 105.83,24.62 102.05,20.84C98.27,17.06 93.76,14.02 88.77,11.95C83.96,9.96 78.67,8.85 73.11,8.85C67.55,8.85 62.27,9.95 57.45,11.95C57.02,12.13 56.59,12.32 56.17,12.51C54.53,9.93 52.55,7.59 50.28,5.56C51.52,4.92 52.79,4.33 54.08,3.79C59.97,1.34 66.39,0 73.12,0ZM67.41,26.11C67.41,24.89 67.91,23.79 68.7,22.99C69.5,22.19 70.6,21.7 71.82,21.7C73.04,21.7 74.14,22.19 74.94,22.99C75.74,23.79 76.23,24.89 76.23,26.11L76.23,49.33L93.58,59.62C94.62,60.24 95.32,61.23 95.6,62.32C95.88,63.41 95.75,64.61 95.13,65.65L95.13,65.66C94.51,66.7 93.52,67.4 92.43,67.68C91.34,67.96 90.14,67.83 89.1,67.21L89.09,67.21L69.68,55.7C69.01,55.33 68.46,54.79 68.06,54.15C67.65,53.48 67.41,52.69 67.41,51.85L67.41,26.11Z" style="fill:white;fill-rule:nonzero;"/>
                                                <path d="M26.98,2.64C41.88,2.64 53.96,14.72 53.96,29.62C53.96,44.52 41.88,56.6 26.98,56.6C12.08,56.6 0,44.52 0,29.62C0,14.72 12.08,2.64 26.98,2.64ZM26.98,13.72L41.46,31.62L32.47,31.62L32.47,41.14L21.49,41.14L21.49,31.62L12.5,31.62L26.98,13.72Z" style="fill:rgb(107,190,102);"/>
                                            </g>
                                        </svg>
                                        <p><?php echo $uptimebanner ?></p>
                                    </div>
                                    <div class="flex">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
                                            <g>
                                                <path d="M24 24c4.42 0 8-3.59 8-8 0-4.42-3.58-8-8-8s-8 3.58-8 8c0 4.41 3.58 8 8 8zm0 4c-5.33 0-16 2.67-16 8v4h32v-4c0-5.33-10.67-8-16-8z" style="fill:white;"/>
                                                <path d="M0 0h48v48h-48z" fill="none"/>
                                            </g>
                                        </svg>
                                        <p><?php echo $countplayers . '/' . $maxplayers; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="version" >Version: <?php echo $version ?></p>
                        <details>
                            <summary>Mods</summary>
                            <div class="mod">
                                <?php
                                if (!empty($serverstatus->modinfo->modList)) {
                                    foreach ($serverstatus->modinfo->modList as $mods) {
                                        echo $mods->modid.", ";
                                    }
                                } else {
                                    echo $language[$lang][10];
                                }
                                ?>
                            </div>
                        </details>
                        <div class="canvasparent">
                            <canvas id="Chart<?php echo $ServerID ?>" class="hchart" width="720" height="140"></canvas>
                        </div>
                    </div>
                    <script>
                        Chart.defaults.color = 'white';
                        let xLabels<?php echo $ServerID ?> = ['4h', '', '', '', '', '', '3h', '', '', '', '', '', '2h', '', '', '', '', '', '1h', '', '', '', '', '', 'now'];
                        let xValues<?php echo $ServerID ?> = <?php echo $seperatevar.";" ?>
                        new Chart("Chart<?php echo $ServerID ?>", {
                            type: "line",
                            data: {
                                labels: xLabels<?php echo $ServerID ?>,
                                datasets: [{
                                    label: "Players",
                                    data: xValues<?php echo $ServerID ?>,
                                    backgroundColor: "white",
                                    borderColor: "red",
                                    color: "white",
                                    borderWidth: 2,
                                    pointBorderWidth: 1.5,
                                    pointRadius: 2,
                                    fill: false,
                                    tension: 0.6,
                                    pointBorderColor: "white",
                                }]
                            },
                            options: {
                                scales: {
                                    x: {grid: {display: false}, ticks: {display: true}},
                                    y: {grid: {display: true, color: 'rgb(70,70,70)',},}
                                }, responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false,}}
                            }
                        });
                    </script>
                </div>
            </div>
        </td>
        <td class="width35 mcquery">
            <?php
            if ($qport == 0):
                echo $language[$lang][13];
            else:
                ?>
                <div>Query is enabled</div>
            <?php
            endif;
            ?>
        </td>
    </tr>
</table>
