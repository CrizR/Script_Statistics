<!DOCTYPE HTML>
<!--TODO:Integrate Paul's functions into the project here-->
<html lang="en">
<head profile="">
    <title>Studio Data Statistics</title>
    <link rel="icon" type="image/png" href="">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <?php
    require("connect.php");
    $usage_date = $connect->getUsageTimes($input[0], $input[1], $input[2]);
    $current_times = json_encode($usage_date["current"], true);
    $avg_times = json_encode($usage_date["avg"], true);
    $peak_times = json_encode($usage_date["peak"], true);

    ?>
</head>
<section>
    <ul class="site-wrapper">
        <li>Inference</li>
        <li><span>&#124;</span></li>
        <li><a href="https://usstudio.inferencecommunications.com/portal/">Script</a></li>
        <li><a href="https://usstudio.inferencecommunications.com/portal/">Global</a></li>
        <li><a href="https://usstudio.inferencecommunications.com/portal/">Workflow</a></li>
        <li><a href="https://usstudio.inferencecommunications.com/portal/">Connect</a></li>
        <li class="active"><a href="portal.html">Statistics</a></li>
        <li><a href="https://usstudio.inferencecommunications.com/portal/">Settings</a></li>
    </ul>
</section>
<body>
<div class="left-bar">
    <ul class="side-nav">
        <li><a href="overview-page.php">Overview</a></li>
        <li><a href="node-map.php">Node Map</a></li>
        <li class="active_stat"><a href="usage-graph.php">Usage Graph</a></li>
    </ul>
</div>
<div class="right-bar">
    <ul class="side-nav">
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>
<section class="upper-bar">
    <!--TODO:Fix Form and Select Option on Reload (Selection isn't saved)-->
    <ul class="buttons">
        <li>
            <form action="php/src/input.php" name="months" method="post" onchange="location.reload()">
                <select id="months" name="msel">
                    <option value="all">Month</option>
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
            </form>
        </li>
        <li>
            <form action="php/src/input.php" name="years" method="post" onchange="location.reload()">
                <select id="years" name="ysel">
                    <option value="all">Year</option>
                    <option value="0">2017</option>
                    <option value="1">2016</option>
                    <option value="2">2015</option>
                    <option value="3">2014</option>
                    <option value="4">2013</option>
                    <option value="5">2012</option>
                </select>
            </form>
        </li>
        <li>
            <form action="php/src/input.php" method="post" onchange="location.reload()">
                <select name="script_choice" onchange="location.reload()">
                    <?php
                    $scripts = array_keys($connect->getScripts());
                    echo "<option name = 'script' value = 'all'>Script</option>";
                    foreach ($scripts as $script) {
                        echo "<option value = \"'$script'\">$script</option>";
                    }
                    ?>
                </select>
            </form>
        </li>
    </ul>
</section>
<div id="container" style="margin-top: 50px; margin-left: 16%; width: 65%; height: 50%;"></div>

</body>
<footer>
    <ul></ul>
</footer>

<script type="text/javascript">

    /**
     * Visualizes the script data usage.
     */
    var current = <?php echo $current_times ?>;
    var avg = <?php echo $avg_times ?>;
    var peak = <?php echo $peak_times ?>;

    Highcharts.chart('container', {
        chart: {
            type: 'area',
            backgroundColor: 'rgba(255, 255, 255, 0)',
            height: 800
        },
        title: {
            text: 'Script Usage Graph'
        },
        xAxis: {
            categories: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
        },
        credits: {
            enabled: false
        },
        series: [{
            name: 'Peak',
            data: [
                peak["Monday"],
                peak["Tuesday"],
                peak["Wednesday"],
                peak["Thursday"],
                peak["Friday"],
                peak["Saturday"],
                peak["Sunday"]]
        }, {
            name: 'Average',
            data: [
                avg["Monday"],
                avg["Tuesday"],
                avg["Wednesday"],
                avg["Thursday"],
                avg["Friday"],
                avg["Saturday"],
                avg["Sunday"]]
        }, {
            name: 'Current',
            data: [
                current["Monday"],
                current["Tuesday"],
                current["Wednesday"],
                current["Thursday"],
                current["Friday"],
                current["Saturday"],
                current["Sunday"]]
        }]
    });
</script>

</html>
