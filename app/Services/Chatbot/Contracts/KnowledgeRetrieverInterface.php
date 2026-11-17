<?php

namespace App\Services\Chatbot\Contracts;

use App\Services\Chatbot\Support\KnowledgeSnippet;
use Illuminate\Support\Collection;

/**
 * Finds knowledge relevant to a visitor question. The default implementation
 * is keyword/text search on the database; a semantic / vector-embedding
 * retriever can be bound to this interface later without touching providers.
 */
interface KnowledgeRetrieverInterface
{
    /**
     * @return Collection<int, KnowledgeSnippet> most relevant first
     */
    public function retrieve(string $query, ?string $language = null, int $limit = 4): Collection;
}
