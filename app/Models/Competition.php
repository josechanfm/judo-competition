<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $fillable=['type_id','date_start','date_end','country','name_en','name_fn','scale','days','remark','mat_number','section_number','token','status'];

}
