<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class TechnicalAnswer extends Model
{
    protected $fillable = ['csr_id', 'customer', 'contact_person', 'slogan', 'problem_description', 'solution_description'];

    public function contact_person()
    {
        return $this->embedsOne(ContactPerson::class);
    }


}
