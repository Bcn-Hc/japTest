<?php
/**
 * Created by PhpStorm.
 * User: LiHaicai
 * Date: 2016/11/26
 * Time: 22:13
 */
function readCsv($fileName, $isUtf8 = false)
{
    $header = array();
    $csv = array();
    $lineNum = 0;
    if (($handle = fopen($fileName, "r")) !== FALSE) {
        if ($isUtf8) {
            fread($handle, 3);
        }
        while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
            if ($lineNum == 0) {
                $header = $data;
                $csv[$lineNum] = $header;
            } else {
                $line = array();
                for ($i = 0; $i < count($data); ++$i) {
                    $line[$header[$i]] = $data[$i];
                }
                $csv[$lineNum] = $line;
            }
            ++$lineNum;
        }
    }
    fclose($handle);
    return $csv;
}

function toCsvStr($arr)
{
    $str = '"';
    if (count($arr) == 0) {
        return;
    }
    $header = $arr[0];
    $str .= implode('","', $header);
    $str .= "\"\n";
    for ($i = 1; $i < count($arr); ++$i) {
        $line = "";
        foreach ($header as $columnName) {
            $line .= '"' . $arr[$i][$columnName] . '",';
        }
        $line = substr($line, 0, strlen($line) - 1);
        $str .= $line . "\n";
    }
    return $str;
}

function getOnlySoundWords()
{
    $words = readCsv("data.csv", true);
    $index = 0;
    $ret = array();
    foreach ($words as $word) {
        if ($index) {
            if (strpos($word['content'], "(") === false && strpos($word['content'], "（") === false) {
                array_push($ret, $word);
            }
        }
        ++$index;
    }
    return $ret;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: "MS UI Gothic", "arial";
        }
    </style>
    <title></title>
</head>
<body>

<div style="margin-top: 30px;" class="col-sm-10 col-sm-offset-1">
    <table class="table table-bordered table-hover table-responsive ">
        <thead>
        <tr>
            <th>index</th>
            <th>mask</th>
            <th>tips</th>
            <th>lesson</th>
            <th>translation</th>
            <th>content</th>
            <th>answer</th>
        </tr>
        </thead>
        <tbody id="japTestBody"></tbody>
    </table>
</div>
<div class="col-sm-1 col-sm-offset-5 ">
    <button type="button" class="btn btn-info" id="bcn_check">检查</button>
</div>

</body>

<script type="text/javascript">

    <?php
    $onlySoundwords=getOnlySoundWords();
    echo "var tabJsonData = ".json_encode($onlySoundwords).";";
    ?>

    $('#lesson').keydown(function (e) {
            if (e.keyCode == 13) {
                getCSVData($('#lesson').val(), 'lesson');
            }
        }
    );
    $('#keyword').keydown(function (e) {
            if (e.keyCode == 13) {
                getCSVData($('#keyword').val(), 'keyword');
            }
        }
    );


    //check a word
    function checkSingleWord(i) {

        var mask = "mask_" + i;
        var content = "content_" + i;
        var answer = "answer_" + i;
        $("#" + content).text(tabJsonData[i]['content']);
        $("#" + answer).text(tabJsonData[i]['mask']);
        if ($("#" + mask).val() != tabJsonData[i]['mask']) {
            $("#japTestBody " + "#tr_" + i).removeClass('success');
            $("#japTestBody " + "#tr_" + i).addClass('danger');
        } else {
            $("#japTestBody " + "#tr_" + i).addClass('success')
            $("#japTestBody " + "#tr_" + i).removeClass('danger');
        }
    }
    $('#bcn_check').click(function () {
        var len = $('#japTestBody').children().length;
        for (var i = 0; i < len; ++i) {
            checkSingleWord(i);
        }
    })


    function init() {


        tabData = '';
        for (var i = 0; i < tabJsonData.length; i++) {
            tabData += '<tr id="tr_' + i + '">';
            tabData += '<td id="index_' + (i + 1) + '">' + (i + 1) + '</td>';
            tabData += '<td ><input class=\"grid-textbox\" id="mask_' + i + '\"/></td>';
            tabData += '<td id="tips_' + i + '">' + tabJsonData[i]['tips'] + '</td>';
            tabData += '<td id="translation_' + i + '">' + tabJsonData[i]['lesson'] + '</td>';
            tabData += '<td id="lesson_' + i + '">' + tabJsonData[i]['translation'] + '</td>';
            tabData += '<td id="content_' + i + '">' + '</td>';
            tabData += '<td id="answer_' + i + '">' + '</td>';
            tabData += '</tr>';
        }
        $('#japTestBody').html(tabData);


        $('.grid-textbox').keydown(function (e) {
            if (e.keyCode == 13) {
                if (e.ctrlKey) {
                    var id = $(this).attr("id");
                    var arr_id = id.split("_");
                    if (arr_id.length == 2) {
                        checkSingleWord(arr_id[1]);
                    }
                } else {
                    $(this).parent().parent().next().find('.grid-textbox').focus();
                }

            }
        });

    }
    init();
</script>

</html>
