<!doctype html>
<html>
<head>
    <title>Connect 4 - The Game</title>
    <style type="text/css">
        p{ margin-top:40px; }
        p.message{font-size:1.5em; text-transform:uppercase; }
        table { border-left:1px solid #444; border-top:1px solid #444; border-collapse:collapse; }
        table td{
            font-family:arial;
            font-size:12px;
            width:20px;
            height:20px;
            vertical-align:middle;
            text-align:center;
            border-right:1px solid #444;
            border-bottom:1px solid #444;
            text-indent:-9999px;
        }
        table td.player-1{background:#f39000; }
        table td.player-2{background:#003366; }
    </style>
</head>

<body>
<p><img src="https://www.leaseweb.com/sites/all/themes/leaseweb/logo.svg" alt="logo" /></p>
<h2>Connect 4</h2>
Player 1 - Orange <br/>
Player 2 - Blue<br/>

<div>
    <form action='index.php' method="post">
        <input type="submit" name="next_step" value="Next Step" id="btn_next_step"/>
    </form>
    <label id="lbl_next_step">
</div>

<?php
//Load our file
require 'ConnectFour.php';
if(isset($_POST['next_step']) || isset($_POST['collomnr']))
{
    $new_game = new ConnectFour();
}

//Load our file
// require 'ConnectFour.php';
//Instantiate our game
// $new_game = new ConnectFour();
?>

</body>
</html>

