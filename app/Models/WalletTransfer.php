<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\WalletTransfertType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransfer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'type' => WalletTransfertType::class,
        ];
    }

    /**
     * @return BelongsTo<Wallet>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'source_id');
    }

    /**
     * @return BelongsTo<Wallet>
     */
    public function target(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'target_id');
    }

    /**
     * @return BelongsTo<WalletTransaction>
     */
    public function credit(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'credit_id');
    }

    /**
     * @return BelongsTo<Wallet>
     */
    public function debit(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class, 'debit_id');
    }
}
