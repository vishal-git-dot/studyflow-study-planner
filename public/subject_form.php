<?php
require_once __DIR__ . '/../includes/functions.php'; require_login();
$user=current_user(); $id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: 0; $subject=['name'=>'','description'=>'','color'=>'#6259d9']; $errors=[];
if($id){$stmt=db()->prepare('SELECT * FROM subjects WHERE id=? AND user_id=?');$stmt->execute([$id,$user['id']]);$found=$stmt->fetch();if(!$found){flash('error','Subject not found.');redirect('subjects.php');}$subject=$found;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$subject['name']=trim((string)($_POST['name']??''));$subject['description']=trim((string)($_POST['description']??''));$subject['color']=(string)($_POST['color']??'#6259d9');
 if(mb_strlen($subject['name'])<2||mb_strlen($subject['name'])>100)$errors[]='Subject name must be between 2 and 100 characters.';
 if(mb_strlen($subject['description'])>500)$errors[]='Description must be 500 characters or fewer.';
 if(!preg_match('/^#[0-9a-fA-F]{6}$/',$subject['color']))$subject['color']='#6259d9';
 if(!$errors){if($id){$stmt=db()->prepare('UPDATE subjects SET name=?,description=?,color=? WHERE id=? AND user_id=?');$stmt->execute([$subject['name'],$subject['description'],$subject['color'],$id,$user['id']]);flash('success','Subject updated.');}else{$stmt=db()->prepare('INSERT INTO subjects(user_id,name,description,color) VALUES(?,?,?,?)');$stmt->execute([$user['id'],$subject['name'],$subject['description'],$subject['color']]);flash('success','Subject created.');}redirect('subjects.php');}
}
$pageTitle=$id?'Edit subject':'Add subject';$activePage='subjects';require __DIR__.'/../includes/header.php';
?>
<section class="page-heading"><div><div class="eyebrow">KEEP IT ORGANIZED</div><h1><?= $id?'Edit subject':'Create a subject' ?></h1><p>Set up a home for related study tasks.</p></div><a class="btn btn-secondary" href="subjects.php">← Back to subjects</a></section>
<section class="panel form-panel"><?php foreach($errors as $err): ?><div class="toast toast-error" role="alert"><span>!</span><?= e($err) ?></div><?php endforeach; ?><form method="post" data-validate><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid"><div class="form-field full"><label for="name">Subject name *</label><input id="name" name="name" maxlength="100" placeholder="e.g. Data Structures" value="<?= e($subject['name']) ?>" required></div><div class="form-field full"><label for="description">Description</label><textarea id="description" name="description" maxlength="500" placeholder="What are you learning in this subject?"><?= e($subject['description']) ?></textarea><span class="form-help">Optional · Up to 500 characters.</span></div><div class="form-field"><label for="color">Subject color</label><input id="color" name="color" type="color" value="<?= e($subject['color']) ?>" style="padding:5px;max-width:100px"></div></div><div class="form-actions"><button class="btn btn-primary" type="submit"><?= $id?'Save changes':'Create subject' ?></button><a class="btn btn-secondary" href="subjects.php">Cancel</a></div></form></section>
<?php require __DIR__.'/../includes/footer.php'; ?>
