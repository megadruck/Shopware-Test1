<?php


class UploadHandler
{
    /**
     * @var array
     */
    public $allowedExtensions = array();
    /**
     * @var int|null|string
     */
    public $sizeLimit = NULL;
    /**
     * @var string
     */
    public $inputName = "qqfile";
    /**
     * @var string
     */
    public $chunksFolder = "chunks";
    /**
     * @var float
     */
    public $chunksCleanupProbability = 0.001;
    /**
     * @var int
     */
    public $chunksExpireIn = 604800;
    /**
     * @var string
     */
    protected $uploadName = NULL;
    /**
     * @var string
     */
    protected $uploadSize = NULL;

    /**
     * Liefert das sizeLimit für den Datenupload zurück
     * @var string
     */

    public function __construct()
    {
        $this->sizeLimit = $this->toBytes(ini_get("upload_max_filesize"));
    }

    /**
     * function clear_string
     * @param $str
     * @return mixed|string
     */

    public function clear_string($str)
    {
        $str = strtr($str, array( " " => "-", "(" => "-", ")" => "-", "'" => "-", "’" => "-", "ß" => "ss" ));
        $str = preg_replace("#[^a-zA-Z0-9\\-\\._]#", "", utf8_decode($str));
        return $str;
    }

    /**
     * Get the original filename
     */

    public function getName($request)
    {
        $qqfilename = $request->getParam("qqfilename");
        if( isset($qqfilename) )
        {
            return $this->clear_string($qqfilename);
        }

        if( isset($_FILES[$this->inputName]) )
        {
            return $this->clear_string($_FILES[$this->inputName]["name"]);
        }

    }

    /**
     * Get the name of the uploaded file
     */

    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * function getUploadSize
     * @return mixed
     */

    public function getUploadSize()
    {
        return $this->uploadSize;
    }

    /**
     * Process the upload.
     * @param string $uploadDirectory Target directory.
     * @param string $name Overwrites the name of the file.
     */

    public function handleUpload($uploadDirectory, $request, $name = NULL)
    {
        if( is_writable($this->chunksFolder) && 1 == mt_rand(1, 1 / $this->chunksCleanupProbability) )
        {
            $this->cleanupChunks();
        }

        if( $this->toBytes(ini_get("post_max_size")) < $this->sizeLimit || $this->toBytes(ini_get("upload_max_filesize")) < $this->sizeLimit )
        {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . "M";
            return array( "error" => "Server error. Increase post_max_size and upload_max_filesize to " . $size );
        }

        if( $this->isInaccessible($uploadDirectory) )
        {
            return array( "error" => "Server error. Uploads directory isn't writable" );
        }

        if( !isset($_SERVER["CONTENT_TYPE"]) )
        {
            return array( "error" => "No files were uploaded." );
        }

        if( strpos(strtolower($_SERVER["CONTENT_TYPE"]), "multipart/") !== 0 )
        {
            return array( "error" => "Server error. Not a multipart request. Please set forceMultipart to default value (true)." );
        }

        $file = $_FILES[$this->inputName];
        $size = $file["size"];
        $this->uploadSize = $size;
        if( $name === NULL )
        {
            $name = $this->getName($request);
        }

        if( $name === NULL || $name === "" )
        {
            return array( "error" => "File name empty." );
        }

        if( $size == 0 )
        {
            return array( "error" => "File is empty." );
        }

        if( $this->sizeLimit < $size )
        {
            return array( "error" => "File is too large." );
        }

        $pathinfo = pathinfo($name);
        $ext = (isset($pathinfo["extension"]) ? $pathinfo["extension"] : "");
        if( $this->allowedExtensions && !in_array(strtolower($ext), array_map("strtolower", $this->allowedExtensions)) )
        {
            $these = implode(", ", $this->allowedExtensions);
            return array( "error" => "File has an invalid extension, it should be one of " . $these . "." );
        }

        $qqTotalParts = $request->getParam("qqtotalparts");
        $totalParts = (isset($qqTotalParts) ? (int) $request->getParam("qqtotalparts") : 1);
        $uuid = $request->getParam("qquuid");
        if( 1 < $totalParts )
        {
            $chunksFolder = $this->chunksFolder;
            $partIndex = (int) $request->getParam("qqpartindex");
            if( !is_writable($chunksFolder) && !is_executable($uploadDirectory) )
            {
                return array( "error" => "Server error. Chunks directory isn't writable or executable." );
            }

            $targetFolder = $this->chunksFolder . DIRECTORY_SEPARATOR . $uuid;
            if( !file_exists($targetFolder) )
            {
                mkdir($targetFolder);
            }

            $target = $targetFolder . "/" . $partIndex;
            $success = move_uploaded_file($file["tmp_name"], $target);
            if( $success && $totalParts - 1 == $partIndex )
            {
                $target = join(DIRECTORY_SEPARATOR, array( $uploadDirectory, $name ));
                $this->uploadName = $uuid . DIRECTORY_SEPARATOR . $name;
                if( !file_exists($target) )
                {
                    mkdir(dirname($target));
                }

                $target = fopen($target, "wb");
                for( $i = 0; $i < $totalParts; $i++ )
                {
                    $chunk = fopen($targetFolder . DIRECTORY_SEPARATOR . $i, "rb");
                    stream_copy_to_stream($chunk, $target);
                    fclose($chunk);
                }
                fclose($target);
                for( $i = 0; $i < $totalParts; $i++ )
                {
                    unlink($targetFolder . DIRECTORY_SEPARATOR . $i);
                }
                rmdir($targetFolder);
                return array( "success" => true, "uuid" => $uuid );
            }

            return array( "success" => true, "uuid" => $uuid );
        }

        $target = join(DIRECTORY_SEPARATOR, array( $uploadDirectory, $name ));
        if( $target )
        {
            $this->uploadName = basename($target);
            if( !is_dir(dirname($target)) )
            {
                mkdir(dirname($target));
            }

            if( move_uploaded_file($file["tmp_name"], $target) )
            {
                return array( "success" => true, "uuid" => $uuid );
            }

        }

        return array( "error" => "Could not save uploaded file." . "The upload was cancelled, or server error encountered" );
    }

