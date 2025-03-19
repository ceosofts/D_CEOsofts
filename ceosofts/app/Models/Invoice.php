<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Invoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'customer_name',
        'quotation_id',
        'invoice_date',
        'your_ref',
        'our_ref',
        'total_amount',
        'payment_amount',
        'remaining_balance',
        'payment_percentage',
        'amount_in_words',
        'payment_terms',
        'due_date',
        'is_public',
        'status_id',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_percentage' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'remaining_balance' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $appends = ['is_paid'];

    // Relationships
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function status()
    {
        return $this->belongsTo(JobStatus::class, 'status_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the user that owns the invoice.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('name', 'Paid');
        });
    }

    public function scopeUnpaid($query)
    {
        return $query->whereHas('status', function($q) {
            $q->where('name', '!=', 'Paid');
        });
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', Carbon::today())
                    ->whereHas('status', function($q) {
                        $q->where('name', '!=', 'Paid');
                    });
    }

    // Accessors & Mutators
    public function getIsOverdueAttribute()
    {
        return !$this->isPaid() && $this->due_date->isPast();
    }

    public function getIsPaidAttribute()
    {
        return $this->status && $this->status->name === 'ชำระเงินแล้ว';
    }

    public function getDaysOverdueAttribute()
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(Carbon::today());
    }

    // Helper Methods
    public function isPaid()
    {
        return $this->is_paid;
    }

    public function markAsPaid()
    {
        $paidStatus = JobStatus::where('name', 'Paid')->first();
        if ($paidStatus) {
            $this->update([
                'status_id' => $paidStatus->id,
                'updated_by' => auth()->id()
            ]);
            return true;
        }
        return false;
    }

    public function calculateRemainingBalance()
    {
        return $this->total_amount - $this->payment_amount;
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-set created_by on creation
        static::creating(function ($invoice) {
            if (!$invoice->created_by) {
                $invoice->created_by = auth()->id();
            }
        });

        // Auto-set updated_by on update
        static::updating(function ($invoice) {
            $invoice->updated_by = auth()->id();
        });
    }
}
