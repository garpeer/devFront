<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='hu-HU' lang="hu-HU">
    <head>
    <meta charset="utf-8" />
    <title><?php echo $this->title; ?></title>
    <link rel="shortcut icon" href="<?php echo $this->theme_dir;?>favicon.ico" />
    <link rel='stylesheet' type='text/css' href='<?php echo $this->theme_dir;?>style.css' />
    </head>
    <body>
        <h1><?php echo $this->title; ?></h1>
        <?php 
        echo $this->content;
        $from = "2012";
        $to = date('Y');
        if ($from == $to){
            $copy = $from;
        }else{
            $copy = $from . "–". $to;
        }
        ?>
        <p class="footer">© <?php echo $copy ?> Garpeer</p>
    </body>
</html>
 
