<form method="POST" action="">
    <div>
        Дата min
        <input type="date" name="DATE_min" placeholder="Дата" value="<?=!empty($_REQUEST["DATE_min"]) ? date('Y-m-d', strtotime($_REQUEST["DATE_min"])) : ''?>">
    </div>
    <div>
        Дата max
        <input type="date" name="DATE_max" placeholder="Цена от" value="<?=!empty($_REQUEST["DATE_max"]) ? date('Y-m-d', strtotime($_REQUEST["DATE_max"])) : ''?>">
    </div>
    <div>
        Цена min
        <input type="text" name="COURSE_min" placeholder="Цена от" value="<?= $_REQUEST["COURSE_min"] ?>">
    </div>
    <div>
        Цена max
        <input type="text" name="COURSE_max" placeholder="Цена до" value="<?= $_REQUEST["COURSE_max"] ?>">
    </div>
    <!-- Добавьте другие поля фильтрации по аналогии -->

    <input type="submit" name="apply_filter" value="Применить фильтр">
</form>