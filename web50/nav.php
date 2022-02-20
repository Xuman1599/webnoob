<div class="navbar navbar-inverse navbar-static-top">
    <div class="navbar-inner">
        <a href="" class="brand">專案討論系統</a>
        <ul class="nav">
            <?php
            if ($_SESSION["user"]["level"] == 1) {
            ?>
                <li><a href="account_main.php">帳號管理</a></li>
                <li><a href="project_main.php">專案管理</a></li>
                <li><a href="chart.php?mode=user_total">圖形統計</a></li>
            <?php } ?>
            <li class="pull-right ml-auto"><a class="pull-right" href="../logout.php">登出</a></li>
        </ul>
    </div>
</div><br><br>