<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                <div class="text-base text-gray-400">@lang('Balance')</div>
                <div class="flex items-center pt-1">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ \Illuminate\Support\Number::currencyCents($balance) }}
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                @livewire('send-money')
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                <h2 class="text-xl font-bold mb-6">@lang('Transfers List')</h2>
                <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            @lang('ID')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Recipient')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Reason')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Amount')
                        </th>
                        <th scope="col" class="px-6 py-3">
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transfers as $transfer)
                        <tr class="bg-white border-b">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{$transfer->id}}
                            </th>
                            <td class="px-6 py-4">
                                {{ $transfer->target->user->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{$transfer->reason}}
                            </td>
                            <td @class([
                                'px-6 py-4',
                            ])>
                                {{Number::currencyCents($transfer->amount)}}
                            </td>
                            <td>
                                <form action={{ route('delete-recurring') }} method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $transfer->id }}" />
                                    <x-primary-button>
                                        {{ __('Delete') }}
                                    </x-primary-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5">
                <h2 class="text-xl font-bold mb-6">@lang('Transactions history')</h2>
                <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            @lang('ID')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Reason')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Description')
                        </th>
                        <th scope="col" class="px-6 py-3">
                            @lang('Amount')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr class="bg-white border-b">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{$transaction->id}}
                            </th>
                            <td class="px-6 py-4">
                                {{$transaction->reason}}
                            </td>
                            <td class="px-6 py-4">
                                @if($transaction->is_transfer)
                                    @if ($transaction->type->isCredit())
                                        @lang(':name sent you :amount', [
                                            'amount' => Number::currencyCents($transaction->transfer->amount),
                                            'name' => $transaction->transfer->source->user->name,
                                        ])
                                    @else
                                        @lang('You sent :amount to :name', [
                                            'amount' => Number::currencyCents($transaction->transfer->amount),
                                            'name' => $transaction->transfer->target->user->name,
                                        ])
                                    @endif
                                @else
                                    @lang('--')
                                @endif
                            </td>
                            <td @class([
                                'px-6 py-4',
                                $transaction->type->isCredit() ? 'text-green-500' : 'text-red-500',
                            ])>
                                {{Number::currencyCents($transaction->type->isCredit() ? $transaction->amount : -$transaction->amount)}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
