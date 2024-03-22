<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bout extends Model
{
    use HasFactory;
    protected $appends=['white_player','blue_player'];

    public function program(){
        return $this->belongsTo(Program::class);
    }

    public function getWhitePlayerAttribute(){
        if($this->white==0){
            //return Athlete::make(["name_display"=>"ss"]);
            return (object)["name_display"=>""];
        }else{
            return  Athlete::find($this->white);
        };
    }
    public function getBluePlayerAttribute(){
        if($this->blue==0){
            return (object)["name_display"=>""];
        }else{
            return  Athlete::find($this->blue);
        };
    }
}



