<?php
//User Model
use Illuminate\Database\Eloquent\Model as Eloquent;
	
class Articles extends Eloquent {
	protected $fillable = array('name', 'id');
    //
}

