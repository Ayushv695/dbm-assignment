<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait AttachmentUploadTrait{

    public function uploadAttachment($request, $input_name ,$path){
        $attachment_name = '';

        if($request->hasFile($input_name)){
            $attachment = $request->file($input_name);
            $extension = $attachment->extension();
            $attachment_name = uniqid().'.'.$extension;
            $attachment->move(public_path($path),$attachment_name);
        }

        return $attachment_name;
    }

    public function updateAttachment($request, $data, $input_name ,$path){  
        $attachment_name = $data->{$input_name};

        if($request->hasFile($input_name)){
                
            if($attachment_name && File::exists(public_path($path.$attachment_name))){
                File::delete(public_path($path.$attachment_name));
            }

            $attachment = $request->file($input_name);
            $extension = $attachment->extension();
            $attachment_name = uniqid().'.'.$extension;

            $attachment->move(public_path($path),$attachment_name);
        }

        return $attachment_name;
    }

    public function deleteAttachment($attachment_name , $path){  

        if($attachment_name && File::exists(public_path($path.$attachment_name))){
            File::delete(public_path($path.$attachment_name));
        }
    }
}