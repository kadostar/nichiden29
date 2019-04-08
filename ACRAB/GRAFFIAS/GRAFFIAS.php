<?php
//エラーチェックするときは以下の1行をコメントアウト
ini_set('display_errors', "Off");

//POSTでフォームから送信されたデータを変数に格納
if(isset($_POST['filename'])){
    $filename = $_POST['filename'];
    //echo $filename;
}
if(isset($_POST['day'])){
    $day = $_POST['day'];
    //echo $day;
}
if(isset($_POST['title'])){
    $title = $_POST['title'];
    //echo $title;
}
if(isset($_POST['staffname'])){
    $staffname = $_POST['staffname'];
    //echo $staffname;
}

//現在のフォームの数をscript.jsからフォームを介して受け取る
$count = 1;
if(isset($_POST['count'])){
    $count = $_POST['count'];
    //echo $count;
}

//word,timingは配列で受け取る
if(isset($_POST['word'])){
    $word = $_POST['word'];
}
if(isset($_POST['timing'])){
    $timing = $_POST['timing'];
    //var_dump($timing);
}

//各フォームの各セレクトボックスで選択された星座を配列で受け取る
$projector_a = $_POST['projector_a'];
    //var_dump($projector_a);
$projector_b = $_POST['projector_b'];
    //var_dump($projector_b);
$projector_c = $_POST['projector_c'];
    //var_dump($projector_c);
$projector_d = $_POST['projector_d'];
    //var_dump($projector_d);
$projector_e = $_POST['projector_e'];
    //var_dump($projector_e);

//改行コードを$wordから削除する、逆に読みにくさの原因にもなっているので要改良か
$word = str_replace(array("\r", "\n"), '', $word);

//各フォームの各on_offラジオボタンの情報を配列で受け取る
$on_off_a = $_POST['on_off_a'];
$on_off_b = $_POST['on_off_b'];
$on_off_c = $_POST['on_off_c'];
$on_off_d = $_POST['on_off_d'];
$on_off_e = $_POST['on_off_e'];

//jsonファイルとして出力するための配列の雛形を作成、これに各情報を追加していく
$DATA_ARRAY = array(
    "info" => array(
        "day" => $day,
        "title" => $title,
        "name" => $staffname,
    ),
    "scenario" => array( 
        array(
            "timing" => "",
            "word" => "",
            "projector" => array(
                "Fst" => 1,
                "Gxy" => 1,
             ),
        ),
    ),
);
    //$DATA_ARRAYのprojectorの中に入れるための配列を調整、各セレクトボックスで星座が未選択の場合はprojectorの中に入れない
    for($n = 0;$n < $count; $n++){
        if($projector_a[$n] == "XXX"){
            unset($projector_a[$n]);
            unset($on_off_a[$n]);
        }
        if($projector_b[$n] == "XXX"){
            unset($projector_b[$n]);
            unset($on_off_b[$n]);
        }
        if($projector_c[$n] == "XXX"){
            unset($projector_c[$n]);
            unset($on_off_c[$n]);
        }
        if($projector_d[$n] == "XXX"){
            unset($projector_d[$n]);
            unset($on_off_d[$n]);
        }
        if($projector_e[$n] == "XXX"){
            unset($projector_e[$n]);
            unset($on_off_e[$n]);
        }
        
    }
    //POSTで受け取ったon/off(1/0)は文字列データなので、$on_offをコピーし、int型に変換した配列を作成
    $on_off_int_a = clone_int($on_off_a);
    $on_off_int_b = clone_int($on_off_b);
    $on_off_int_c = clone_int($on_off_c);
    $on_off_int_d = clone_int($on_off_d);
    $on_off_int_e = clone_int($on_off_e);

    function clone_int($array){
        if(is_array($array)){
            return array_map("clone_int",$array);
        }else{
            return intval($array);
        }
    }

    //scenarioの中に入れるarrayを作成
    for($i = 1;$i < $count+1 ; $i++){
        $DATA_ARRAY["scenario"][$i] = array(
            "timing" => $timing[$i-1],
            "word" => $word[$i-1],
            "projector" => array(
                $projector_a[$i-1] => $on_off_int_a[$i-1],
                $projector_b[$i-1] => $on_off_int_b[$i-1],
                $projector_c[$i-1] => $on_off_int_c[$i-1],
                $projector_d[$i-1] => $on_off_int_d[$i-1],
                $projector_e[$i-1] => $on_off_int_e[$i-1],
            ),
        );
        $DATA_ARRAY["scenario"][$i]["projector"] = array_filter($DATA_ARRAY["scenario"][$i]["projector"],"strlen");
    }
    
    //print_r($DATA_ARRAY);
    
