<?php
/**
 * CVExtractor - topITworks Hackathon 2016
 *
 * @author: Thomas Nguyen
 * @email: resize2011@gmail.com
 * @updated_at: 19-09-2016 20:50
 */

require_once "libs/regexPattern.php";
require_once "libs/parseDoc.php";
require_once "libs/readdoc.php";

if(isset($argv)) {
    // Run by CLI
    $file = @$argv[1];

    if($file == '') {
        echo "---------------\nPlease using command \"php run.php file_csv.doc\"\n---------------\n";
    } else {

        $rW = new ReadWord();
        $rW->setFile($file);
        $rW->parse();
        $data = $rW->getCVData();

        echo "\n\n";
        echo " **************** CV Of ".strtoupper($data['profile']['fullname'])." ****************\n\n";

        foreach ($data as $label=>$content) {
            echo strtoupper($label).":\n-------------------------------\n";

            if($label == 'education' || $label == 'experiences') {
                foreach ($content as $k => $v) {
                    echo "  * {$v['from']}";
                    if($v['to'] != '') {
                        echo " - {$v['to']}";
                    }
                    echo " : {$v['place']}";
                    if(isset($v['desc'])) {
                        echo "\n\t" . implode("\n\t", $v['desc']);
                    }
                    echo "\n";
                }
            } else {

                if (is_array($content)) {
                    foreach ($content as $k => $v) {
                        echo "  {$k}:\t{$v}\n";
                    }
                } else {
                    echo $content;
                }
            }
            echo "\n\n";
        }

    }

} else {
    // Run by browser
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <meta HTTP-EQUIV="Expires" CONTENT="-1">
        <title>Parse CV Data</title>
        <style type="text/css">
            table td {
                border: 1px solid gray;
            }
        </style>
    </head>
    <body >
    <?php
    $error = '';
    $data = array();
    if(isset($_FILES['file'])) {
        if(is_file($_FILES['file']['tmp_name'])) {
            if($_FILES['file']['type'] == 'application/msword') {
                $rW = new ReadWord();
                $rW->setFile($_FILES['file']['tmp_name'], true);
                $rW->parse();
                $data = $rW->getCVData();
            } else {
                $error = 'Chỉ hỗ trợ định đang word97 (.doc), hay sử dụng các file .doc của "Nhiệm vụ đầu tiên"';
            }
        } else {
            $error = "Upload file thất bại, xin thử lại";
        }
    }
    ?>
    <?php if(count($data) > 0) { ?>

    <h3>Kết Quả Trích Rút Dữ Liệu</h3>
        <p><a href="?upload">Backup to upload</a></p>

        <?php
        if($_POST['show_type'] == 1) {

            echo "<pre>";
            var_dump($data);
            echo "</pre>";

        } else {
        ?>

        <table style="border-collapse: collapse; border:1px solid gray;" cellpadding="10">
            <tr>
                <td colspan=2 align="center">
                    <h1><?php echo strtoupper($data['profile']['fullname']);?></h1>
                </td>
            </tr>
            <?php
            foreach ($data as $label=>$content) {

                echo '<tr><td colspan=2>';
                echo "<b>".strtoupper($label)."</b>";
                echo '</td></tr>';

                if($label == 'education' || $label == 'experiences') {
                    foreach ($content as $k => $v) {
                        echo '<tr><td>Thời gian</td><td>';
                        echo "{$v['from']}";
                        if($v['to'] != '') {
                            echo " - {$v['to']}";
                        }
                        echo "</td>";
                        echo "</tr>";
                        echo "<tr><td>Tại</td>";
                        echo "<td>{$v['place']}</td>";
                        echo "</tr>";
                        if(isset($v['desc'])) {
                            echo "<tr><td>Mô tả</td><td>" . implode("<br>", $v['desc'])."</td></tr>";
                        }
                        echo "<tr><td colspan=2></td></tr>";
                    }
                } else {

                    if (is_array($content)) {
                        foreach ($content as $k => $v) {
                            echo "<tr><td width=150>{$k}</td><td>{$v}</td></tr>";
                        }
                    } else {
                        echo "<tr><td></td><td>".nl2br($content)."</td></tr>";
                    }
                    echo "<tr><td colspan=2></td></tr>";
                }
            }
            ?>
        </table>

    <?php } } else { ?>

    <p>
    <h3>Rút trích dữ liệu CV</h3>
    <p style="color:red"><?php echo $error; ?></p>
    Hỗ trợ định đang word97 (.doc), hay sử dụng các file .doc của "Nhiệm vụ đầu tiên"
    </p>
    <form method="POST" enctype="multipart/form-data" action="?rnd=<?php echo time(); ?>">
        <p>
            Chọn file: <input type="file" name="file">
            <p>Chọn cách hiễn thị dữ liệu: <label><input type="radio" name="show_type" value="0" checked> Dạng bảng</label> &nbsp; <label><input type="radio" name="show_type" value="1" > Dump array</label>
            </p>
            <p><input type="submit" value="Upload and parse now !" style="font-weight: bold;"></p>
        </p>
    </form>

    <?php } ?>

    </body>
    </html>
    <?php
}

?>