    /**
     * Process a delete.
     * @param string $uploadDirectory Target directory.
     * @params string $name Overwrites the name of the file.
     *
     */

    public function handleDelete($uploadDirectory, $request)
    {
        $targetFolder = $uploadDirectory;
        if( is_file($targetFolder) )
        {
            unlink($targetFolder);
            return array( "success" => true );
        }

        return array( "success" => false, "error" => "File not found! Unable to delete.", "path" => $targetFolder );
    }

    /**
     * Returns a path to use with this upload. Check that the name does not exist,
     * and appends a suffix otherwise.
     * @param string $uploadDirectory Target directory
     * @param string $filename The name of the file to use.
     */

    protected function getUniqueTargetPath($uploadDirectory, $filename)
    {
        if( function_exists("sem_acquire") )
        {
            $lock = sem_get(ftok(__FILE__, "u"));
            sem_acquire($lock);
        }

        $pathinfo = pathinfo($filename);
        $base = $pathinfo["filename"];
        $ext = (isset($pathinfo["extension"]) ? $pathinfo["extension"] : "");
        $ext = ($ext == "" ? $ext : "." . $ext);
        $unique = $base;
        $suffix = 0;
        while( file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext) )
        {
            $suffix += rand(1, 999);
            $unique = $base . "-" . $suffix;
        }
        $result = $uploadDirectory . DIRECTORY_SEPARATOR . $unique . $ext;
        if( !touch($result) )
        {
            $result = false;
        }

        if( function_exists("sem_acquire") )
        {
            sem_release($lock);
        }

        return $result;
    }

    /**
     * Deletes all file parts in the chunks folder for files uploaded
     * more than chunksExpireIn seconds ago
     */

    protected function cleanupChunks()
    {
        foreach( scandir($this->chunksFolder) as $item )
        {
            if( $item == "." || $item == ".." )
            {
                continue;
            }

            $path = $this->chunksFolder . DIRECTORY_SEPARATOR . $item;
            if( !is_dir($path) )
            {
                continue;
            }

            if( $this->chunksExpireIn < time() - filemtime($path) )
            {
                $this->removeDir($path);
            }

        }
    }

    /**
     * Removes a directory and all files contained inside
     * @param string $dir
     */

    protected function removeDir($dir)
    {
        foreach( scandir($dir) as $item )
        {
            if( $item == "." || $item == ".." )
            {
                continue;
            }

            if( is_dir($item) )
            {
                removeDir($item);
            }
            else
            {
                unlink(join(DIRECTORY_SEPARATOR, array( $dir, $item )));
            }

        }
        rmdir($dir);
    }

    /**
     * Converts a given size with units to bytes.
     * @param string $str
     */

    protected function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch( $last )
        {
            case "g":
                $val *= 1024;
            case "m":
                $val *= 1024;
            case "k":
                $val *= 1024;
        }
        return $val;
    }

    /**
     * Determines whether a directory can be accessed.
     *
     * is_writable() is not reliable on Windows
     *  (http://www.php.net/manual/en/function.is-executable.php#111146)
     * The following tests if the current OS is Windows and if so, merely
     * checks if the folder is writable;
     * otherwise, it checks additionally for executable status (like before).
     *
     * @param string $directory The target directory to test access
     */

    protected function isInaccessible($directory)
    {
        $isWin = strtoupper(substr(PHP_OS, 0, 3)) === "WIN";
        $folderInaccessible = ($isWin ? !is_writable($directory) : !is_writable($directory) && !is_executable($directory));
        return $folderInaccessible;
    }

}