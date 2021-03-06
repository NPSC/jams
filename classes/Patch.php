<?php
/**
 * Patch.php
 *
 * Encapsulates common functionality for DB access
 *
 * @category  Configuration
 * @package   Hospitality HouseKeeper
 * @author    Eric K. Crane <ecrane@nonprofitsoftwarecorp.org>
 * @copyright 2010-2014 <nonprofitsoftwarecorp.org>
 * @license   GPL and MIT
 * @link      https://github.com/ecrane57/Hospitality-HouseKeeper
 */

/**
 * Description of Patch
 *
 * @author Eric
 */
class Patch {

    public static function loadFiles($fileRoot, $filePathName) {

        $result = "";

        $skipDirs = array('.git', 'thirdParty', 'install');

        self::deleteBakFiles($fileRoot, $skipDirs);

         // Detect guest tracking subunit
        if (is_dir($fileRoot . "house") === FALSE) {
            $skipDirs[] = 'house';
        }
        // Detect volunteer subunit
        if (is_dir($fileRoot . "volunteer") === FALSE) {
            $skipDirs[] = 'volunteer';
        }
        // Renames existing files to *.bak and copies in new versions.
        $result .= self::unzip($filePathName, $skipDirs);

        return $result;
    }

    public static function updateViewsSps($mysqli, $tfile, $vFile, $spFile) {

        $tquery = file_get_contents($tfile);
        $tresult = multiQuery($mysqli, $tquery);

        if ($tresult != '') {
            return 'Tables update failed.  ' . $tresult;
        }

        $vquery = file_get_contents($vFile);
        $result = multiQuery($mysqli, $vquery);

        if ($result == '') {
            $spquery = file_get_contents($spFile);
            $result .= multiQuery($mysqli, $spquery, '$$', '-- ;');
        }

        if ($result == '') {
            return "Views and SP's updated.  ";
        }

        return $result;
    }

    public static function loadConfigUpdates($configUpdateFile, Config_Lite $config) {

        if ($configUpdateFile == '') {
            return '';
        }

        $result = "";

        $cfupdates = new Config_Lite($configUpdateFile);

        foreach ($cfupdates as $secName => $secArray) {

            if ($secName != 'db' && $config->hasSection($secName)) {

                foreach ($secArray as $itemName => $val) {

                    $config->set($secName, $itemName, $val);
                    $result .= $secName . "." . $itemName . " = " . $val . "<br/>";

                }
            }
        }

        $config->save();
        return $result;
    }

    public static function deleteBakFiles($directory, array $skipDirs = array(), $oldExtension = 'bak') {

        $fit = new FilesystemIterator($directory, FilesystemIterator::UNIX_PATHS | FilesystemIterator::CURRENT_AS_FILEINFO);

        foreach ($fit as $fileinfo) {

            if ($fileinfo->isDir()) {

                // Not these files
                $flag = FALSE;
                foreach ($skipDirs as $d) {
                    if (stripos($fileinfo->getFilename(), $d) !== false) {
                        $flag = true;
                    }
                }

                if ($flag) {
                    continue;
                }

                self::deleteBakFiles($directory.$fileinfo->getFilename().DS);

            } else {

                if ($fileinfo->getExtension() == $oldExtension) {
                    //echo $fileinfo->getRealPath() . "<br/>";
                    unlink($fileinfo->getRealPath());
                }
            }
        }
    }

    protected static function unzip($file, array $skipDirs, $oldExtension = 'bak', $rootDir = 'hhk') {

        $result = '';
        $zip = zip_open($file);

        if (is_resource($zip)) {

            $colCounter = 0;
            $table = new HTMLTable();
            $tr = "";

            while (($entry = zip_read($zip)) !== false) {


                if (strpos(zip_entry_name($entry), "/") !== false) {

                    $last = strrpos(zip_entry_name($entry), "/");
                    $dir = substr(zip_entry_name($entry), 0, $last);
                    $file = substr(zip_entry_name($entry), strrpos(zip_entry_name($entry), "/") + 1);

                    // Files inside this directory
                    if ($dir == $rootDir) {
                        continue;
                    }

                    // Not these files
                    $flag = FALSE;
                    foreach ($skipDirs as $d) {
                        if (stripos($dir, $d) !== false) {
                            $flag = true;
                        }
                    }

                    if ($flag) {
                        continue;
                    }

                    $relDir = str_ireplace($rootDir, '..', $dir);

                    if (strlen(trim($file)) > 0) {

                        // rename the existing file
                        if (file_exists($relDir . "/" . $file)) {
                            $renamedFile = $relDir . "/" . $file . '.' . $oldExtension;
                            rename($relDir . "/" . $file, $renamedFile);
                        }

                        // copy the new version in
                        $fileSize = file_put_contents($relDir . "/" . $file, zip_entry_read($entry, zip_entry_filesize($entry)));

                        if ($fileSize === false) {
                            throw new Hk_Exception_Runtime("Unable to write file: $relDir/$file");
                        }

                        if ($colCounter >= 2) {
                            $table->addBodyTr($tr);
                            $colCounter = 0;
                            $tr = '';
                        }

                        $tr .= HTMLTable::makeTd($relDir . "/" . $file);
                        $colCounter++;
                    }
                }
            }

            if ($tr != '') {
                $table->addBodyTr($tr);
            }

            $result = $table->generateMarkup();

        } else {
            throw new Hk_Exception_Runtime("Unable to open zip file.  ");
        }

        return $result;
    }

}

?>
