<?php
require_once __DIR__ . '/../includes/functions.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('subjects.php');}verify_csrf();$user=current_user();$id=filter_input(INPUT_POST,'subject_id',FILTER_VALIDATE_INT)?:0;
$stmt=db()->prepare('SELECT id FROM subjects WHERE id=? AND user_id=?');$stmt->execute([$id,$user['id']]);
if(!$stmt->fetch()){flash('error','Subject not found.');redirect('subjects.php');}
if(($_POST['action']??'')==='delete'){$stmt=db()->prepare('DELETE FROM subjects WHERE id=? AND user_id=?');$stmt->execute([$id,$user['id']]);flash('success','Subject deleted. Related tasks are now unassigned.');}
redirect('subjects.php');
