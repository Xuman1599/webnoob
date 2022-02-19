<?php
require_once("../db.php");

//專案
$project = sel("project", ["id" => $_GET["id"]]);


//刪除執行方案
if (isset($_POST["delete"])) {
    del("plan", ["id" => $_POST["delete"]]);
    header("location:plan.php?id={$_GET['id']}");
}


// 開始、結束評分
if (isset($_POST["start_score"])) {
    upd("project", ["plan_mark" => 1], ["id" => $_GET["id"]]);
    header("location:plan.php?id={$_GET['id']}");
}
if (isset($_POST["end_score"])) {
    upd("project", ["plan_mark" => 0], ["id" => $_GET["id"]]);
    header("location:plan.php?id={$_GET['id']}");
}


// 評分執行方案
if (isset($_POST["submit_score"])) {
    if (empty(sel("index_score", ["index_id" => $_POST["submit_score"], "plan_id" => $_POST["plan_id"], "user_id" => $_SESSION["user"]["id"]]))) {
        ins("index_score", ["score" => $_POST["select_score" . $_POST["submit_score"]], "index_id" => $_POST["submit_score"], "plan_id" => $_POST["plan_id"], "user_id" => $_SESSION["user"]["id"]]);
        $plan = sel("plan", ["id" => $_POST["plan_id"]]);
        upd("plan", ["score" => $plan["score"] + $_POST["select_score" . $_POST["submit_score"]]], ["id" => $_POST["plan_id"]]);
    } else {
        echo "<script>alert('此指標已被評分過');location.href='';</script>";
    }
}





