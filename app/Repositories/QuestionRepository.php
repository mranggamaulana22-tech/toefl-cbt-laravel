<?php

namespace App\Repositories;

use App\Models\Question;

class QuestionRepository implements QuestionRepositoryInterface
{
    public function paginateFiltered(array $filters = [], int $perPage = 10)
    {
        $query = $this->queryFiltered($filters);

        return $query->latest()->paginate($perPage);
    }

    public function countByCategory(string $category): int
    {
        return Question::where('category', $category)->count();
    }

    public function totalCount(): int
    {
        return Question::count();
    }

    public function create(array $data)
    {
        return Question::create($data);
    }

    public function update(int $id, array $data)
    {
        $q = Question::findOrFail($id);
        $q->update($data);
        return $q;
    }

    public function delete(int $id): void
    {
        $q = Question::findOrFail($id);
        $q->delete();
    }

    public function queryFiltered(array $filters = [])
    {
        $query = Question::query();

        if (!empty($filters['category'] ?? null)) {
            $query->where('category', $filters['category']);
        }

        return $query;
    }
}
