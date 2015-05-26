<?php
/* @var $data array */
/* @var $date_begin string */
/* @var $date_end string */
?>
<div class="well well-sm">
  <h2 class="centered">
    Загальна статистика надання відповіді загальному відділу
    (<?php echo date("d.m.Y",strtotime($date_begin))
      ." - ".date("d.m.Y",strtotime($date_end)); ?>)
  </h2>
<table class="table table-bordered table-striped">
  <tr>
    <th>Підрозділ</th>
    <th>К-сть прийнятих розсилок</th>
    <th>К-сть ігнорованих розсилок</th>
  </tr>
<?php
foreach ($data as $row){
  ?>
  <tr>
    <td><?php echo $row['DepartmentName']; ?></td>
    <td style="color: green;"><?php echo $row['ans_cnt']; ?></td>
    <td <?php echo (($row['not_ans_cnt']>0)? 'style="color: red;"':''); ?> >
      <?php echo $row['not_ans_cnt']; ?></td>
  </tr>
  <?php
}

?>
</table>
</div>

