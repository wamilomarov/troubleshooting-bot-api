<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Resources\TechnicalAnswerResource;
use App\Models\ContactPerson;
use App\Models\TechnicalAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    public function check(Request $request)
    {
        $rules = [
            'csr_id' => 'required|unique:technical_answers'
        ];

        $request->validate($rules);

        $csr = DB::connection('mysql')->table('CSR_Notes')
            ->where('PK_CSR_ID', $request->get('csr_id'))
            ->first();

        if ($csr) {
            var_dump($csr);
        } else {
            throw ValidationException::withMessages([
                'csr_id' => [trans("validation.no_such_csr")]
            ]);
        }

    }

    public function search(Request $request)
    {
        $pageSize = 15;
        if ($request->filled('page_size') && is_numeric($request->get('page_size'))) {
            $pageSize = (int)$request->get('page_size');
        }
        $page = (int)$request->get('page', 0);
        $aggregate = [];

        $paginationAggregate = [ ['$skip' => ($page*$pageSize)], ['$limit' => $pageSize]];
        $countAggregate = [['$group' => ['_id' => null, 'total' => ['$sum' => 1]]]];

        if ($request->filled('q'))
        {
            $aggregate[] = ['$searchBeta' => [
                'index' => "technical_answers_fts",
                'search' => [
                    'query' => $request->get('q'),
                    'path' => ['slogan', 'solution_description', 'problem_description'],
                ]
            ]];
        }

        $technicalAnswers = TechnicalAnswer::query()->raw()
            ->aggregate(array_merge($aggregate, $paginationAggregate));

        $pagination = TechnicalAnswer::query()->raw()
            ->aggregate(array_merge($aggregate, $countAggregate));

        $totalCount = iterator_to_array($pagination)[0]->total;
        $maxPages = round($totalCount/$pageSize);
        $nextPage = $page < $maxPages ? $page + 1 : null;

        $links = [
            'first' => $request->fullUrlWithQuery(['page' => 0, 'page_size' => $pageSize, 'q' => $request->get('q')]),
            'last' => $request->fullUrlWithQuery(['page' => $maxPages, 'page_size' => $pageSize, 'q' => $request->get('q')]),
            'next' => !is_null($nextPage) ? $request->fullUrlWithQuery(['page' => $nextPage, 'page_size' => $pageSize, 'q' => $request->get('q')]) : null,
            'prev' => $page > 0 ? $request->fullUrlWithQuery(['page' => ($page - 1), 'page_size' => $pageSize, 'q' => $request->get('q')]) : null,
        ];

        $meta = [
            'total' => $totalCount,
            'per_page' => $pageSize,
            'current_page' => $page
        ];

        return response()->json([
            'data' => collect(iterator_to_array($technicalAnswers)),
            'links' => $links,
            'meta' => $meta], 200);

    }

    public function get(TechnicalAnswer $technical_answer)
    {
        return TechnicalAnswerResource::make($technical_answer);
    }
}
