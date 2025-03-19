<?php

namespace App\Repositories;

use App\Models\Company;
use App\Repositories\Interfaces\CompanyRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class CompanyRepository implements CompanyRepositoryInterface
{
    /**
     * Get all companies with pagination
     */
    public function getAllWithPagination(int $perPage = 10): LengthAwarePaginator
    {
        return Company::paginate($perPage);
    }
    
    /**
     * Find company by ID
     */
    public function findById(int $id): ?Company
    {
        return Company::find($id);
    }
    
    /**
     * Create new company
     */
    public function create(array $data): Company
    {
        return Company::create($data);
    }
    
    /**
     * Update company
     */
    public function update(Company $company, array $data): bool
    {
        Log::info('Updating company data:', $data);
        
        $company->forceFill($data);
        $company->updated_at = now();
        
        Log::info('Before Save (Dirty Data):', $company->getDirty());
        
        return $company->save();
    }
    
    /**
     * Delete company
     */
    public function delete(Company $company): bool
    {
        return $company->delete();
    }
}
