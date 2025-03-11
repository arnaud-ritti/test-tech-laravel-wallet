<div>
    <h2 class="text-xl font-bold mb-6">@lang('Send money to a friend')</h2>
    <form method="POST" action="{{ route('send-money') }}" class="space-y-4">
        @csrf

        @if (session('money-sent-status') === 'success')
            <div class="p-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">@lang('Money sent!')</span>
                @if(session('money-sent-type') == \App\Enums\WalletTransfertType::SINGLE)
                @lang(':amount were successfully sent to :name.', ['amount' => Number::currencyCents(session('money-sent-amount', 0)), 'name' => session('money-sent-recipient-name')])
                @elseif(session('money-sent-type') == \App\Enums\WalletTransfertType::RECURRING)
                @lang(':amount were successfully scheduled to :name.', [
                    'amount' => Number::currencyCents(session('money-sent-amount', 0)),
                    'name' => session('money-sent-recipient-name')
                ])
                @endif
            </div>
        @elseif (session('money-sent-status') === 'insufficient-balance')
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">@lang('Insufficient balance!')</span>
                    @lang('You can\'t send :amount to :name.', ['amount' => Number::currencyCents(session('money-sent-amount', 0)), 'name' => session('money-sent-recipient-name')])
                </div>
        @endif

        <div>
            <x-input-label for="recipient_email" :value="__('Recipient email')" />
            <x-text-input id="recipient_email"
                            class="block mt-1 w-full"
                            type="email"
                            name="recipient_email"
                            :value="old('recipient_email')"
                            required />
            <x-input-error :messages="$errors->get('recipient_email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="amount" :value="__('Amount (€)')" />
            <x-text-input id="amount"
                            class="block mt-1 w-full"
                            type="number"
                            min="0"
                            step="0.01"
                            :value="old('amount')"
                            name="amount"
                            required />
            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="reason" :value="__('Reason')" />
            <x-text-input id="reason"
                            class="block mt-1 w-full"
                            type="text"
                            :value="old('reason')"
                            name="reason"
                            required />
            <x-input-error :messages="$errors->get('reason')" class="mt-2" />
        </div>

        <div>
            <div class="flex gap-2 items-center">
                <x-text-input id="recurring"
                            class="mt-1"
                            type="checkbox"
                            wire:model.live="recurring"
                            name="recurring"
                             />
                <x-input-label for="recurring" :value="__('Should be recurring ?')" />
            </div>
            <x-input-error :messages="$errors->get('recurring')" class="mt-2" />
        </div>

        @if ($this->recurring ?? false)
            <div>
                <x-input-label for="start_date" :value="__('Start Date')" />
                <x-text-input id="start_date"
                                class="block mt-1 w-full"
                                type="date"
                                :value="old('start_date')"
                                name="start_date"
                                required />
                <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="end_date" :value="__('End Date')" />
                <x-text-input id="end_date"
                                class="block mt-1 w-full"
                                type="date"
                                :value="old('end_date')"
                                name="end_date"
                                required />
                <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="frequency" :value="__('Frequency in days')" />
                <x-text-input id="frequency"
                                class="block mt-1 w-full"
                                type="number"
                                :value="old('frequency')"
                                name="frequency"
                                required />
                <x-input-error :messages="$errors->get('frequency')" class="mt-2" />
            </div>
        @endif

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Send my money !') }}
            </x-primary-button>
        </div>
    </form>
</div>
