<?php
require_once("../db.php");
//預設模式為使用者意見發表統計
$mode = $_GET["mode"];


//使用者

//使用者共同資料
$users = sels("users");
foreach ($users as $user) {
    $datas[$user["id"]] = count(sels("opinion", ["user_id" => $user["id"]]));
}
arsort($datas); //保留key排序大到小
$thrdatas = array_slice($datas, 0, 3, true); //取前三高發表意見



if ($mode == "user_each_score") {
    // 1~5分的切換使用者
    if (isset($_POST["change_user"])) {
        $user_id = $_POST["select_user"];
        $mode = "user_each_score";
    } else {
        // 預設user_id
        reset($thrdatas); //先抓到第一個元素
        $user_id = key($thrdatas); //在抓那個元素的鍵名
    }
}



if ($mode == "face") {
    // 切換專案
    if (isset($_POST["choose_submit"])) {
        $project_id = $_POST["choose_project"];
        $mode = "face";
    } else {
        // 預設project_id
        $project = sels("project");
        if(!empty($project)){
            $project_id = $project[0]["id"];
        }
    }
}
?>
<!-- 統計圖表 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form action="" method="post">
        <div class="container">
            <h2>圖形統計</h2>
            <!-- <pre>
            各專案的面向意見總計 例: 專案x 面向1有x個意見，面向2有x個意見 (圓餅圖)
            </pre> -->
            <br>
            <!-- 選擇模式 -->
            <a class="btn btn-primary pull-right" href="chart.php?mode=user_total">使用者統計</a>
            <a class="btn btn-success pull-right" href="chart.php?mode=project">專案統計</a>
            <hr>


            <!-- 內容 -->
            <?php if ($mode == "user_total" || $mode == "user_each_score") { ?>
                <h3>使用者統計
                    <a class="btn btn-warning" href="chart.php?mode=user_total">使用者意見發表統計</a>
                    <a class="btn btn-info" href="chart.php?mode=user_each_score">使用者評1~5分個別次數</a>
                </h3>



                <!-- 使用者評1~5分個別次數 -->
                <?php if ($mode == "user_each_score") { ?>
                    <h4>使用者評1~5分個別次數
                        <select name="select_user" id="">

                            <?php foreach (array_keys($thrdatas) as $id) {
                                $user = sel("users", ["id" => $id]); ?>

                                <option value="<?= $user["id"] ?>"><?= $user["name"] ?></option>

                            <?php } ?>

                        </select>

                        <button class="btn btn-inverse" name="change_user" type="submit">送出</button>
                    </h4>


                    <!-- 使用者意見發表統計 -->
                <?php }
                if ($mode == "user_total") {
                    $name_data = join(",", array_keys($thrdatas));
                    $opinion_count = join(",", array_values($thrdatas));
                ?>
                    <h4>使用者意見發表統計</h4>
            <?php }
            } ?>


            <?php if ($mode == "project" || $mode == "face") { ?>
                <h3>專案統計
                    <a class="btn" href="chart.php?mode=project">各專案意見發表總數量統計</a>
                    <a class="btn btn-inverse" href="chart.php?mode=face">各專案意見之各面向統計</a>
                </h3>

                <?php if ($mode == "project") { ?>
                    <h4>各專案意見發表總數量統計</h4>

                <?php }
                if ($mode == "face") { ?>
                    <h4>各專案的面向意見總計</h4>
                    <select name="choose_project">
                        <?php $projects = sels("project");
                        if (!empty($projects)) {
                            foreach ($projects as $project) {
                        ?>
                                <option value="<?= $project["id"] ?>"><?= $project["name"] ?></option>
                        <?php }
                        } ?>
                    </select>
                    <button class="btn btn-info" name="choose_submit" type="submit">送出</button>

            <?php }
            } ?>
            <div id="content<?= $mode ?>"></div>

            <input type="hidden" value="<?= $user_id ?>" id="user_id">
            <input type="hidden" value="<?= $name_data ?>" id="name_data">
            <input type="hidden" value="<?= $opinion_count ?>" id="opinion_count">
            <input type="hidden" value="<?= $project_id ?>" id="project_id">
            <script>
                var href = new URL(location.href);
                var mode = href.searchParams.get("mode");

                var chart_data = { //初始統計圖數據
                    chart: {
                        type: ""
                    },
                    title: {
                        text: ""
                    },
                    xAxis: {
                        categories: null
                    },
                    series: [],
                    plotOptions: {
                        pie: {
                            allowPointerSelect: true,
                            cursor: "pointer",
                            dataLabels: {
                                enables: true,
                                format: "<b>{point.name}</b>{point.percentage:.1f}%"
                            }
                        }
                    },
                };



                //使用者意見發表
                if (mode == "user_total") {
                    (async function() {
                        chart_data.chart.type = "bar";
                        chart_data.title.text = "使用者意見發表";

                        let name_data = $("#name_data").val();
                        let opinion_count = $("#opinion_count").val();

                        await fetch("chart_data.php?mode=" + mode + "&name_data=" + name_data)
                            .then(res => res.json())
                            .then(res => {
                                chart_data.xAxis.categories = res
                            });

                        await fetch("chart_data.php?mode=" + mode + "&opinion_count=" + opinion_count)
                            .then(res => res.json())
                            .then(res => {
                                chart_data.series = [{
                                    data: res
                                }]
                            });

                        Highcharts.chart("content" + mode, chart_data);
                    })();
                }


                //使用者評各分個別次數
                if (mode == "user_each_score") {
                    (async function() {
                        chart_data.chart.type = "pie";
                        chart_data.title.text = "使用者評各分個別次數";

                        var user_id = $("#user_id").val();
                        await fetch("chart_data.php?mode=" + mode + "&user_id=" + user_id)
                            .then(res => res.json())
                            .then(res => {
                                chart_data.series = [{
                                    data: res
                                }]
                            })

                        Highcharts.chart("content" + mode, chart_data);
                    })();
                }


                // 各專案意見發表總數量統計
                if (mode == "project") {
                    (async function() {
                        chart_data.chart.type = "bar";
                        chart_data.title.text = "各專案意見發表總數量統計";

                        await fetch("chart_data.php?mode=project&name")
                            .then(res => res.json())
                            .then(res => {
                                chart_data.xAxis.categories = res
                            });
                        await fetch("chart_data.php?mode=project&count")
                            .then(res => res.json())
                            .then(res => {
                                chart_data.series = [{
                                    data: res
                                }]
                            });

                        Highcharts.chart("content" + mode, chart_data);
                    })();
                }


                //各專案的面向意見總計
                if (mode == "face") {
                    (async function() {
                        chart_data.chart.type = "pie";
                        chart_data.title.text = "各專案的面向意見總計";

                        var project_id = $("#project_id").val();
                        await fetch("chart_data.php?mode=face&project_id=" + project_id)
                            .then(res => res.json())
                            .then(res => {
                                chart_data.series = [{
                                    data: res
                                }]
                                console.log(res);
                            })
                        Highcharts.chart("content" + mode, chart_data);
                    })();
                }
            </script>
        </div>
    </form>
</body>

</html>