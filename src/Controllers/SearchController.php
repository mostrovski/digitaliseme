<?php

namespace Digitaliseme\Controllers;

use Digitaliseme\Core\Database\DB;
use Digitaliseme\Core\Database\MySQL;
use Digitaliseme\Core\Database\Query;
use Digitaliseme\Core\Exceptions\DatabaseException;
use Digitaliseme\Core\Exceptions\ValidatorException;
use Digitaliseme\Core\Http\Response;
use Digitaliseme\DataEntities\Keywords;
use Digitaliseme\Enumerations\DocumentType;
use Digitaliseme\Exceptions\KeywordException;
use Digitaliseme\Models\Document;
use Digitaliseme\Models\Issuer;
use Digitaliseme\Models\Keyword;
use Digitaliseme\Models\StoragePlace;

class SearchController extends Controller
{
    public function index(): Response
    {
        $results = $_SESSION['redirect-data'] ?? [];
        unset($_SESSION['redirect-data']);

        return viewResponse('search/index', ['results' => $results]);
    }

    /**
     * @throws ValidatorException
     * @throws DatabaseException
     */
    public function find(): Response
    {
        if (! $this->isPostRequest() ||
            ! $this->hasValidToken()
        ) {
            return redirectResponse('404');
        }

        $validator = $this->validate($_POST, $this->validationRules(), $this->validationMessages());

        if ($validator->fails()) {
            return $this->withErrors($validator->getErrors())->redirect('search');
        }

        try {
            $results = $this->searchResults($validator->getValidated());
        } catch (KeywordException $e) {
            return $this->withErrors(['keywords' => [$e->getMessage()]])->redirect('search');
        }

        if (empty($results)) {
            flash()->warning('There are no documents matching your search criteria');
        }

        return redirectResponse('search', $results);
    }

    /**
     * @throws DatabaseException
     * @throws KeywordException
     */
    protected function searchResults(array $params): array
    {
        $query = Document::go()->query()->unsetFetchClass();

        if (! empty($params['title'])) {
            $query->where('title', 'LIKE', "%{$params['title']}%");
        }

        if (! empty($params['type'])) {
            $query->where('type', '=', $params['type']);
        }

        if (! empty($params['issue_date'])) {
            $query->where('issue_date', '=', $params['issue_date']);
        }

        if (! empty($params['issuer_name'])) {
            $issuers = Issuer::go()->query()
                ->select('id')
                ->where('name', 'LIKE', "%{$params['issuer_name']}%")
                ->get();
            if (empty($issuers)) {
                return [];
            }
            $issuerIds = array_map(static fn(Issuer $issuer) => $issuer->id, $issuers);

            $query->whereIn('issuer_id', $issuerIds);
        }

        if (! empty($params['storage'])) {
            $places = StoragePlace::go()->query()
                ->select('id')
                ->where('place', 'LIKE', "%{$params['storage']}%")
                ->get();
            if (empty($places)) {
                return [];
            }
            $placeIds = array_map(static fn(StoragePlace $place) => $place->id, $places);

            $query->whereIn('storage_id', $placeIds);
        }

        if (! empty($params['keywords'])) {
            $keywords = Keywords::fromString($params['keywords'])->all();

            if (! empty($keywords)) {
                $records = Keyword::go()->query()
                    ->select('id')
                    ->whereIn('word', $keywords)
                    ->get();
                if (empty($records)) {
                    return [];
                }
                $keywordIds = array_map(static fn(Keyword $keyword) => $keyword->id, $records);
                $documentIds = DB::wire(MySQL::connect(), new Query)->table('document_keywords')
                    ->select('document_id')
                    ->whereIn('keyword_id', $keywordIds)
                    ->get();
                if (empty($documentIds)) {
                    return [];
                }

                $query->whereIn('id', array_map(static fn($record) => $record->document_id, $documentIds));
            }
        }

        return $query->get();
    }

    protected function validationRules(): array
    {
        return [
            'title' => ['nullable', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9()*,.\s-]+$/'],
            'type' => ['nullable', 'in:'.implode(',', DocumentType::values())],
            'issue_date' => ['nullable', 'date:Y-m-d'],
            'issuer_name' => ['nullable', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9.\s-]+$/'],
            'storage' => ['nullable', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9,()\s-]+$/'],
            'keywords' => ['nullable', 'regex:/^[a-zA-ZäöüßÄÖÜ0-9,\s-]+$/'],
        ];
    }

    protected function validationMessages(): array
    {
        return [
            'type.in' => 'Document type is invalid',
            'issue_date.date' => 'Issue date is invalid',
            'title.regex' => 'Only alphabetic and numeric symbols, spaces, round brackets, asterisks, points, and hyphens are allowed',
            'issuer_name.regex' => 'Only alphabetic and numeric symbols, points, spaces, and hyphens are allowed',
            'storage.regex' => 'Only alphabetic and numeric symbols, commas, round brackets, spaces, and hyphens are allowed',
            'keywords.regex' => 'Only alphabetic and numeric symbols, commas, spaces, and hyphens are allowed',
        ];
    }
}
