<?php

namespace App\Repositories;

interface QuestionRepositoryInterface
{
    public function paginateFiltered(array $filters = [], int $perPage = 10);
    public function countByCategory(string $category, ?int $paketSoalId = null): int;
    public function totalCount(?int $paketSoalId = null): int;
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): void;
    public function queryFiltered(array $filters = []);
}