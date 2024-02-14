<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    const httpPath = "192.168.0.102/cdn";
    const allowedPaths = array('product_catalog',
                          'quotations',
                          'images',
                          'product_photometry');
    public function get($local) 
    {
        $cdn_dir = '/var/www/html/cdn/';
        if(!in_array($local,$this::allowedPaths))
        return 'Not allowed';
        foreach(array_diff(scandir($cdn_dir.$local), array('.', '..')) as $files) 
        {
            $fileDate =  date ("Y-m-d H:i:s", filemtime($cdn_dir.'/'.$local.'/'.$files));
            $json_Array[] = array('name'=>$files,'id'=>$this::httpPath.'/'.$local.'/'.$files,'date'=>$fileDate);

        }
        return json_encode($json_Array);
    }
}