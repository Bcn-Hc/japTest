<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 16-3-28
 * Time: 上午2:12
 */
//require_once('dbop.php');
function getJpData()
{
    $handle=fopen('data.csv','rb');
    //bom
    fread($handle,3);
    $ret=array();
    $lineNum=0;
    $col_names=null;
    while(($line=fgetcsv($handle))!==false)
    {
        if($lineNum>0) {
            $item=array();
            $item['lineNum']=$lineNum;
            $len=count($line);
            for($i=0;$i<$len;++$i){
                $item[$col_names[$i]]=$line[$i];
            }
            array_push($ret,$item);
        }else{
            $col_names=$line;
        }
        $lineNum++;
    }
    fclose($handle);
    return $ret;
}
if(isset($_POST['type']))
{
    try{
        if($_POST['type']=='csv')
        {
            if(isset($_POST['action']))
            {
                if($_POST['action']=="search_lesson" && isset($_POST['lesson']))
                {
                    $lessons=array();
                    $lesson_tmp_array=explode('|',$_POST['lesson']);
                    foreach($lesson_tmp_array as $lesson_tmp) {
                        $lesson_tmp_array2=explode('-',$lesson_tmp);
                        if(count($lesson_tmp_array2)>1) {
                            for($i=$lesson_tmp_array2[0];$i<=$lesson_tmp_array2[1];++$i) {
                                array_push($lessons,$i);
                            }
                        }else{
                            array_push($lessons,$lesson_tmp_array2[0]);
                        }
                    }
                    $jpData=getJpData();
                    $result=array();
                    foreach($jpData as $elem)
                    {
                        $lesson=$elem['lesson'];//lesson

                        if(in_array($lesson,$lessons))
                        {
                            array_push($result,$elem);
                        }else{

                        }
                    }
                    if(isset($_POST['random']) && $_POST['random']==1){
                        shuffle($result);
                    }
                    echo json_encode($result);
                }else if($_POST['action']=="search_keyword" && isset($_POST['keyword']))
                {

                    $jpData=getJpData();
                    $result=array();
                    foreach($jpData as $elem)
                    {
                        if(strpos($elem['content'],$_POST['keyword'])!==false ||
                            strpos($elem['translation'],$_POST['keyword'])!==false)
                        {
                            array_push($result,$elem);
                        }else{

                        }
                    }
                    echo json_encode($result);
                }
            }


        }
    }catch (Exception $e){
        echo $e->getMessage();
}


}
