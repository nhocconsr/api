<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarCat extends Model {

    var $table = 'engr_types';
    public $timestamps = false;
    /**
     * The videos that belong to the playlist.
     */
    public function questions() {
        return $this->belongsToMany('App\Models\GrammarQuestion', 'engr_types_questions','type_id','question_id');
    }

    public function lessons() {
        return $this->belongsToMany('App\Models\GrammarLesson', 'engr_types_articles',  'the_loai', 'truyen_ngan')->where("published",1);
    }  
}
