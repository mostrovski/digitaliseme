<?php

namespace Digitaliseme\Models;

use Digitaliseme\Core\Exceptions\DatabaseException;
use Digitaliseme\Core\ORM\Meta\ModelAttribute;
use Digitaliseme\Core\ORM\Model;

class Document extends Model
{
    #[ModelAttribute(protectedOnCreate: true, protectedOnUpdate: true)]
    public int $id;
    #[ModelAttribute]
    public string $title;
    #[ModelAttribute]
    public string $type;
    #[ModelAttribute]
    public string $issue_date;
    #[ModelAttribute]
    public ?int $issuer_id;
    #[ModelAttribute]
    public ?int $storage_id;
    #[ModelAttribute(protectedOnUpdate: true)]
    public int $user_id;

    /**
     * @throws DatabaseException
     */
    public function file(): ?File
    {
        return (new File)->query()
            ->where('document_id', '=', $this->id)
            ->first();
    }

    /**
     * @throws DatabaseException
     */
    public function issuer(): ?Issuer
    {
        return (new Issuer)->find($this->issuer_id);
    }

    /**
     * @throws DatabaseException
     */
    public function storage(): ?StoragePlace
    {
        return (new StoragePlace)->find($this->storage_id);
    }

    /**
     * @throws DatabaseException
     */
    public function keywords(): array
    {
        $pivotRecords = $this->pivot('document_keywords')
            ->select('keyword_id')
            ->where('document_id', '=', $this->id)
            ->get();

        if (empty($pivotRecords)) {
            return [];
        }

        $keywordIds = array_map(
            static fn (object $record) => $record->keyword_id,
            $pivotRecords,
        );

        return (new Keyword)->query()
            ->whereIn('id', $keywordIds)
            ->get();
    }
}
