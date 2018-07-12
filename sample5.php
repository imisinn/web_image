<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>集合写真検索システム</title>
  </head>
  <body>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
   <p>
   複数入力する場合は半角空白やコンマで区切ってください。<br>
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
$flag = 0;//flag = 1:単一指定、 2:範囲指定 3:入力エラーミス
$keywords = preg_split("/[\s,]+/",$_POST["keyword"]);

if (isset($_POST["keyword"]) && isset($_POST["number"]) && isset($_POST["number_max"]) && isset($_POST["number_min"])) {
    foreach ($keywords as $oneword) {
        if(array_key_exists($oneword, $tf_data)){
          $nump = mb_convert_kana($_POST["number"], "n", "utf-8");
          $max = mb_convert_kana($_POST["number_max"], "n", "utf-8");
          $min = mb_convert_kana($_POST["number_min"], "n", "utf-8");


      //!preg_match("/^[0-9]+$/", $nump) || !preg_match("/^[0-9]+$/", $max)  || !preg_match("/^[0-9]+$/", $min)
      	if($_POST["number"]==null && $_POST["number_max"]==null &&  $_POST["number_min"]==null  ){
      		echo "人数を入力して下さい。";
              $flag = 3;
      	}else if($_POST["number"]!=null && !preg_match("/^[0-9]+$/", $nump)){
              echo "人数を正しく入力してください。";
              $flag = 3;
          }else if($_POST["number_max"]!=null && !preg_match("/^[0-9]+$/", $max)){
              echo "人数を正しく入力してください。";
              $flag = 3;
          }elseif ($_POST["number_min"]!=null && !preg_match("/^[0-9]+$/", $min)){
              echo "人数を正しく入力してください。";
              $flag = 3;
          }elseif ($nump != null && ($max != null || $min != null)) {
              echo "範囲指定か、単一な人数指定かどちらかにしてください :2";
              $flag = 3;
          }else {
          }
          echo "<hr><br>\n";
          //echo "flag : '$flag'　<br>";

          if($flag != 3){//入力エラーミス出ない場合
              if($max == null && $min != null){//最大が入力されていない場合
                  //echo "最大未入力<br>";
                  $flag = 2;
                  $max = "1000";
                  echo "キーワード「".$oneword."」　人数「";
                  echo $min."人以上」での検索結果<br>\n";
              }elseif ($min == null && $max != null){//最小が入力されていない場合
                  //echo "最小未入力<br>";
                  $flag = 2;
                  $min = "0";
                  echo "キーワード「".$oneword."」　人数「";
                  echo $max."人以下」の検索結果<br>\n";
              }elseif ($nump != null){//単一指定の場合
                  //echo "単一指定<br>";
                  $flag = 1;
                  echo "キーワード「".$oneword."」　人数「";
                  echo $nump."人」での検索結果<br>\n";
              }else{//最大、最小が入力されている場合
                  //echo "範囲両方指定<br>";
                  $flag = 2;
                  echo "キーワード「".$oneword."」　人数「";
                  echo $min."人以上".$max."人以下」での検索結果<br>\n";
              }
          }


          //echo "<br><br>単一指定 '$nump' 最小：　'$min' 最大: '$max' flag: '$flag'<br><br>";


          if($flag != 3){
              foreach($tf_data[$oneword] as $key => $val ) {
                  if($flag == 1){
                      if ($nump == @$fc_data[$key]){
                		echo "<img src='$key'><br>\n";
                		echo "キーワード出現回数＝".$val."回<br>\n";
                		echo "写真中の人の数＝".@$fc_data[$key]."人<br>\n";
                		echo "$key<br><br><br>\n";
                		$result_num++;
                      }
                  }elseif ($flag == 2) {
                      if ($min <= @$fc_data[$key] && $max >= @$fc_data[$key]){
                		echo "<img src='$key'><br>\n";
                		echo "キーワード出現回数＝".$val."回<br>\n";
                		echo "写真中の人の数＝".@$fc_data[$key]."人<br>\n";
                		echo "$key<br><br><br>\n";
                		$result_num++;
                      }
                  }
              }

          }

      } elseif ($oneword==null) {
        echo '検索キーワードを入力して下さい。';
      } else {
        echo '検索キーワードに合致する写真はありません。';
      }
    }
}

if($flag != 3)echo "<br><br>検索結果は".$result_num."件でした。";

?>
</body>
</html>
