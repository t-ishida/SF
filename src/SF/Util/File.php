<?php
namespace SF\Util;

class File
{
    private $fp = null;
    private $path = null;

    public function __construct($filePath, $mode = 'r')
    {
        if (!is_file($filePath) && $mode === 'r') {
            throw new \RuntimeException($filePath . ' is not file');
        }
        $this->path = $filePath;
        $this->fp = fopen($this->path, $mode);
    }

    /**
     * @return null
     */
    public function getPath()
    {
        return $this->path;
    }

    public function clear()
    {
        ftruncate($this->fp, 0);
        $this->reset();
    }

    public function reset()
    {
        fseek($this->fp, 0);
    }

    public function writeAll($string)
    {
        $this->clear();
        $this->write("$string\n");
    }

    public function writeLine($ln)
    {
        $this->write("$ln\n");
    }

    public function write($string)
    {
        fwrite($this->fp, $string);
    }

    public function exists()
    {
        is_file($this->path);
    }

    public function read($size = 65535)
    {
        return fread($this->fp, $size);
    }

    public function gets($size = 65535)
    {
        return fgets($this->fp, $size);
    }

    public function readAll()
    {
        $content = '';
        $this->reset();
        while ($ln = $this->read()) {
            $content .= $ln;
        }
        return $content;
    }


    public function close ()
    {
        if ($this->fp) {
            fclose($this->fp);
            $this->fp = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }
}