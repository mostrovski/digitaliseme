<?php

namespace Digitaliseme\DataEntities;

use Digitaliseme\Exceptions\KeywordException;
use Digitaliseme\Models\Keyword;

final class Keywords
{
    protected const WORD_MIN_LENGTH = 3;
    /**
     * @var array<int,string>
     */
    protected array $keywords = [];

    /**
     * @param array<int,Keyword> $source
     */
    public static function fromModelArray(array $source): self
    {
        $instance = new self;

        if (empty($source)) {
            return $instance;
        }

        $instance->keywords = array_map(
            static fn (Keyword $keyword) => $keyword->word,
            $source,
        );

        return $instance;
    }

    /**
     * @throws KeywordException
     */
    public static function fromString(string $source): self
    {
        $instance = new self;

        if (empty($source)) {
            return $instance;
        }

        $parts = explode(',', $source);

        if (count($parts) === 1) {
            $parts = explode(' ', $source);
        }

        $keywords = [];

        foreach ($parts as $word) {
            $word = preg_replace('/\s+/', '-', trim($word));

            if (strlen($word) < self::WORD_MIN_LENGTH) {
                throw new KeywordException('Keyword must be at least '.self::WORD_MIN_LENGTH.' characters long');
            }

            $keywords[] = $word;
        }

        $instance->keywords = $keywords;

        return $instance;
    }

    /**
     * @return array<int,string>
     */
    public function all(): array
    {
        return $this->keywords;
    }

    public function toString(): string
    {
        return implode(', ', $this->keywords);
    }
}
