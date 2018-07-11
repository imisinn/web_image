<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>集合写真検索システム</title>
  </head>
  <body>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
   <p>
   検索キーワード：<input type="text" name="keyword" size=20/><br>
   <br>人数範囲指定か人数指定どちらかにしてください<br><br>
   写真中の人の数：<input type="text" name="number" size=20 /><br>
   写真中の人の最大の数：<input type="text" name="number_max" size=20 /><br>
   写真中の人の最小の数：<input type="text" name="number_min" size=20 /><br>
   <input type="submit" value="Search!" />
   </p>
</form>

<?php

// tfファイルの読み込み　１行ずつ　最後まで
$tf_data = array( array());
$tffile = "tfimg.all";
$f1 = fopen($tffile, "r");
while (! feof ($f1)) {
  $line = fgets($f1);
  $tf_line = preg_split( "/\t/" , $line );
  @$tf_line[2] = preg_replace("/\r|\n/","",$tf_line[2]);
  @$tf_data[$tf_line[0]][$tf_line[2]] = $tf_line[1];
}
fclose($f1);
// tfファイルの読み込み　ここまで


// fcファイルの読み込み　１行ずつ　最後まで
$fc_data = array();
$fcfile = "fcimg.all";
$f2 = fopen($fcfile, "r");
while (! feof ($f2)) {
  $line = fgets($f2);
  $fc_line = preg_split( "/\t/" , $line );
  @$fc_line[1] = preg_replace("/\r|\n/","",$fc_line[1]);
  $fc_data[$fc_line[1]] = $fc_line[0];
}
fclose($f2);
// fcファイルの読み込み　ここまで


// 以下、検索処理
$result_num = 0;
$flag = 0;

if (isset($_POST["keyword"]) && isset($_POST["number"]) && isset($_POST["number_max"]) && isset($_POST["number_min"])) {
  if(array_key_exists($_POST["keyword"], $tf_data)){
    $nump = mb_convert_kana($_POST["number"], "n", "utf-8");
    $max = mb_convert_kana($_POST["number_max"], "n", "utf-8");
    $min = mb_convert_kana($_POST["number_min"], "n", "utf-8");


//!preg_match("/^[0-9]+$/", $nump) || !preg_match("/^[0-9]+$/", $max)  || !preg_match("/^[0-9]+$/", $min)
	if($_POST["number"]==null && $_POST["number_max"]==null &&  $_POST["number_min"]==null  ){
		echo "人数を入力して下さい。";
        $flag = -1;
	}else if($_POST["number"]!=null && !preg_match("/^[0-9]+$/", $nump)){
        echo "人数を正しく入力してください。";
        $flag = -1;
    }else if($_POST["number_max"]!=null && !preg_match("/^[0-9]+$/", $max)){
        echo "人数を正しく入力してください。";
        $flag = -1;
    }elseif ($_POST["number_min"]!=null && !preg_match("/^[0-9]+$/", $min)){
        echo "人数を正しく入力してください。";
        $flag = -1;
    } else {
    	echo "キーワード「".$_POST["keyword"]."」　人数「";
    	echo $_POST["number"]."人」での検索結果<br>\n";
    }
    echo "<hr><br>\n";
    if($flag == -1)echo "検索中止";

    foreach($tf_data[@$_POST["keyword"]] as $key => $val ) {
      if ($nump == @$fc_data[$key] && $nump<>null){
		echo "<img src='$key'><br>\n";
		echo "キーワード出現回数＝".$val."回<br>\n";
		echo "写真中の人の数＝".@$fc_data[$key]."人<br>\n";
		echo "$key<br><br><br>\n";
		$result_num++;
      }
    }

  } elseif (@$_POST["keyword"]==null) {
    echo '検索キーワードを入力して下さい。';
  } else {
    echo '検索キーワードに合致する写真はありません。';
  }
}

echo "検索結果は".$result_num."件でした。";

?>
</body>
</html>
