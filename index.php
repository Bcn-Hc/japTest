<?php
require_once('func.php');

?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
    <script type="text/javascript" src="bootstrap/js/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        body {
            font-family: "MS UI Gothic", "arial";
        }
    </style>
</head>
<body>
<div style="margin-top: 50px;">
    <div class="col-sm-4  col-sm-offset-2 ">
        <div class="input-group input-group-sm">
            <input type="text" id="lesson" class="form-control"
                   placeholder="input the number of lesson,split by '|' or '-'">
            <a class="input-group-addon" href="#" id="begin_lesson">begin</a>
        </div>
        <div class="col-sm-offset-8">
            <input type="checkbox" id="random"/>
            <label for="random">Random</label>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="input-group input-group-sm">
            <input type="text" id="keyword" class="form-control" placeholder="input the keywords">
            <a class="input-group-addon" href="#" id="begin_keyword">begin</a>
        </div>
    </div>
</div>
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
</html>
<script type="text/javascript">
    var jsonData = [];
    document.getElementById("begin_lesson").addEventListener("click", function () {
        getCSVData($('#lesson').val(), 'lesson');
        return false;
    });

    document.getElementById("begin_keyword").addEventListener("click", function () {
        getCSVData($('#keyword').val(), 'keyword');
        return false;
    });

    function getCSVData(str_data, type) {
        switch (type) {
            case 'lesson':
            {
                var submitData = {
                    type: 'csv',
                    lesson: str_data,
                    action: 'search_lesson',
                    random: $('#random').is(':checked') ? "1" : 0
                };
                break;
            }
            case 'keyword':
            {
                var submitData = {
                    type: 'csv',
                    keyword: str_data,
                    action: 'search_keyword'
                };
                break;
            }
        }

        function successFunc(data, textStatus, jqXHR) {
            var htmlData = '';
            console.log(data);
            jsonData = JSON.parse(data);

            for (var i = 0; i < jsonData.length; i++) {
                htmlData += '<tr id="tr_' + i + '">';
                htmlData += '<td id="index_' + (i + 1) + '">' + (i + 1) + '</td>';
                htmlData += '<td ><input class=\"grid-textbox\" id="mask_' + i + '\"/></td>';
                htmlData += '<td id="tips_' + i + '">' + jsonData[i]['tips'] + '</td>';
                htmlData += '<td id="translation_' + i + '">' + jsonData[i]['lesson'] + '</td>';
                htmlData += '<td id="lesson_' + i + '">' + jsonData[i]['translation'] + '</td>';
                htmlData += '<td id="content_' + i + '">' + '</td>';
                htmlData += '<td id="answer_' + i + '">' + '</td>';
                htmlData += '</tr>';
            }
            $('#japTestBody').html(htmlData);

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

        $.ajax({
            url: "func.php",
            type: "post",
            data: submitData,
            success: successFunc,
            datatype: "json"
        });
        return false;
    }

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
        $("#" + content).text(jsonData[i]['content']);
        $("#" + answer).text(jsonData[i]['mask']);
        if ($("#" + mask).val() != jsonData[i]['mask']) {
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
</script>
