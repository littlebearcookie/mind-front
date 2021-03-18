<?php
    $link = mysqli_connect("localhost","root","root","my_project");
    mysqli_set_charset($link,"utf8");//以utf8讀取資料，讓資料可以讀取中文
    $sum = 0;    
    $xmind_no = $_POST['xmind_no'];
    $sql = "select * 
              from xmind_branch
             where XMIND_NO='".$xmind_no."'
          order by XMIND_LEVEL";
    $data = mysqli_query($link,$sql);
    $lv = 0;
    // 先把等級分類
    for($i=0 ;$i< mysqli_num_rows($data);$i++){
        $rs = mysqli_fetch_array($data,MYSQLI_ASSOC);
        if ($lv != $rs['XMIND_LEVEL']) {
            $sum = 0;
            $lv = $rs['XMIND_LEVEL'];
        }
        $id = $rs['XMIND_ID']; // 只要前面三碼 用來比對Father

        $fatherId = $rs['XMIND_PID']; // 主題沒有Father
        if ($rs['XMIND_FONT_WEIGHT'] == 'F') {
            $fontWeight = "";
        }
        else {
            $fontWeight = "bold";
        }
        $mindArray[$lv][$sum]=array( 'id' => $id , 'lv' => $lv , 'fatherId' => $fatherId, 'text' => $rs['XMIND_TEXT'],
                                    'background' => $rs['XMIND_BACKGROUND'], 'color' => $rs['XMIND_FONT_COLOR'],
                                    'font-weight' => $fontWeight);
        $sum++;
    }
    // 把相同父節點的放在一起
    for ($i=1; $i <= $lv ; $i++) {
        $f = 0; // 用來計算相同的fatherId  
        for ($j=0; $j <count($mindArray[$i]) ; $j++) {
            $data =array('text' => $mindArray[$i][$j]['text'],
                        'background' => $mindArray[$i][$j]['background'],
                        'color' => $mindArray[$i][$j]['color'],
                        'font-weight' => $mindArray[$i][$j]['font-weight']);
            if ($i == $lv) {
               $m = array('data' => $data, 'fatherId' => $mindArray[$i][$j]['fatherId']);
               
            } // 最下面一層 不需要儲存自己的id 只要fatherId
            else {
                $m = array('data' => $data, 'fatherId' => $mindArray[$i][$j]['fatherId'] , 
                           'id' => $mindArray[$i][$j]['id'] );
            }
            if ($j == 0) {
                $mind[$i][$f][0] = $m;
            }
            else {
                $check = 0;
                for ($k=0; $k <count($mind[$i]) ; $k++) { 
                    if ($m['fatherId'] == $mind[$i][$k][0]['fatherId']) {
                        array_push($mind[$i][$k],$m);
                        $check =1;
                    }
                }
                if ($check ==0) {
                    $f++;
                    $mind[$i][$f][0] = $m;
                }//如果沒有找到相同的fatherId
            }       
        }
    } 
    
    for ($i=$lv; $i >1 ; $i--) { 
        $m = [];
        $mo = [];
        $son = count($mind[$i]);
        $fa = count($mind[$i-1]);
        $num = 0;
        if ($i != $lv) {
            $mlong = count($mArray);
            $a = 0;
            // 先排列成相同父節點
            for ($x=0; $x <$mlong ; $x++) {
                if ($x == 0) {
                    $mo[$a][0] = $mArray[$x];
                }
                else {
                    $check = 0;
                    for ($y=0; $y <count($mo) ; $y++) { 
                        if ($mArray[$x]['fatherId'] == $mo[$y][0]['fatherId']) {
                            array_push($mo[$y],$mArray[$x]);
                            $check =1;
                        }
                    }
                    if ($check ==0) {
                        $a++;
                        $mo[$a][0] = $mArray[$x];
                    }//如果沒有找到相同的fatherId
                }
            }
            $mlong = count($mo);
             // 尋找父節點
            for ($x=0; $x <$fa ; $x++) {
                $sum = count($mind[$i-1][$x]); 
                for ($y=0; $y <$sum ; $y++) {
                    $check = 0; //檢查是否有子結點
                    for ($l=0; $l <$mlong ; $l++) { 
                        if ($mo[$l][0]['fatherId'] == $mind[$i-1][$x][$y]['id']) {
                            $m[$num] = array('children' => $mo[$l] ,
                                            'data' => $mind[$i-1][$x][$y]['data'],
                                            'fatherId' => $mind[$i-1][$x][$y]['fatherId']);
                            $num++;
                            $check = 1;
                            break;
                        }
                    }
                    // 沒子結點
                    if ($check !=1) {
                        $m[$num] = array('data' => $mind[$i-1][$x][$y]['data'],
                                        'fatherId' => $mind[$i-1][$x][$y]['fatherId']);
                        $num++;
                    }
                }
            }
            $mArray = $m;
        }
        else {
            for ($j=0; $j <$fa ; $j++) { 
                $sum = count($mind[$i-1][$j]);
                for ($k=0; $k <$sum ; $k++) {
                    $check = 0; // 用來檢查有沒有兒子 
                    for ($l=0; $l <$son ; $l++) { 
                        if ($mind[$i][$l][0]['fatherId'] == $mind[$i-1][$j][$k]['id']) {
                            $m[$num] = array('children' => $mind[$i][$l] ,
                                            'data' => $mind[$i-1][$j][$k]['data'],
                                            'fatherId' => $mind[$i-1][$j][$k]['fatherId']);
                            $num++;
                            $check = 1;
                            break;
                        }
                    }
                    if ($check != 1) {
                        $m[$num] = array('data' => $mind[$i-1][$j][$k]['data'],
                                        'fatherId' => $mind[$i-1][$j][$k]['fatherId']);
                        $num++;
                    } //沒有兒子
                }
            }
            $mArray = $m; 
        }// 最下層 用的方法
    }
    // 只有一層的話  就不會有mArray 但有mind
    if (!isset($mArray) && isset($mind)) {
        $mArray = $mind[1][0];
    } 
    // 只有主題  其他東西都沒有  用來避免智障的
    else if (!isset($mind)) {
        $mArray =[];
    }
    $data = mysqli_query($link,"select * 
                                  from xmind 
                                 where XMIND_NO='".$xmind_no."'"); // 這邊不能寫person_no 不然主管會看不到其他人的
    $rs = mysqli_fetch_array($data,MYSQLI_ASSOC); 
    if ($rs['XMIND_FONT_WEIGHT'] == 'F') {
        $fontWeight = "";
    }
    else {
        $fontWeight = "bold";
    }
    $t = array('text' => $rs['XMIND_TEXT'], 'background' => $rs['XMIND_BACKGROUND'],
                'color' => $rs['XMIND_FONT_COLOR'], 'font-weight' => $fontWeight);
    $an =  array('children' =>$mArray , 'data' => $t);
    $a = array('root' =>$an,'template'=>'default','theme'=>'fresh-blue','version'=>'1.4.33');
    echo json_encode($a);
?>