//自動製作執行方案
if (isset($_POST["auto"])) {
    //先確定所有使用者有評所有專案的意見
    $users = sels("member", ["project_id" => $_GET["id"]]);
    $total_opinion = sels("opinion", ["project_id" => $_GET["id"]], "ORDER BY `score` DESC");
    if (!empty($total_opinion)) {
        $count_opinion = count($total_opinion);
        foreach ($users as $user) {
            if (count(sels("opinion_score", ["project_id" => $_GET["id"], "user_id" => $user["user_id"]])) != $count_opinion) {
                echo "<script>alert('有使用者尚未評完所有專案意見');location.href=''</script>";
            }
        }


        //開始查詢前三高意見並製作執行方案
        foreach ($total_opinion as $key => $opinion) {
            if ($total_opinion > 2) {
                if ($key < 2) {
                    $datas_id[] = $opinion["id"];
                    $datas_name[] = $opinion["name"];
                }
            } else {
                $datas_id[] = $opinion["id"];
                $datas_name[] = $opinion["name"];
            }
        }


        shuffle($datas_id);
        shuffle($datas_name);
        $datas_id = join(",", $datas_id);
        $auto = query("SELECT max(auto_do) FROM `plan` WHERE `project_id`={$_GET['id']}");
        $auto_id = intval($auto["max(auto_do)"]);//因為query裡面value=key
        if (empty($auto_id)) {
            $auto_id = 1;
        }
        ins("plan", ["opinion_id" => $datas_id, "name" => "A" . str_pad($auto_id, 3, "0", STR_PAD_LEFT), "auto_do" => $auto_id + 1, "des" => join(" ", $datas_name), "project_id" => $_GET["id"]]);
    }
    header("location:plan.php?id={$_GET['id']}");
}
?>
<!-- 執行方案頁面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <div class="container">
        <h2>專案內容</h2>

        <!-- 一般人的返回連結 -->
        <?php if ($_SESSION["user"]["level"] != 1) { ?>
            <a class="pull-right btn" href="../default/project_main.php">返回</a>
        <?php } ?>

        <a class="btn btn-inverse pull-right" href="face.php?id=<?= $_GET['id'] ?>">面向頁面</a>
        <hr>
        <h3>執行方案清單</h3>


        <!-- 組長或管理員功能: 建立執行方案，建立評分指標，自動製作執行方案，開始結束評分 -->
        <form action="" method="post">
            <?php
            $member = sel("member", ["project_id" => $_GET["id"], "user_id" => $_SESSION["user"]["id"]]);
            $user = sel("users", ["id" => $_SESSION["user"]["id"]]);
            if ($user['level'] == 1 || $member["leader"] == 1) { ?>
                <a class="btn" href="plan_ins.php?id=<?= $_GET["id"] ?>">建立執行方案</a>
                <a class="btn btn-warning" href="index_ins.php?id=<?= $_GET["id"] ?>">建立評分指標</a>
                <button class="btn btn-primary" name="auto" type="submit">自動製作執行方案</button>

                <?php if ($project["plan_mark"] == 1) { ?>
                    <button name="end_score" class="btn btn-danger" type="submit">停止評分</button>

                <?php } else { ?>
                    <button name="start_score" class="btn btn-success" type="submit">開始評分</button>

            <?php }
            } ?>
        </form>
        <br><br>


        <?php
        $plans = sels("plan", ["project_id" => $_GET["id"]]);
        if (!empty($plans)) { ?>
            <div class="accordion">

                <!-- 執行方案標題 -->
                <?php foreach ($plans as $key => $plan) { ?>
                    <div class="accordion-group">

                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" href="#collapse<?= $key ?>">
                                <h4>
                                    執行方案編號<?= $key + 1 ?>&nbsp;<?= $plan["name"] ?>
                                    <span class="pull-right">
                                        <?php
                                        if (empty(sel("index_score", ["plan_id" => $plan["id"], "user_id" => $_SESSION["user"]["id"]]))) {
                                            echo "尚未評分此執行方案!";
                                        }
                                        ?>
                                    </span>
                                </h4>
                            </a>
                        </div>

                        <!-- 執行方案說明 -->
                        <div id="collapse<?= $key ?>" class="accordion-body collapse">
                            <div class="accordion-inner">

                                <form action="" method="post">
                                    <h4>執行方案說明:&nbsp;<?= $plan["des"] ?></h4>
                                    <?php
                                    $opinions = explode(",", $plan["opinion_id"]);
                                    if (!empty($opinions)) {
                                        foreach ($opinions as $opinion_key => $opinion_id) {
                                            $opinion = sel("opinion", ["id" => $opinion_id]);


                                            //平均分數，總共分數，總共人數
                                            $count = count(sels("opinion_score", ["opinion_id" => $opinion["id"]]));
                                            if (!empty($count)) {
                                                $total = $opinion["score"] / $count;
                                            } else {
                                                $count = 0;
                                                $total = 0;
                                            }
                                    ?>

                                            <!-- 方案意見標題 -->
                                            <div class="accordion-group">
                                                <div class="accordion-heading">
                                                    <a class="accordion-toggle" data-toggle="collapse" href="#message<?= $key . $opinion_key ?>">
                                                        <p>方案意見<?= $opinion_key + 1 ?>&nbsp;意見標題&nbsp;<?= $opinion["name"] ?>
                                                            <span class="pull-right">
                                                                被評價總分<?= $opinion["score"] ?>&nbsp;
                                                                評價人數<?= $count ?>&nbsp;
                                                                平均評價<?= $total ?>
                                                            </span>
                                                        </p>
                                                    </a>
                                                </div>

                                                <!-- 方案意見說明 -->
                                                <div id="message<?= $key . $opinion_key ?>" class="accordion-body collapse">
                                                    <div class="accordion-inner">
                                                        <h4>方案意見說明:<?= $opinion["des"] ?>
                                                            <div class="pull-right"><?= $opinion["time"] ?></div>
                                                        </h4>
                                                    </div>
                                                </div>
                                            </div>

                                    <?php }
                                    } ?>
                                    <br><br>

                                    <!-- 組長or管理員功能 -->
                                    <?php if ($_SESSION["user"]["level"] == 1 || $member["leader"] == 1) { ?>

                                        <div class="pull-right">
                                            <a class="btn btn-info" href="plan_edit.php?project_id=<?= $_GET['id'] ?>&id=<?= $plan["id"] ?>">修改</a>
                                            <button value="<?= $plan["id"] ?>" onclick="return confirm('確定要刪除此執行方案?')" class="btn btn-danger" name="delete" type="submit">刪除</button>
                                        </div>
                                    <?php } ?>
                                </form>

                                <!-- 評分指標 -->
                                <?php if ($project["plan_mark"] == 1) { ?>
                                    <a class="btn pull-left accordion-toggle" data-toggle="collapse" href="#score<?= $key ?>">評分</a>
                                    <br><br>

                                    <div id="score<?= $key ?>" class="accordion-body collapse">

                                        <br>
                                        <h5>指標名稱</h5>
                                        <?php
                                        $indexs = sels("index", ["project_id" => $_GET["id"]]);
                                        if (!empty($indexs)) {
                                            foreach ($indexs as $index) {
                                        ?>

                                                <form action="" method="post">

                                                    <h5>
                                                        <?= $index["name"] ?>&nbsp;
                                                        <select name="select_score<?= $index["id"] ?>">
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3" selected>3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                        <!-- <input type="text" value="< ?= $plan['id'] ?>" name="plan_id"> -->
                                                        <input type="hidden" value="<?= $plan["id"] ?>" name="plan_id">
                                                        <button class="btn btn-success" name="submit_score" value="<?= $index['id'] ?>" type="submit">評分</button>
                                                    </h5>
                                                </form>

                                        <?php }
                                        } ?>
                                        <br>
                                    </div>

                                <?php } ?>

                            </div>
                        </div>

                    </div>

                <?php } ?>
            </div>
        <?php } ?>

    </div>

</body>

</html>