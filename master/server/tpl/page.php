<html>
<head>
    <title>Garden</title>
    <?php require_once 'header.php'; ?>
</head>
<body>

<div>
    <p>Total rows: <?php echo $Db->getCount(); ?></p>
    <ul class="stat">
        <?php foreach ($last_ids as $sources) { ?>
        <?php foreach ($sources as $table_object => $id) { ?>
            <li><?php echo $table_object;?> - <?php echo $id;?></li>
            <?php } ?>
        <?php } ?>
    </ul>
    <div>
        <p>Last ping: <?php echo $last_ping;?></p>
    </div>
</div>

<?php
    $table = $Db->getData(array(
        'limit' => 100,
    ));

//    $header_all = array_keys($table[0]);
    $header_small = array(
        'ID',
        'ID_OBJ',
        'SDATE',
        'MES',
        'mes_type',
        'ID_MESGRP',
        'ZONA',
        'ADATE',
    );
    $header = $header_small;
?>
<table class="all-table tablesorter">
    <thead>
        <tr>
            <?php foreach($header as $h) { ?>
            <td><?php echo $h;?></td>
            <?php } ?>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <?php foreach($header as $h) { ?>
                <td><?php echo $h;?></td>
            <?php } ?>
        </tr>
    </tfoot>
    <tbody>
        <?php foreach($table as $row) { ?>
        <tr>
            <?php foreach($header as $col) { ?>
                <td><?php echo $row[$col]; ?></td>
            <?php } ?>
        </tr>
        <?php } ?>
    </tbody>
</table>
</body>
</html>