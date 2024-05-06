<?php

namespace wishanddell\userlog\models;

use craft\base\Model;

class Settings extends Model
{
    public $ipService = 'disabled';
    public $ipstackKey = '';
    public $days = 21;

    public function rules(): array
    {
        return [];
    }
}