<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\WalletTransfertType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendMoneyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recipient_email' => [
                'required',
                'email',
                Rule::exists(User::class, 'email')->whereNot('id', $this->user()->id),
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
            ],
            'reason' => [
                'required',
                'string',
                'max:255',
            ],
            'start_date' => [
                'required_if:recurring,true',
                'date',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required_if:recurring,true',
                'date',
                'after_or_equal:start_date',
            ],
            'frequency' => [
                'required_if:recurring,true',
                'numeric',
                'min:1',
            ],
        ];
    }

    public function getRecipient(): User
    {
        return User::where('email', '=', $this->input('recipient_email'))->firstOrFail();
    }

    public function getAmountInCents(): int
    {
        return (int) ceil($this->float('amount') * 100);
    }

    public function isRecurring(): bool
    {
        return $this->boolean('recurring');
    }

    public function getType(): WalletTransfertType
    {
        return match ($this->isRecurring()) {
            true => WalletTransfertType::RECURRING,
            default => WalletTransfertType::SINGLE
        };
    }
}
