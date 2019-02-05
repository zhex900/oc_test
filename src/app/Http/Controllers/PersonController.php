<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PersonController extends Controller
{

    public function search(Request $request)
    {
        $parameters = $request->query->all();

        //make the filter;
        $person_table = (new Person())->getTable();
        $filters = [];
        foreach ($parameters as $field => $search_value ){

            if(Schema::hasColumn($person_table, $field)) { // skip invalid parameters
                $filters[] = [$field,'LIKE','%'.$search_value.'%'];
            }
        }

        $people = Person::filter($filters)->get();

        return $people;
    }

    public function index()
    {
        return $this->search(new Request());
    }

}
