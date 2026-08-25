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

    public function countByCategory(string $category, ?int $paketSoalId = null): int
    {
        return Question::where('category', $category)
            ->when($paketSoalId, fn ($query) => $query->where('paket_soal_id', $paketSoalId))
            ->count();
    }

    public function totalCount(?int $paketSoalId = null): int
    {
        return Question::query()
            ->when($paketSoalId, fn ($query) => $query->where('paket_soal_id', $paketSoalId))
            ->count();
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

        if (!empty($filters['paket_soal_id'] ?? null)) {
            $query->where('paket_soal_id', $filters['paket_soal_id']);
        }

        return $query;
    }
}