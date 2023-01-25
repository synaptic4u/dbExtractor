<?php

namespace Synaptic4u\Crawler;

use Synaptic4u\Logs\Activity;
use Synaptic4u\Logs\Error;
use Synaptic4u\Logs\Log;

/**
 * Class::Crawler :
 * Crawls recursively through a directory structure, produces lists of files.
 */
class Crawler
{
    private $file_match_case;

    public function __construct($file_match_case)
    {
        $this->file_match_case = $file_match_case;
    }

    /**
     * crawl: Recursively cylces through directory building a file list from a case sensitive parameter.
     *
     * @param string $path
     * @param array $tree
     * @return array
     */
    public function crawl(string $path, array $tree)
    {
        $cnt = 0;
        if (is_dir($path)) {

            if ($dh = opendir($path)) {
            
                while (($file = readdir($dh)) !== false) {
            
                    if ('.' !== $file) {
                        
                        if ('..' !== $file) {

                            $newfile = $path.$file;
                            $newpath = $newfile.'/';

                            $path_clean = str_replace("-","_",$path);
                            $file_clean = str_replace("-","_",$file);

                            // var_dump([
                            //     "file" => $file,
                            //     "path" => $path,
                            //     "newfile" => $newfile,
                            //     "path_clean" => $path_clean,
                            //     "file_clean" => $file_clean,
                            //     "filematch" => $this->file_match_case,
                            //     "substring" => substr($newfile, strripos($newfile, '.')+1),
                            //     "inarray" => (in_array(substr($newfile, strripos($newfile, '.')+1), $this->file_match_case))  ? "true":"false"
                            // ]);

                            if (is_dir($newpath)) {

                                $tree[$path_clean][$file_clean] = $this->crawl($newpath, []);
                            } else {

                                if (in_array(substr($newfile, strripos($newfile, '.')+1), $this->file_match_case)) {
                                    
                                    $tree[$path_clean][$file_clean] = $newfile;
                                }
                            }
                        }
                    }
                    ++$cnt;
                }
                closedir($dh);
            }
        }

        return $tree;
    }

    /**
     * Method flattens a multi-dimensional array into a associative single dimensional array.
     *
     * @param array $array : Associative multi-dimensional array
     *
     * @return array : Single dimensional associative array
     */
    public function flattenArray(array $array)
    {
        $flat_array = [];
        array_walk_recursive($array, function ($value, $key) use (&$flat_array) {
            $flat_array[$key] = $value;
        });

        return $flat_array;
    }

    /**
     * Error logging.
     *
     * @param array $msg : Error message
     */
    protected function error($msg)
    {
        new Log($msg, new Error());
    }

    /**
     * Activity logging.
     *
     * @param array $msg : Message
     */
    protected function log($msg)
    {
        new Log($msg, new Activity());
    }
}