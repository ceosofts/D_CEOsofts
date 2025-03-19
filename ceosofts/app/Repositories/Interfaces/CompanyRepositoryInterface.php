<?php

namespace App\Repositories\Interfaces;

use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepositoryInterface
{
    /**
     * Get all companies with pagination
     */
    public function getAllWithPagination(int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Find company by ID
     */
    public function findById(int $id): ?Company;
    
    /**
     * Create new company
     */
    public function create(array $data): Company;
    
    /**
     * Update company
     */
    public function update(Company $company, array $data): bool;
    
    /**
     * Delete company
     */
    public function delete(Company $company): bool;
}
