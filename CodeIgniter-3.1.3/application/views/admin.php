<h2>Admin</h2>

<p>This page is only accessible by admins - no general public or members!</p>

<table>
 <tr>
 <th>Delete</th>
 <th>Freeze</th>
 <th>Username</th>
 <th>Password</th>
 <th>Access level</th>
 <th>Frozen</th>
 </tr>
<? foreach ($listing as $row) { ?>
 <tr>
 <td><a href="<?= base_url() ?>index.php?/Admin/delete/<?= $row['id']?>">D</a></td>
 <td><a href="<?= base_url() ?>index.php?/Admin/update/<?= $row['id']?>">F</a></td>
 <td><?= $row['username']?></td>
 <td><?= $row['password']?></td>
 <td><?= $row['accesslevel']?></td>
 <td><?= $row['frozen']?></td>
 </tr>
<? } ?>
</table>

<?= validation_errors(); ?>
<?= form_open('Admin/newentry') ?>
<?= form_fieldset("Add Entry") ?>
<?= form_label('First Name:', 'fname'); ?> <br>
<?= form_input(array('name' => 'fname',
 'id' => 'fname')); ?> <br>

<?= form_label('Last Name:', 'lname'); ?> <br>
<?= form_input(array('name' => 'lname',
 'id' => 'lname')); ?> <br>

<?= form_label('Phone Number:', 'phone'); ?> <br>
<?= form_input(array('name' => 'phone',
 'id' => 'phone')); ?> <br>

<?= form_label('E-mail:', 'email'); ?> <br>
<?= form_input(array('name' => 'email',
 'id' => 'email')); ?> <br>

<?= form_submit('usersubmit', 'Submit'); ?>
<?= form_fieldset_close(); ?>
<?= form_close() ?>
