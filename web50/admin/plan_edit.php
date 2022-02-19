<?php
require_once("../db.php");
//要更改的執行方案
$plan = sel("plan", ["id" => $_GET["id"]]);

//修改執行方案
if (isset($_POST["edit"])) {
    if (isset($_POST["face"])) {
        //刪掉重新新增一個
        del("plan", ["id" => $_POST["edit"]]);
        $newplan = ins("plan", ["id" => $_POST["edit"], "name" => $_POST["name"], "des" => $_POST["des"], "project_id" => $_GET["project_id"]]);
        foreach ($_POST["face"] as $face) {
            $opinion_id = join(",", $face["select"]);
        }
        upd("plan", ["opinion_id" => $opinion_id], ["id" => $newplan]);
        header("location:plan.php?id={$_GET['project_id']}");
    } else {
        echo "<script>alert('請選取一個意見');location.href='plan_edit.php?project_id={$_GET["project_id"]}&id={$_GET['id']}'</script>";
    }
}
?>
<!-- 修改執行方案 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form action="" method="post">
        <div class="container">
            <h2>修改執行方案</h2>
            <div class="pull-right">
                <a class="btn btn-inverse" href="plan.php?id=<?= $_GET['project_id'] ?>">返回</a>
                <button value="<?= $_GET['id'] ?>" name="edit" class="btn btn-success" type="submit">修改</button>
            </div>
            <hr>

            <div class="row">

                <div class="span3">
                    <h4>執行方案</h4>
                    執行方案名稱:
                    <input name="name" value="<?= $plan["name"] ?>" type="text" required><br>
                    執行方案說明:
                    <textarea name="des" id="" cols="5" rows="3" required><?= $plan["des"] ?></textarea>
                </div>

                <div class="span9">
                    <h4>意見選取</h4>

                    <div class="accordion">
                        <?php
                        $faces = sels("face", ["project_id" => $_GET["project_id"]]);
                        if (!empty($faces)) {
                            foreach ($faces as $face_key => $face) {
                                $opinions = sels("opinion", ["face_id" => $face["id"]]);
                        ?>

                                <div class="accordion-group">

                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" href="#<?= $face["id"] ?>">
                                            面向<?= $face_key + 1 ?>&nbsp;<?= $face["name"] ?>
                                        </a>
                                    </div>

                                    <div id="<?= $face["id"] ?>" class="accordion-body collapse">
                                        <div class="collapse-inner">
                                            <table class="table">

                                                <thead>
                                                    <th>意見標題</th>
                                                    <th>意見總分</th>
                                                </thead>

                                                <tbody>
                                                    <?php foreach ($opinions as $opinion_key => $opinion) { ?>
                                                        <tr>
                                                            <td><?= $opinion["name"] ?></td>
                                                            <td><?= $opinion["score"] ?></td>
                                                            <td><input id="<?= $opinion["id"] ?>" type="checkbox" name="face[<?= $face["id"] ?>][select][]" value="<?= $opinion["id"] ?>"></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>

                                                <!-- 讓以選取過的意見 checkbox 變成 checked -->
                                                <?php
                                                if (!empty($plan["opinion_id"])) {
                                                    $plan_opinions = explode(",", $plan["opinion_id"]);
                                                    foreach ($plan_opinions as $opinion_id) { ?>
                                                        <script>
                                                            var id = <?= $opinion_id ?>;
                                                            $("#" + id).attr("checked", true);
                                                        </script>
                                                <?php }
                                                } ?>

                                            </table>
                                        </div>
                                    </div>

                                </div>
                        <?php }
                        } ?>
                    </div>
                </div>

            </div>

        </div>
    </form>
</body>

</html>