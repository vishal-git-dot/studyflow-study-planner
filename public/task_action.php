<?php
require_once __DIR__ . '/../includes/functions.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST'){redirect('tasks.php');}verify_csrf();$user=current_user();$id=filter_input(INPUT_POST,'task_id',FILTER_VALIDATE_INT)?:0;$action=(string)($_POST['action']??'');
$stmt=db()->prepare('SELECT id,status FROM study_tasks WHERE id=? AND user_id=?');$stmt->execute([$id,$user['id']]);$task=$stmt->fetch();
if(!$task){flash('error','Task not found.');redirect('tasks.php');}
if($action==='toggle'){$next=$task['status']==='completed'?'pending':'completed';$stmt=db()->prepare('UPDATE study_tasks SET status=?,completed_at=? WHERE id=? AND user_id=?');$stmt->execute([$next,$next==='completed'?date('Y-m-d H:i:s'):null,$id,$user['id']]);flash('success',$next==='completed'?'Task marked completed.':'Task moved back to pending.');}
elseif($action==='delete'){$stmt=db()->prepare('DELETE FROM study_tasks WHERE id=? AND user_id=?');$stmt->execute([$id,$user['id']]);flash('success','Task deleted.');}
else flash('error','Unknown action.');
$back=(string)($_POST['return_to']??'tasks.php');$allowed=['tasks.php','dashboard.php','calendar.php'];redirect(in_array($back,$allowed,true)?$back:'tasks.php');
