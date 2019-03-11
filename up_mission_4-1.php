<?php

echo '新規投稿時にすきなパスワードを入れてください。'."<br>";
echo '削除・編集時には投稿に使ったパスワードを入力してください。'."<br>";
echo '編集内容を投稿するときはパスワードは必要ありません。'."<br>";


//接続する
$dsn='mysql:dbname = データベース名;host=localhost';
$user = 'ユーザー名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//tableをつくる
$sql = "CREATE TABLE IF NOT EXISTS t_mission_4_1"
." ("//以下はテーブルの中の要素
. "count INT auto_increment primary key,"
. "name char(32),"//
. "comment TEXT,"
. "date TEXT,"
. "pass_1 char(32)"
.");";
$stmt = $pdo->query($sql);

//日時の取得
date_default_timezone_set('Japan');

if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['pass_1'])){//isset()でデータがあるか確認
	//tableに書き込む
	$name = $_POST['name'];
	$comment =  $_POST['comment'];
	$date = date('"Y年m月d日 A H時i分s秒"');
	$pass_1 = $_POST['pass_1'];
	$sql = $pdo -> prepare("INSERT INTO t_mission_4_1 (count,name, comment,date,pass_1) VALUES ('',:name, :comment,:date,:pass_1)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	$sql -> bindParam(':pass_1',$pass_1,PDO::PARAM_STR);

	$sql -> execute();
}//ifの終わり



//削除
if(!empty($_POST['delete']) && !empty($_POST['pass_2'])){//削除対象番号が空じゃないとき(削除番号と一致＆パスワードが一致のときに上書きしなければいい)
	$delete = $_POST['delete'];
	$pass_2 = $_POST['pass_2'];
	$sql = 'SELECT * FROM t_mission_4_1'; 
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();//テーブル全体を取ってくる
	foreach ($results as $row){//ループさせる
		if($row['count'] == $delete and $row['pass_1'] == $pass_2){
			//3-8を参考に削除を実行
			$sql = 'delete from t_mission_4_1 where count=:count';
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam(':count', $delete, PDO::PARAM_INT);
			$stmt->execute();
		}
		if($row['count'] == $delete and $row['pass_1'] != $pass_2){
			echo 'パスワードが違います';
		}//書き込み後一旦ファイルを閉じて投稿を終了!
	}//ループ終了
}

if(!empty($_POST['edit']) && !empty($_POST['pass_3'])){//編集対象番号が空じゃないとき
	$edit = $_POST['edit'];
	$pass_3 = $_POST['pass_3'];
	$sql = 'SELECT * FROM t_mission_4_1'; 
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();//テーブル全体を取ってくる
	foreach($results as $row){//ループさせる
		if($row['count'] == $edit and $row['pass_1'] == $pass_3){//投稿番号と一致するとき		
			$name_e = $row['name'];//名前とコメントを変数に入れる
			$comment_e = $row['comment'];
		}
		if($row['count'] == $edit and $row['pass_1'] != $pass_3){
				echo 'パスワードが一致しません'."<br>";
		}
	}
}

if(!empty($_POST['edit_num']) && !empty($_POST['name']) && !empty($_POST['comment'])){//isset()でデータがあるか確認
$edit_num = $_POST['edit_num'];
$name_new = $_POST['name'];
$comment_new = $_POST['comment'];
$date_new = date('Y年m月d日 A H時i分s秒');
$sql = 'SELECT * FROM t_mission_4_1'; 
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
	foreach($results as $row){//ループさせる
			if($row['count'] == $edit_num){//投稿番号と一致
				//なにもしない
				$count =$edit_num;
				//bindParamの引数(:nameなど)は3-2でどんな名前のカラムを設定したかで変える必要がある。
				$sql = 'update t_mission_4_1 set name=:name,comment=:comment,date=:date where count=:count';
				$stmt = $pdo->prepare($sql);

				$stmt->bindParam(':name', $name_new, PDO::PARAM_STR);
				$stmt->bindParam(':comment', $comment_new, PDO::PARAM_STR);
				$stmt->bindParam(':date', $date_new, PDO::PARAM_STR);
				$stmt->bindParam(':count', $count, PDO::PARAM_INT);
				$stmt->execute();
			}
		}//ループ終了
}
?>

<!DOCTYPE html>
<html lang = "ja">
	<head>
	<meta charset = "UTF-8">
	<title>mission_4-1</title>
	</head>
	<body>
		<form action = "mission_4-1.php" method = "post">
		<label>名前:</label>
		<input type = "text" name = "name" placeholder = "名前"  value = "<?php echo $name_e ?>" ><br><!-- inputがないとフォームがでない -->
		
		<label>コメント:</label>
		<input type = "text" name = "comment" placeholder = "コメント" value = "<?php echo $comment_e ?>" ><br>
<!-- editで送られた番号をhiddenで編集内容と一緒に送る -->
		<input type = "hidden" name = "edit_num" value = "<?php echo $edit ?>" ><!-- htmlのなかに変数を組み込む際の書き方に注意value -->
<!-- pass_1で送られた番号をhiddenで編集内容と一緒に送る -->
		<input type = "hidden" name = "pass_word_1" placeholder = "<?php echo $pass_1 ?>" ><!-- htmlのなかに変数を組み込む際の書き方に注意value -->
<!-- パスワードのフォームを追加 -->
		<label>pass_1:</label>
		<input type = "text" name = "pass_1" placeholder = "パスワード" >
		<input type = "submit" value = "送信"><br>
<br>
<!-- 削除対象番号のフォームを追加 -->
		<label>削除対象番号:</label>
		<input type = "text" name = "delete" placeholder = "削除対象番号" ><br>
<!-- パスワードのフォームを追加 -->
		<label>pass_2:</label>
		<input type = "text" name = "pass_2" placeholder = "パスワード" >
		<input type = "submit" value = "削除"><br>
<br>
<!-- 編集対象番号のフォームを追加 -->
		<label>編集対象番号:</label>
		<input type = "text" name = "edit" placeholder = "編集対象番号" ><br>
<!-- パスワードのフォームを追加 -->
		<label>pass_3:</label>
		<input type = "text" name = "pass_3" placeholder = "パスワード" >
		<input type = "submit" value = "編集"><br>
		</form>
	</body>
</html>

<?php
//表示させる
$sql = 'SELECT count,name,comment,date,pass_1 FROM t_mission_4_1 order by count asc';//count,name,comment,date,pass_1を出力、countで昇順に並び替え 
$stmt = $pdo->query($sql);
$results = $stmt->fetchAll();
foreach ($results as $row){
//$rowの中にはテーブルのカラム名が入る
echo $row['count'].',';
echo $row['name'].',';
echo $row['comment'].',';
echo $row['date'].',';
echo $row['pass_1'].'<br>';
}
?>

