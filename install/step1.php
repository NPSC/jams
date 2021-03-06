<?php
/**
 * step1.php
 *
 * @category  Site
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */
require ("InstallIncludes.php");
require CLASSES . 'SiteConfig.php';

    function createMarkup(Config_Lite $config) {

        $hostName = '';

        $hostParts = explode(".", $_SERVER["SERVER_NAME"]);
        if (strtolower($hostParts[0]) == "www") {
            $hostParts[0] = "";
            $hostName = substr(implode(".", $hostParts), 1);
        } else {
            $hostName = $_SERVER["SERVER_NAME"];
        }

        $path = "";
        // find the path
        $parts = explode("/", $_SERVER["REQUEST_URI"]);

        $path = implode("/", array_slice($parts, 0, count($parts) - 2)) . '/';


        $tbl = new HTMLTable();
        $inputSize = '60';

        $tbl->addBodyTr(HTMLTable::makeTh("Site URL") . HTMLTable::makeTd(
                HTMLContainer::generateMarkup('span', "http://" . $hostName . $path, array('id'=>'spnSiteURL'))
                . HTMLInput::generateMarkup('Generate URLs', array('id'=>'btnGenURL', 'type'=>'button', 'data-host'=>$hostName, 'data-path'=>$path, 'style'=>'margin-left:5px;'))));

        foreach ($config as $section => $name) {

            $tbl->addBodyTr(HTMLTable::makeTh($section, array('colspan' => '2')));

            if (is_array($name)) {

                foreach ($name as $key => $val) {

                    $attr = array(
                        'name' => $section . '[' . $key . ']',
                        'id' => $section . $key
                    );

                    if ($section == 'code') {
                        $attr['readonly'] = 'Readonly';
                    }

                    if ($key == 'Disclaimer') {
                        $attr["rows"] = "3";
                        $attr["cols"] = "70";
                        $inpt = HTMLCONTAINER::generateMarkup('textarea', $val, $attr);
                    } else {
                        $attr['size'] = $inputSize;
                        $inpt = HTMLInput::generateMarkup($val, $attr);
                    }

                    $tbl->addBodyTr(
                            HTMLTable::makeTd($key, array('class' => 'tdlabel'))
                            . HTMLTable::makeTd($inpt)
                    );

                    unset($attr);
                }
            }
        }


        return $tbl->generateMarkup();
    }


// Get the site configuration object
$config = new Config_Lite(ciCFG_FILE);
$result = "";

if (isset($_POST['btnSave'])) {
    addslashesextended($_POST);
    try {
        SiteConfig::saveConfig($config, $_POST);
        $result = "Config file saved.  ";
    } catch (Exception $ex) {
        $result = $ex->getMessage();
    }
}



if (isset($_POST['btnNext'])) {
    header('location:step2.php');
}


$configuration = createMarkup($config);



?>
<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>HHK Installer</title>
        <script type="text/javascript" src="../<?php echo JQ_JS; ?>"></script>
        <script type="text/javascript">
    function testDb() {
        var parms = {
            cmd: 'testdb',
            dburl: document.getElementById('dbURL').value,
            dbuser: document.getElementById('dbUser').value,
            dbPW: document.getElementById('dbPassword').value,
            dbSchema: document.getElementById('dbSchema').value
        };
        $.post('ws_install.php',
            parms,
            function(data) {
                if (!data) {
                    alert('Bad Reply from Server');
                    return;
                }
                try {
                    data = $.parseJSON(data);
                } catch (err) {
                    alert('Bad JSON Encoding');
                    return;
                }

                if (data.error) {
                    $('#dbResult').text(data.error);
                } else if (data.success) {
                    $('#dbResult').text(data.success);
                }
            }
        );
    }
    $(document).ready(function() {
        "use strict";
        $.ajaxSetup({
            beforeSend: function() {
                $('body').css('cursor', "wait");
            },
            complete: function() {
                $('body').css('cursor', "auto");
            },
            cache: false
        });
        $('#btnTestDb').click(function () {testDb();});
        $('#btnGenURL').click(function () {
            var link = $('#spnSiteURL').text();
            $('#siteSite_URL').val(link);
            $('#siteVolunteer_URL').val(link + $('#siteVolunteer_Dir').val());
            $('#siteAdmin_URL').val(link + $('#siteAdmin_Dir').val());
            if ($('#siteHouse_Dir').val() != '') {
                $('#siteHouse_URL').val(link + $('#siteHouse_Dir').val());
            } else {
                $('#siteHouse_URL').val('');
            }
        });
    });
        </script>
        <style>
            .tblhdr {background-color: tomato}
            .tdtitle {width: 22%; text-align: right; margin-right:3px;}
        </style>
    </head>
    <body>
        <div id="page" style="width:900px;">
            <div class="topNavigation"></div>
            <div>
                <h2 class="logo">Hospitality HouseKeeper Installation Process

                </h2>
                <h3>Step One: Configuration File</h3>
            </div><div class='pageSpacer'></div>
            <div id="content" style="margin:10px; width:100%;">
                <span style="color:red;"><?php echo $result; ?></span>
                <form method="post" action="step1.php" name="form1" id="form1">
<?php echo $configuration ?>
                    <input type="button" id="btnTestDb" value="Test Db Connection" style="margin-left:5px;margin-top:20px;"/>
                    <span id="dbResult" style="color:darkgreen;"></span>
                    <input type="submit" name="btnSave" id="btnSave" value="Save" style="margin-left:700px;margin-top:20px;"/>
                    <input type="submit" name="btnNext" id="btnNext" value="Next" style="margin-left:7px;margin-top:20px;"/>
                </form>
            </div>
        </div>
    </body>
</html>

