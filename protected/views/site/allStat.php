<?php
/* @var $data array */
?>
<div class="well well-sm">
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

