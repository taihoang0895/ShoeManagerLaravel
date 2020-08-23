<?php


namespace App\Http\Controllers\objects;


class TableCell
{
    function __construct($text, $colspan = 1, $width = 50, $height = 35)
    {
        $this->colspan = $colspan;
        $this->width = $this->colspan * $width;
        $this->height = $height;
        $this->text = $text;
        $this->width_str = strval($this->width) . 'px;';
        $this->height_str = strval($this->height) . 'px;';
    }


}
