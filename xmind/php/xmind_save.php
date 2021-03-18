<?php
    $link = mysqli_connect("localhost","root","root","my_project");
    mysqli_set_charset($link,"utf8");//以utf8讀取資料，讓資料可以讀取中文
    $today = date("Y-m-d H:i:s");
    $mind = $_POST['MindOut'];
    $user_id = $_POST['user_id']; // 上傳者的user_id
    $xmind_no = $_POST['xmind_no']; // 
    if ($xmind_no != "new") {
        $sql = "delete from xmind 
                      where XMIND_NO = '".$xmind_no."'";
        mysqli_query($link,$sql); // 先刪除之前的紀錄 
        $sql = "delete from xmind_branch 
                      where XMIND_NO = '".$xmind_no."'";
        mysqli_query($link,$sql); // 先刪除之前的紀錄
    }
    // 宣告函數  讓裡面都有數字 不然會找不到num陣列
    for ($i=0; $i <10000 ; $i++) { 
        $num[$i]=0;
    }
    // 取得最新的 XMIND_NO
    $sql = "select XMIND_NO
              from xmind
             order by XMIND_NO DESC
             limit 1";
    $result = $link->query($sql);
    $xmind_no = 1;
    while($row = mysqli_fetch_row($result))
    {
        $xmind_no = $row[0]+1;
        break;
    }

    //設定字體背景色
    if (isset($mind['root']['data']['background'])) 
        $background = $mind['root']['data']['background'];
    else 
        $background ="#5990B2"; // 設定成原始淡藍色
    //設定字體顏色
    if (isset($mind['root']['data']['color'])) 
        $fontColor = $mind['root']['data']['color'];
    else 
        $fontColor ="#fff"; // 設定成原始白色
    //設定字體粗細
    if (isset($mind['root']['data']['font-weight'])) 
        $fontWeight = "T";
    else 
        $fontWeight = "F"; // 如果沒有設定  在資料庫會變0 最後顯示出來會看不到
    $sql = "insert into xmind 
                        ( XMIND_NO ,
                            XMIND_ID, 
                            XMIND_LEVEL, 
                            XMIND_TEXT, 
                            XMIND_BACKGROUND, 
                            XMIND_FONT_COLOR, 
                            XMIND_FONT_WEIGHT, 
                            XMIND_UPDATE_TIME,
                            USER_ID) 
                    VALUES ('".$xmind_no."',
                            '000000',
                            '0', 
                            '".$mind['root']['data']['text']."',
                            '".$background."', 
                            '".$fontColor."',
                            '".$fontWeight."',
                            '".$today."',
                            '".$user_id."')"; 
    mysqli_query($link,$sql);  // 儲存
    if (isset($mind['root']['children'])) { //如果有分支的話
        $mind = $mind['root']['children'];
        for ($i=0; $i <count($mind) ; $i++) { 
            getChildren($mind[$i],1,'000000');
        }
    }
    
    function getChildren($mind,$l,$fa){
        global $num;
        global $link; // 全域變數
        global $xmind_no; 
        global $user_id;
        mysqli_set_charset($link,"utf8");//以utf8讀取資料，讓資料可以讀取中文
        $lv = $l; // 等級
        $father = $fa; // 上一端是誰

        $text = $mind['data']['text']; // 內容的文字
        $id = sprintf("%02d",$l).sprintf("%04d",$num[$l]); //id 由等級2碼+編號4碼  ex: 030033 
        if (isset($mind['data']['background'])) {
            $background = $mind['data']['background'];
        }
        else {
            if ($lv >= 2) $background =""; // 因為原始超過第二層是沒有底色的  故不給值 就給空值
            else $background ="#E9F0F4"; // 設定成原始淡藍色
        }
        if (isset($mind['data']['color'])) {
            $fontColor = $mind['data']['color'];
        }
        else {
            $fontColor ="#000"; // 設定成原始黑色
        }
        if (isset($mind['data']['font-weight'])) {
            $fontWeight = "T";
        }
        else {
            $fontWeight = "F"; // 如果沒有設定  在資料庫會變0 最後顯示出來會看不到
        }
        $str = "insert into xmind_branch 
                            ( XMIND_NO ,
                              XMIND_ID, 
                              XMIND_PID, 
                              XMIND_LEVEL, 
                              XMIND_TEXT, 
                              XMIND_BACKGROUND, 
                              XMIND_FONT_COLOR, 
                              XMIND_FONT_WEIGHT,
                              USER_ID) 
                     VALUES ('".$xmind_no."',
                             '".$id."', 
                             '".$fa."', 
                             '".$lv."', 
                             '".$text."',
                             '".$background."', 
                             '".$fontColor."',
                             '".$fontWeight."',
                             '".$user_id."')";
        echo $str;
        mysqli_query($link,$str); // 儲存
        $num[$l]++;
        // 判斷是否有children
        if (isset($mind['children'])) {
            $father = $id;
            $l++;
            for ($j=0; $j <count($mind['children']) ; $j++) { 
                getChildren($mind['children'][$j],$l,$father);
            }
        } 
    }
?>