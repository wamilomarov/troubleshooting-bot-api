<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TechnicalAnswerResource;
use App\Models\ContactPerson;
use App\Models\TechnicalAnswer;
use Illuminate\Http\Request;
use Jenssegers\Mongodb\Eloquent\Builder;

class TechnicalAnswerController extends Controller
{
    public function rearrange()
    {
        foreach (TechnicalAnswer::all()->chunk(200) as $technicalAnswers) {
            foreach ($technicalAnswers as $technicalAnswer) {
                $contactPerson = new ContactPerson([
                    'name' => $technicalAnswer->contact_person_name,
                    'username' => $technicalAnswer->getAttribute('contact_person'),
                    'email' => $technicalAnswer->contact_person_email,
                ]);

                $technicalAnswer->contact_person_account()->save($contactPerson);
            }
        }
        return TechnicalAnswer::query()->paginate();
    }

    public function search(Request $request)
    {
        $technicalAnswers = TechnicalAnswer::query()
            ->when($request->filled('q'), function (Builder $query) use ($request){
                return $query->whereRaw(['$or' => [[
                    'slogan' => ['$regex' => $request->get('q'), '$options' => 'i'],
                    'problem_description' => ['$regex' => $request->get('q'), '$options' => 'i'],
                    'solution_description' => ['$regex' => $request->get('q'), '$options' => 'i'],
                ]]]);
            })
            ->paginate();

        return TechnicalAnswerResource::collection($technicalAnswers);
    }
}
