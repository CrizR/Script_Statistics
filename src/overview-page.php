<!DOCTYPE HTML>
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
    <?php
    require('connect.php');
    $total_calls = $connect->getTotalCalls($input[0], $input[1], $input[2]);
    $calls_per_day = $connect->getCallsPerDay($input[0], $input[1], $input[2]);
    $script_created = $connect->getScriptLife($input[0])[0];
    $last_activity = $connect->getScriptLife($input[0])[1];
    $avg_call_length = $connect->getAvgCallLength($input[0], $input[1], $input[2]);
    $top_calls = $connect->getTopUsers($input[0], $input[1], $input[2]);
    $relative_call_time = $connect->relativeCallLength($input[0], $input[1], $input[2]);
    $relative_usage = $connect->relativeScriptUsage($input[0], $input[1], $input[2]);
    $calls_today = $connect->callsToday($input[0]);
    ?>
    <script>
        function submitForm(sel) {
            if (sel.val() !== 'all') this.form.submit();
        }
    </script>
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
        <li class="active_stat"><a href="overview-page.php">Overview</a></li>
        <li><a href="node-map.php">Node Map</a></li>
        <li><a href="usage-graph.php">Usage Graph</a></li>
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
            <form action="" method="post" onchange='submitForm(this);'>
                <select name="script_choice" onchange="location.reload()" title="scripts">
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
<section id="overview-stats">
    <ul class="overview-stats">
        <li>
            <h1 class="overview-title">Account</h1>
            <h3 class="overview-stat-info">Firebase URL:
                <?php
                if (!empty($url)) {
                    echo $url;
                } ?></h3>
            <h3 class="overview-stat-info">Database Secret:
                <?php if (!empty($token)) {
                    echo $token;
                } ?></h3>
        </li>
        <li>
            <h1 class="overview-title">Usage/Info</h1>
            <div class = "info-button-sc"></div>
            <h3 class="overview-stat-info">Script Created:
                &nbsp;<?php
                if (isset($script_created)) {
                    echo $script_created;
                }
                ?> </h3>
            <div class = "info-button-la"></div>
            <h3 class="overview-stat-info">Last Activity: &nbsp;
                <?php
                if (isset($last_activity)) {
                    echo $last_activity;
                }
                ?> </h3>
            <div class = "info-button-tc"></div>
            <h3 class="overview-stat-info">Total Calls: &nbsp;
                <?php
                if (isset($total_calls)) {
                    echo $total_calls . " calls";
                }
                ?> </h3>
            <h3 class="overview-stat-info">Calls Today: &nbsp;
                <?php
                if (isset($calls_today)) {
                    echo $calls_today;
                }
                ?></h3>
            <div class = "info-button-acd"></div>
            <h3 class="overview-stat-info">Average Calls Per Day: &nbsp;
                <?php
                if (isset($calls_per_day)) {
                    echo  number_format((float)$calls_per_day, 2, '.', '');
                }
                ?> </h3>
            <div class = "info-button-acl"></div>
            <h3 class="overview-stat-info">Average Call Length: &nbsp;
                <?php
                if (isset($avg_call_length)) {
                    echo  number_format((float)$avg_call_length, 2, '.', '') . " s";
                }
                ?></h3>
            <div class = "info-button-ru"></div>
            <h3 class="overview-stat-info">Today's Relative Call Time: &nbsp;
                <?php
                if (isset($relative_call_time)) {
                    echo $relative_call_time . "%";
                }
                ?></h3>
            <h3 class="overview-stat-info">Today's Relative Usage: &nbsp;
                <?php
                if (isset($relative_usage)) {
                    echo $relative_usage . "%";
                }
                ?></h3>
        </li>

        <li>
            <h1 class="overview-title">Top Callers</h1>
                <?php
                $rank = 1;
                $i = 0;
                foreach (array_keys($top_calls) as $user){
                    $count = $top_calls[$user];
                    if ($user == 0){
                        $user = "Hidden Number";
                    }
                    echo "<h3 class='overview-stat-info'> Rank $rank:  &nbsp;&nbsp; $user &nbsp;&nbsp; Count : $count</h3>";
                    $rank++;
                    $i++;
                }
                ?>

        </li>
    </ul>
</section>
</body>
<footer>
    <ul></ul>
</footer>

</html>
