<?php

namespace Makeable\LaravelFactory\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Makeable\LaravelFactory\Factory;
use Makeable\LaravelFactory\RelationRequest;

trait BuildsRelationships
{
    /**
     * The current batch no.
     */
    protected int $currentBatch = 0;

    /**
     * Requested relations.
     */
    protected array $relations = [];

    /**
     * Load a RelationRequest onto current FactoryBuilder.
     *
     * @param  \Makeable\LaravelFactory\RelationRequest  $request
     * @return \Makeable\LaravelFactory\Concerns\BuildsRelationships
     */
    public function loadRelation(RelationRequest $request): self
    {
        $factory = $this->buildFactoryForRequest($request);

        // Recursively create factories until no further nesting.
        if ($request->hasNesting()) {
            return $this->loadRelation($request->createNestedRequest());
        }

        // Apply the request onto the final relationship factory.
        $factory->apply(...$request->getArguments());

        return $this;
    }

    protected function buildFactoryForRequest(RelationRequest $request): Factory
    {
        $relation = $request->getRelationName();
        $batch = $request->getBatch();

        return data_get($this->relations, "{$relation}.{$batch}", function () use ($request, $relation, $batch) {
            return tap(static::factoryForModel($request->getRelatedClass()), function ($factory) use ($relation, $batch) {
                $this->relations[$relation][$batch] = $factory;
            });
        });
    }

    protected function applyRelations()
    {
        dd('ok');
    }

//
//    /**
//     * Create all requested BelongsTo relations.
//     *
//     * @param Model $child
//     */
//    protected function createBelongsTo($child)
//    {
//        collect($this->relations)
//            ->filter($this->relationTypeIs(BelongsTo::class))
//            ->each(function ($batches, $relation) use ($child) {
//                foreach (array_slice($batches, 0, 1) as $factory) {
//                    $parent = $this->collectModel($factory->inheritConnection($this)->create());
//                    $child->$relation()->associate($parent);
//                }
//            });
//    }
//
//    /**
//     * Create all requested BelongsToMany relations.
//     *
//     * @param Model $sibling
//     */
//    protected function createBelongsToMany($sibling)
//    {
//        collect($this->relations)
//            ->filter($this->relationTypeIs(BelongsToMany::class))
//            ->each(function ($batches, $relation) use ($sibling) {
//                foreach ($batches as $factory) {
//                    $models = $this->collect($factory->inheritConnection($this)->create());
//                    $models->each(function ($model) use ($sibling, $relation, $factory) {
//                        $sibling->$relation()->save($model, $this->mergeAndExpandAttributes($factory->pivotAttributes));
//                    });
//                }
//            });
//    }
//
//    /**
//     * Create all requested HasMany relations.
//     *
//     * @param Model $parent
//     */
//    protected function createHasMany($parent)
//    {
//        collect($this->relations)
//            ->filter($this->relationTypeIs(HasOneOrMany::class))
//            ->each(function ($batches, $relation) use ($parent) {
//                foreach ($batches as $factory) {
//                    // In case of morphOne / morphMany we'll need to set the morph type as well.
//                    if (($morphRelation = $this->newRelation($relation)) instanceof MorphOneOrMany) {
//                        $factory->fill([
//                            $morphRelation->getMorphType() => (new $this->class)->getMorphClass(),
//                        ]);
//                    }
//
//                    $factory->inheritConnection($this)->create([
//                        $parent->$relation()->getForeignKeyName() => $parent->$relation()->getParentKey(),
//                    ]);
//                }
//            });
//    }

    /**
     * Get closure that checks for a given relation-type.
     *
     * @param $relationType
     * @return Closure
     */
    protected function relationTypeIs($relationType)
    {
        return function ($batches, $relation) use ($relationType) {
            return $this->newRelation($relation) instanceof $relationType;
        };
    }

    /**
     * @param $relationName
     * @return Relation
     */
    protected function newRelation($relationName)
    {
        return (new $this->class)->$relationName();
    }

//    /**
//     * Inherit connection from a parent factory.
//     *
//     * @param $factory
//     * @return FactoryBuilder
//     */
//    protected function inheritConnection($factory)
//    {
//        if ($this->connection === null && (new $this->class)->getConnectionName() === null) {
//            return $this->connection($factory->connection);
//        }
//    }

    protected function newBatch(): self
    {
        $this->currentBatch++;

        return $this;
    }
}
