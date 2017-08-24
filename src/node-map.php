<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Studio Data Statistics</title>
    <!-- <link rel="icon" type="image/png" href=""> -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link href="css/style.css" rel="stylesheet">
    <?php require("connect.php"); ?>
    <script>
        function submitForm(sel) {
            if (sel.val() !== 'all') this.form.submit();
        }
    </script>
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
</head>

<body>

<div class="left-bar">
    <ul class="side-nav">
        <li><a href="overview-page.php">Overview</a></li>
        <li class="active_stat"><a href="node-map.php">Node Map</a></li>
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
            <form action="php/src/input.php" name="months" method="post" onchange='submitForm(this);'>
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
            <form action="php/src/input.php" name="years" method="post" onchange='submitForm(this);'>
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
            <form action="php/src/input.php" method="post" onchange='submitForm(this);'>
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
<section class="stat_body">
    <div class="node-container">
        <?php
        //Script Month Year
        $nodes = $connect->getNodes($input[0]);
        //Display each node in the script
        $node_num = 0;
        $buttons = [];
        $modals = [];
        foreach ($nodes as $node) {
            $node_data = $connect->getNodeData($input[0], $node);
            //TODO: Make node specific data exclusive to node.
            $avg_confidence_level = "Not Applicable";
            $ratio = "Not Applicable";
            $utterance = "Not Applicable";
            if ($node == "menu" or $node == "form") {
                $avg_confidence_level = $node_data["acl"];
                $ratio = $node_data["ratio"];
                $utterance = $node_data["utterance"][0];

            }
            $node_usage = $node_data["data_usage"];
            $button_id = "myBtn" . $node_num;
            $modal_id = "myModal" . $node_num;
            array_push($buttons, $button_id);
            array_push($modals, $modal_id);
            echo "<button id= $button_id class = 'node' onclick='display_data($node_num);'>$node</button>
                    <div id= $modal_id class='modal'>
                        <div class=\"modal-content\">
                        <span class = 'close' style = 'text-align: center; margin-top: -200px;'>X</span>
                        <h1 style = 'margin-top: -200px; text-align: center;'>$node data</h1>
                        <p style = 'text-align: left;'>Number of Logs: $node_usage</p>
                        <p style = 'text-align: left;'>Avg Confidence Level: $avg_confidence_level</p>
                         <p style = 'text-align: left;'>Speech to DTMF Ratio: $ratio</p>
                         <p style = 'text-align: left;'>Most Common Utterance: '$utterance'</p>
                        </div>
                    </div>";
            $node_num++;
        }

        ?>
    </div>
</section>
</body>
<script>

    //    TODO: Improve Node display functionality (sometimes unable to exit out of popup)
    function display_data(node_num) {
        var buttons = <?php echo json_encode($buttons); ?>;
        var modals = <?php echo json_encode($modals); ?>;
        var modal = document.getElementById(modals[node_num]);
        var btn = document.getElementById(buttons[node_num]);
        var exit = document.getElementsByClassName('close')[0];
        btn.onclick = function () {
            modal.style.display = "block";
        }
        exit.onclick = function () {
            modal.style.display = "none";
        }
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        }
    }

</script>
<footer>
</footer>

</html>
