<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class StumhubSellModel extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tod_stubhub_sells';

    public static function saveEvent($data) {
        return StumhubSellModel::insert($data);
    }

}
