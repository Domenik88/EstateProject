<?php
/**
 * Created by TutMee Co.
 * User: Domenik88(kataevevgenii@gmail.com)
 * Date: 28.09.2020
 *
 * @package estateblock20
 */

namespace App\Service;


class RemoveDirService
{
    public function dirDel($dir)
    {
        $d=opendir($dir);
        while(($entry=readdir($d))!==false)
        {
            if ($entry != "." && $entry != "..")
            {
                if (is_dir($dir."/".$entry))
                {
                    $this->dirDel($dir."/".$entry);
                }
                else
                {
                    unlink ($dir."/".$entry);
                }
            }
        }
        closedir($d);
        rmdir ($dir);
    }
}