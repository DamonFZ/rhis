<x-layouts.mobile>
    <div class="min-h-screen bg-gray-50 flex flex-col items-center pt-20 px-5">

        <div class="mb-10 w-24 h-24 bg-white border border-gray-200 rounded-2xl flex items-center justify-center shadow-sm overflow-hidden">
            <img src="{{ asset('image/logo.png') }}" alt="Logo" class="w-4/5 object-contain">
        </div>

        <div class="w-full max-w-sm bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-gray-50 pb-4">
                    <span class="text-gray-500">{{ __('mobile.name_label') }}</span>
                    <span class="text-gray-900 font-medium text-lg">{{ $patient->name ?? __('mobile.name_value') }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-gray-50 pb-4">
                    <span class="text-gray-500">{{ __('mobile.member_id_label') }}</span>
                    <span class="text-gray-900 font-medium">{{ $patient->patient_id ?? __('mobile.member_id_value') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">{{ __('mobile.phone_label') }}</span>
                    <span class="text-gray-900 font-medium">{{ $patient->phone ?? __('mobile.phone_value') }}</span>
                </div>
            </div>
        </div>

        @if (empty($patient->wechat_openid))
            <form action="{{ route('mobile.bind.store') }}" method="POST" class="w-full max-w-sm">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                <button type="submit" class="w-full bg-[#07c160] active:bg-[#06ad56] text-white font-medium text-lg py-3.5 rounded-lg transition-colors">
                    {{ __('mobile.confirm_bind') }}
                </button>
            </form>
        @else
            <div class="w-full max-w-sm">
                <button type="button" class="w-full bg-gray-300 text-gray-700 font-medium text-lg py-3.5 rounded-lg transition-colors cursor-not-allowed" disabled>
                    {{ __('mobile.already_bound') }}
                </button>
            </div>
        @endif

        <div class="mt-8 text-center">
            <a href="#" class="text-sm text-gray-400 hover:text-gray-500 transition-colors">
                {{ __('mobile.not_you_contact_desk') }}
            </a>
        </div>

    </div>
</x-layouts.mobile>
