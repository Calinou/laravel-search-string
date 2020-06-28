<?php

namespace Lorisleiva\LaravelSearchString\Visitors;

use Lorisleiva\LaravelSearchString\AST\ListSymbol;
use Lorisleiva\LaravelSearchString\AST\QuerySymbol;
use Lorisleiva\LaravelSearchString\AST\RelationshipSymbol;
use Lorisleiva\LaravelSearchString\AST\SearchSymbol;
use Lorisleiva\LaravelSearchString\AST\SoloSymbol;
use Lorisleiva\LaravelSearchString\AST\Symbol;
use Lorisleiva\LaravelSearchString\SearchStringManager;

class AttachRulesVisitor extends Visitor
{
    /** @var SearchStringManager */
    protected $manager;

    public function __construct(SearchStringManager $manager)
    {
        $this->manager = $manager;
    }

    public function visitRelationship(RelationshipSymbol $relationship)
    {
        return $this->attachRuleFromColumns($relationship, $relationship->key);
    }

    public function visitSolo(SoloSymbol $solo)
    {
        return $this->attachRuleFromColumns($solo, $solo->content);
    }

    public function visitSearch(SearchSymbol $search)
    {
        return $this->attachRuleFromColumns($search, $search->content);
    }

    public function visitQuery(QuerySymbol $query)
    {
        return $this->attachRuleFromKeywordsOrColumns($query, $query->key);
    }

    public function visitList(ListSymbol $list)
    {
        return $this->attachRuleFromKeywordsOrColumns($list, $list->key);
    }

    protected function attachRuleFromKeywordsOrColumns(Symbol $symbol, string $key)
    {
        if ($rule = $this->manager->getRule($key)) {
            return $symbol->attachRule($rule);
        }

        return $symbol;
    }

    protected function attachRuleFromColumns(Symbol $symbol, string $key)
    {
        if ($rule = $this->manager->getColumnRule($key)) {
            return $symbol->attachRule($rule);
        }

        return $symbol;
    }
}