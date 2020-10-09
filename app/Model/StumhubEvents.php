<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StumhubEvents extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tod_stubhub_events';

    public static function saveEvent($data) {
        return StumhubEvents::insert($data);
    }

}