//$DATA_ARRAYを配列からjson形式に変換
    $make = json_encode($DATA_ARRAY,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    
    //jsonファイルをscenarioディレクトリ内に作成する、デバッグ・テスト時にはコメントアウトすること
    if($filename){
        file_put_contents("scenario/".$filename.".json",$make);
        $result = file_get_contents("scenario/".$filename.".json");
    }
   //jsonリスト作成用、専用のボタンをクリック時に実行
    if(isset($_POST['listmake'])){
        foreach(glob("scenario/*.json") as $ListMake){
            $FileList[] = $ListMake;
    }
        for($j=0;$j<count($FileList);$j++){
            $FileListResult["scenariolist"][$j] = $FileList[$j];
        }
    $FileListMake = json_encode($FileListResult,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    file_put_contents("../scenario_list.json",$FileListMake);
    }
    
?>
<!DOCTYPE HTML>
<html>
<head>
  <meta charset="utf-8">
  <title>GRAFFIAS</title>
  <script src="jquery-3.1.0.js"></script>
  <script src="script.js"></script>
  <link rel="stylesheet" type="text/css" href="GRAFFIAS_CSS.css">
</head>
<body>
    <h1>GRAFFIAS</h1>
    <form id="frm" name="frm" action="GRAFFIAS.php" method="post" >
            <p>ファイル名：<br>
                <input type="text" id="filename" name="filename"/></p>
            <p>公演日:<br>
              <select id="day" name="day">
                  <option value="ソフト">ソフト</option>
                  <option value="一日目">一日目</option>
                  <option value="二日目">二日目</option>
                  <option value="三日目">三日目</option>
              </select></p>
            <p>タイトル:<br>
                <input type="text" id="title" name="title"/> </p>
            <p>担当者:<br>
                <input type="text" id="staffname" name="staffname"/></p>
            <p id="scenario">シナリオ</p>
                <div class="form-block" id="form_block[0]">
                    <!-- Closeボタン -->
                    <button type="button" id="close" style="display: none;">-</button>
                    <!--タイミング（ラジオボタン）-->
                    <p>タイミング:前<input type="radio" id="timing_pre[0]" name="timing[0]" value="pre" checked="checked"/>
                        後<input type="radio" id="timing_post[0]" name="timing[0]" value="post"/></p>
                    <!--表示星座絵（セレクト）-->
                    <p>操作:<br>
                        <select id="projector_a[0]" name="projector_a[0]">
                            <option value="XXX">--使用星座絵を選択--</option> <option value="And">アンドロメダ</option> 
                            <option value="Sgr">いて</option> <option value="Psc">うお</option>
                            <option value="Lep">うさぎ</option>　<option value="Boo">うしかい</option>　<option value="Tau">おうし</option>
                            <option value="CMa">おおいぬ</option> <option value="UMa">おおぐま</option> <option value="Vir">おとめ</option>
                            <option value="Ari">おひつじ</option> <option value="Ori">オリオン</option> <option value="Cas">カシオペヤ</option>
                            <option value="Cnc">かに</option> <option value="Crv">からす</option> <option value="Aur">ぎょしゃ</option>
                            <option value="Cet">くじら</option> <option value="Cep">ケフェウス</option> <option value="CMi">こいぬ</option>
                            <option value="UMi">こぐま</option> <option value="Lyr">こと</option> <option value="Sco">さそり</option>
                            <option value="Leo">しし</option> <option value="Lib">てんびん</option> <option value="Cyg">はくちょう</option>
                            <option value="Gem">ふたご</option> <option value="Peg">ペガスス</option> <option value="Per">ペルセウス</option>
                            <option value="Aqr">みずがめ</option> <option value="Cap">やぎ</option> <option value="Aql">わし</option>
                            <option value="Him">織姫</option> <option value="Hik">彦星</option> 
                            <option value="Spc">春の大曲線</option> <option value="Spt">春の大三角</option> <option value="Smt">夏の大三角</option>
                            <option value="Wnt">冬の大三角</option> <option value="Twv">黄道十二星座</option> 
                            <option value="Wnd">冬のダイヤモンド</option> <option value="Eth">エチオピア</option>
                        </select>
                        <!--星座表示on/off（ラジオボタン）-->
                        on<input type="radio" id="on_a[0]" name="on_off_a[0]" value= 1 checked="checked"/>
                        off<input type="radio" id="off_a[0]" name="on_off_a[0]" value= 0 checked="checked"/><br>
                        <select id="projector_b[0]" name="projector_b[0]">
                            <option value="XXX">--使用星座絵を選択--</option> <option value="And">アンドロメダ</option> 
                            <option value="Sgr">いて</option> <option value="Psc">うお</option>
                            <option value="Lep">うさぎ</option>　<option value="Boo">うしかい</option>　<option value="Tau">おうし</option>
                            <option value="CMa">おおいぬ</option> <option value="UMa">おおぐま</option> <option value="Vir">おとめ</option>
                            <option value="Ari">おひつじ</option> <option value="Ori">オリオン</option> <option value="Cas">カシオペヤ</option>
                            <option value="Cnc">かに</option> <option value="Crv">からす</option> <option value="Aur">ぎょしゃ</option>
                            <option value="Cet">くじら</option> <option value="Cep">ケフェウス</option> <option value="CMi">こいぬ</option>
                            <option value="UMi">こぐま</option> <option value="Lyr">こと</option> <option value="Sco">さそり</option>
                            <option value="Leo">しし</option> <option value="Lib">てんびん</option> <option value="Cyg">はくちょう</option>
                            <option value="Gem">ふたご</option> <option value="Peg">ペガスス</option> <option value="Per">ペルセウス</option>
                            <option value="Aqr">みずがめ</option> <option value="Cap">やぎ</option> <option value="Aql">わし</option>
                            <option value="Him">織姫</option> <option value="Hik">彦星</option>
                            <option value="Spc">春の大曲線</option> <option value="Spt">春の大三角</option> <option value="Smt">夏の大三角</option>
                            <option value="Wnt">冬の大三角</option> <option value="Twv">黄道十二星座</option> 
                            <option value="Wnd">冬のダイヤモンド</option> <option value="Eth">エチオピア</option>
                        </select>
                        on<input type="radio" id="on_b[0]" name="on_off_b[0]" value= 1 checked="checked"/>
                        off<input type="radio" id="off_b[0]" name="on_off_b[0]" value= 0 checked="checked"/><br>
                        <select id="projector_c[0]" name="projector_c[0]">
                            <option value="XXX">--使用星座絵を選択--</option> <option value="And">アンドロメダ</option> 
                            <option value="Sgr">いて</option> <option value="Psc">うお</option>
                            <option value="Lep">うさぎ</option>　<option value="Boo">うしかい</option>　<option value="Tau">おうし</option>
                            <option value="CMa">おおいぬ</option> <option value="UMa">おおぐま</option> <option value="Vir">おとめ</option>
                            <option value="Ari">おひつじ</option> <option value="Ori">オリオン</option> <option value="Cas">カシオペヤ</option>
                            <option value="Cnc">かに</option> <option value="Crv">からす</option> <option value="Aur">ぎょしゃ</option>
                            <option value="Cet">くじら</option> <option value="Cep">ケフェウス</option> <option value="CMi">こいぬ</option>
                            <option value="UMi">こぐま</option> <option value="Lyr">こと</option> <option value="Sco">さそり</option>
                            <option value="Leo">しし</option> <option value="Lib">てんびん</option> <option value="Cyg">はくちょう</option>
                            <option value="Gem">ふたご</option> <option value="Peg">ペガスス</option> <option value="Per">ペルセウス</option>
                            <option value="Aqr">みずがめ</option> <option value="Cap">やぎ</option> <option value="Aql">わし</option>
                            <option value="Him">織姫</option> <option value="Hik">彦星</option>
                            <option value="Spc">春の大曲線</option> <option value="Spt">春の大三角</option> <option value="Smt">夏の大三角</option>
                            <option value="Wnt">冬の大三角</option> <option value="Twv">黄道十二星座</option> 
                            <option value="Wnd">冬のダイヤモンド</option> <option value="Eth">エチオピア</option>
                        </select>
                        on<input type="radio" id="on_c[0]" name="on_off_c[0]" value= 1 checked="checked"/>
                        off<input type="radio" id="off_c[0]" name="on_off_c[0]" value= 0 checked="checked"/><br>
                        <select id="projector_d[0]" name="projector_d[0]">
                            <option value="XXX">--使用星座絵を選択--</option><option value="And">アンドロメダ</option> 
                            <option value="Sgr">いて</option> <option value="Psc">うお</option>
                            <option value="Lep">うさぎ</option>　<option value="Boo">うしかい</option>　<option value="Tau">おうし</option>
                            <option value="CMa">おおいぬ</option> <option value="UMa">おおぐま</option> <option value="Vir">おとめ</option>
                            <option value="Ari">おひつじ</option> <option value="Ori">オリオン</option> <option value="Cas">カシオペヤ</option>
                            <option value="Cnc">かに</option> <option value="Crv">からす</option> <option value="Aur">ぎょしゃ</option>
                            <option value="Cet">くじら</option> <option value="Cep">ケフェウス</option> <option value="CMi">こいぬ</option>
                            <option value="UMi">こぐま</option> <option value="Lyr">こと</option> <option value="Sco">さそり</option>
                            <option value="Leo">しし</option> <option value="Lib">てんびん</option> <option value="Cyg">はくちょう</option>
                            <option value="Gem">ふたご</option> <option value="Peg">ペガスス</option> <option value="Per">ペルセウス</option>
                            <option value="Aqr">みずがめ</option> <option value="Cap">やぎ</option> <option value="Aql">わし</option>
                            <option value="Him">織姫</option> <option value="Hik">彦星</option>
                            <option value="Spc">春の大曲線</option> <option value="Spt">春の大三角</option> <option value="Smt">夏の大三角</option>
                            <option value="Wnt">冬の大三角</option> <option value="Twv">黄道十二星座</option> 
                            <option value="Wnd">冬のダイヤモンド</option> <option value="Eth">エチオピア</option>
                        </select>
                        on<input type="radio" id="on_d[0]" name="on_off_d[0]" value= 1 checked="checked"/>
                        off<input type="radio" id="off_d[0]" name="on_off_d[0]" value= 0 checked="checked"/><br>
                        <select id="projector_e[0]" name="projector_e[0]">
                            <option value="XXX">--使用星座絵を選択--</option><option value="And">アンドロメダ</option> 
                            <option value="Sgr">いて</option> <option value="Psc">うお</option>
                            <option value="Lep">うさぎ</option>　<option value="Boo">うしかい</option>　<option value="Tau">おうし</option>
                            <option value="CMa">おおいぬ</option> <option value="UMa">おおぐま</option> <option value="Vir">おとめ</option>
                            <option value="Ari">おひつじ</option> <option value="Ori">オリオン</option> <option value="Cas">カシオペヤ</option>
                            <option value="Cnc">かに</option> <option value="Crv">からす</option> <option value="Aur">ぎょしゃ</option>
                            <option value="Cet">くじら</option> <option value="Cep">ケフェウス</option> <option value="CMi">こいぬ</option>
                            <option value="UMi">こぐま</option> <option value="Lyr">こと</option> <option value="Sco">さそり</option>
                            <option value="Leo">しし</option> <option value="Lib">てんびん</option> <option value="Cyg">はくちょう</option>
                            <option value="Gem">ふたご</option> <option value="Peg">ペガスス</option> <option value="Per">ペルセウス</option>
                            <option value="Aqr">みずがめ</option> <option value="Cap">やぎ</option> <option value="Aql">わし</option>
                            <option value="Him">織姫</option> <option value="Hik">彦星</option>
                            <option value="Spc">春の大曲線</option> <option value="Spt">春の大三角</option> <option value="Smt">夏の大三角</option>
                            <option value="Wnt">冬の大三角</option> <option value="Twv">黄道十二星座</option> 
                            <option value="Wnd">冬のダイヤモンド</option> <option value="Eth">エチオピア</option>
                        </select>
                        on<input type="radio" id="on_e[0]" name="on_off_e[0]" value= 1 checked="checked"/>
                        off<input type="radio" id="off_e[0]" name="on_off_e[0]" value= 0 checked="checked"/><br>
                    </p>
                    <p>セリフ:<br>
                        <textarea id="word[0]" name="word[0]" rows="6" cols="60"></textarea>
                    </p>
                </div>
        <div class="form-block" id="form_add">
            <button type="button" id="add">+</button>
        </div>
        <div class="form-block" id="form_send"><br>
            <input type="button" id="send_button"  value="JSONを作成"  />
        <!--    <input type="reset" id="reset_button" value="リセット"/>    -->
        </div> 
        <!--script.js内でフォーム個数をカウントするfrm_cntグローバル変数を受け取ってphpに渡す用-->
        <input type="hidden" name="count" value="" />
        <div class="form-block" id="form_confirm">
            <p>出力したJSON: <?php 
                if($filename)
                    echo $filename.".json"
                 ?><br>
                <textarea readonly id="resulttext" rows="46" cols="60">
                <?php print_r($result); ?>
                </textarea><br>
                <small>JSONの整形と構文チェックをやってくれるサイト(別タブ)→
                    <a href="https://lab.syncer.jp/Tool/JSON-Viewer/" target="_blank">JSON-Viewer</a></small>
            </p>
        </div>
      </form>
      <form id="frm_listmake" name="frm_listmake" action="GRAFFIAS.php" method="post">
            <input type="submit" id="listmake"  name="listmake" value="JSONリストの作成"　/>
      </form>
    <footer>
        <small>28日電作成/chromeでの表示推奨です/写真提供:28須田くん/ver1.10</small>
    </footer>
</body>
</html>
  

