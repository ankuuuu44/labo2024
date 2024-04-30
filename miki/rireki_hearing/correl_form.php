<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<?php
  $radio = $_REQUEST["correl"];
  $_SESSION["correl_mode"] = $radio;
  ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Language" content="ja" />
        <title>フレーム（上下分割）</title>
    </head>
    <frameset rows="60%,*">
        <frame src="./correl.php" /><!-- フレーム上側に表示するファイルを指定 -->
        <frame src="./correl_value.php" /><!-- フレーム下側に表示するファイルを指定 -->
        <noframes>
            <body>
            <p>フレーム対応のブラウザでご覧下さい。</p>
            </body>
        </noframes>
    </frameset>
</html>