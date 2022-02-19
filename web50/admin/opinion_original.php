<?php
require_once("../db.php");
$opinion = sel("opinion", ["id" => $_GET["id"]]);

// 原始意見
$replys = explode(",", $opinion["reply_id"]);
?>

<!-- 原始意見頁面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php
    if ($_SESSION["user"]["level"] == 1) {
        require_once("../nav.php");
    } else { ?>
        <br><br>
    <?php } ?>

    <div class="container">
        <h2>原始意見</h2>
        <a class="pull-right btn" href="opinion_list.php?id=<?= $_GET["face_id"] ?>">返回</a>
        <hr>

        <table class="table">

            <thead>
                <th>編號</th>
                <th>發表的時間</th>
                <th>標題</th>
                <th>說明</th>
                <th>發表者的使用者名稱</th>
                <th>評分列表</th>
                <th>檔案</th>
                <th>原始意見</th>
            </thead>

            <tbody>

                <!-- 評價人數，平均分數，評價人數 -->
                <?php foreach ($replys as $key => $reply) {
                    $reply_opinion = sel("opinion", ["id" => $reply]);
                    $user = sel("users", ["id" => $reply_opinion["user_id"]]);
                    $scores = sels("opinion_score", ["opinion_id" => $reply_opinion["id"]]);
                    if (!empty($scores)) {
                        //評價人數
                        $count_score = count($scores);
                        //被評價平均分數
                        $average = $reply_opinion["score"] / $count_score;
                    } else {
                        $count_score = 0;
                        $average = 0;
                    }
                ?>

                    <tr>
                        <td><?= str_pad($key + 1, 3, "0", STR_PAD_LEFT) ?></td>
                        <td><?= $reply_opinion["time"] ?></td>
                        <td><?= $reply_opinion["name"] ?></td>
                        <td><?= $reply_opinion["des"] ?></td>
                        <td><?= $user["name"] ?></td>
                        <td><a class="btn btn-info" data-toggle="modal" href="#give_score<?= $reply_opinion["id"] ?>">評分</a></td>
                        <td><input value="<?= $reply_opinion["id"] ?>" type="checkbox" name="reply[]"></td>
                        <td>
                            <?php if (!empty($reply_opinion["upload"])) { ?>
                                <a href="upload.php?face_id=<?= $_GET['face_id'] ?>&id=<?= $reply_opinion["id"] ?>&reply_id=<?= $opinion["id"] ?>">查看檔案</a>
                            <?php } else {
                                echo "無";
                            } ?>
                        </td>
                        <td>
                            <?php if (!empty($reply_opinion["reply_id"])) { ?>
                                <a href="opinion_original.php?face_id=<?= $_GET["face_id"] ?>&id=<?= $reply_opinion['id'] ?>">查看原始意見</a>
                            <?php } else {
                                echo "無";
                            } ?>
                        </td>
                    </tr>

                    <div class="modal fade lade" id="give_score<?= $reply_opinion["id"] ?>">

                        <div class="modal-header">
                            <h3><?= $reply_opinion["name"] ?>的意見評價列表</h3>
                        </div>

                        <div class="modal-body">
                            <h4>
                                評價人數:<?= $count_score ?><br>
                                被評價平均分數:<?= $average ?><br>
                                評價總分:<?= $reply_opinion["score"] ?>
                            </h4>
                        </div>
                    </div>

                <?php } ?>
            </tbody>

        </table>

    </div>
</body>

</html>