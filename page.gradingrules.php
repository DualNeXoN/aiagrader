<!-- Page content-->
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col text-center"><b>Grading rules</b></div>
    </div>
    <div class="row justify-content-center">
        <div class="col text-center">
            <?php
            result();
            ?>
        </div>
    </div>
</div>

<?php

function result() {
    $actions = array();
    $actions[] = new Action("Button1", "Click");

    $componentResults = array();
    //$componentResults[] = new ComponentResult();

    $ruleSet = new RuleSet($actions, $componentResults);
    /*if (isset($_SESSION['rules'])) {

    } else {
        echo "No rules set.";
    }*/
}