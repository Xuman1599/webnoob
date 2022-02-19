<?php
require_once("../db.php");



// 新增執行方案
if (isset($_POST["insert"])) {
    if (isset($_POST["face"])) {
        $newplan = ins("plan", ["name" => $_POST["name"], "des" => $_POST["des"], "project_id" => $_POST["insert"]]);
        foreach ($_POST["face"] as $face) {
            $opinion_id = join(",", $face["select"]);
            upd("plan", ["opinion_id" => $opinion_id], ["id" => $newplan]);
        }
        header("location:plan.php?id={$_GET['id']}");
    } else {
        echo "<script>alert('請選取一個意見');location.href='plan_ins.php?id={$_GET['id']}'</script>";
    }
}
?>

<!-- 新增執行方案頁面 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("header.php"); ?>
</head>

<body>
    <?php require_once("../nav.php"); ?>


    <form action="" method="post">
        <div class="container">

            <h2>建立執行方案</h2>
            <div class="pull-right">
                <a class=" btn btn-inverse" href="plan.php?id=<?= $_GET['id'] ?>">返回</a>
                <button value="<?= $_GET['id'] ?>" name="insert" class=" btn" type="submit">建立</button>
            </div>
            <hr>

            <div class="row">

                <div class="span3">
                    <h4>執行方案</h4>
                    執行方案名稱:
                    <input name="name" type="text" required><br>
                    執行方案說明:
                    <textarea name="des" id="" cols="5" rows="3" required></textarea>
                </div>

                <div class="span9">
                    <h4>意見選取</h4>

                    <div class="accordion">
                        <?php
                        $faces = sels("face", ["project_id" => $_GET["id"]]);
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
                                                            <td><input value="<?= $opinion["id"] ?>" <?= ($opinion_key == 0) ? "checked" : "" ?> type="checkbox" name="face[<?= $face["id"] ?>][select][]" id=""></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>

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