<?php
require_once("../db.php");
$face = sel("face", ["id" => $_GET["id"]]);
$project = sel("project", ["id" => $face["project_id"]]);




if (isset($_POST["type"])) {

    //發表意見
    if ($_POST["type"] == "insert") {
        if (isset($_POST["reply"])) {
            $reply = join(",", $_POST["reply"]);
            header("location:opinion_ins.php?id={$_GET['id']}&reply_id={$reply}");
        } else {
            header("location:opinion_ins.php?id={$_GET['id']}");
        }
    }

    //評分
    if ($_POST["type"] == "score") {
        //判斷有沒有重複評分
        $check = sel("opinion_score", ["opinion_id" => $_POST["score_submit"], "project_id" => $project["id"], "user_id" => $_SESSION["user"]["id"]]);
        if (empty($check)) {
            $ins = ins("opinion_score", ["score" => intval($_POST["score" . $_POST["score_submit"]]), "project_id" => $project["id"], "opinion_id" => $_POST["score_submit"], "user_id" => $_SESSION["user"]["id"]]);
            $opinion = sel("opinion", ["id" => $_POST["score_submit"]]);
            upd("opinion", ["score" => $opinion["score"] + intval($_POST["score" . $_POST["score_submit"]])], ["id" => $_POST["score_submit"]]);
            header("location:opinion_list.php?id={$_GET['id']}");
        } else {
            echo "<script>alert('以評過此意見')</script>";
        }
    }
}
?>
<!-- 意見列表 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form action="" method="post" name="theform">
        <!-- 用來判斷新增意見或評分 -->
        <input type='hidden' name='type'>
        <div class="container">
            <h2>意見列表</h2>
            <a class="btn pull-right" href="face.php?id=<?= $project["id"] ?>">返回</a>

            <?php if ($project["able"] == 1) { ?>
                <button name="submit" onclick="document.forms['theform'].type.value='insert';" class="btn btn-warning pull-right">發表意見</button>
            <?php } ?>

            <hr>

            <table class="table">

                <thead>
                    <th>編號</th>
                    <th>發表的時間</th>
                    <th>標題</th>
                    <th>說明</th>
                    <th>發表者的使用者名稱</th>
                    <th>評分列表</th>
                    <th>延伸此意見</th>
                    <th>檔案</th>
                    <th>原始意見</th>
                </thead>

                <tbody>
                    <?php
                    $opinions = sels("opinion", ["face_id" => $face["id"]]);
                    if (!empty($opinions)) {
                        foreach ($opinions as $key => $opinion) {
                            $user = sel("users", ["id" => $opinion["user_id"]]);

                            //意見分數:評價人數，平均分數，評價人數
                            $scores = sels("opinion_score", ["project_id" => $project["id"], "opinion_id" => $opinion["id"]]);
                            if (!empty($scores)) {
                                //評價人數
                                $count_score = count($scores);
                                //被評價平均分數
                                $average = $opinion["score"] / $count_score;
                            } else {
                                $count_score = 0;
                                $average = 0;
                            }
                    ?>

                            <tr>
                                <td><?= str_pad($key + 1, 3, "0", STR_PAD_LEFT) ?></td>
                                <td><?= $opinion["time"] ?></td>
                                <td><?= $opinion["name"] ?></td>
                                <td><?= $opinion["des"] ?></td>
                                <td><?= $user["name"] ?></td>
                                <td><a class="btn btn-info" data-toggle="modal" href="#give_score<?= $opinion["id"] ?>">評分</a></td>
                                <td><input value="<?= $opinion["id"] ?>" type="checkbox" name="reply[]"></td>
                                <td>

                                    <?php if (!empty($opinion["upload"])) { ?>
                                        <a href="upload.php?face_id=<?= $_GET['id'] ?>&id=<?= $opinion["id"] ?>">查看檔案</a>
                                    <?php } else {
                                        echo "無";
                                    } ?>

                                </td>
                                <td>

                                    <?php if (!empty($opinion["reply_id"])) { ?>
                                        <a href="opinion_original.php?face_id=<?= $_GET["id"] ?>&id=<?= $opinion['id'] ?>">查看原始意見</a>
                                    <?php } else {
                                        echo "無";
                                    } ?>

                                </td>
                            </tr>



                            <div class="modal fade lade" id="give_score<?= $opinion["id"] ?>">

                                <div class="modal-header">
                                    <h3><?= $opinion["name"] ?>的意見列表</h3>
                                </div>

                                <div class="modal-body">
                                    <h4>
                                        評價人數:<?= $count_score ?><br>
                                        被評價平均分數:<?= $average ?><br>
                                        評價總分:<?= $opinion["score"] ?>
                                    </h4>
                                </div>
                                <div class="modal-footer">

                                    <select name="score<?= $opinion["id"] ?>">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>

                                    <button name="score_submit" value="<?= $opinion["id"] ?>" onclick="document.forms['theform'].type.value='score';" class="btn btn-info" type="submit">評分</button>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </tbody>

            </table>

        </div>
    </form>

</body>

</html>