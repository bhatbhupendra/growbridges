<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name (Company Name in case of Agent & School)')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Role')" />
            <select id="role" name="role"
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                required>
                <option value="">Select Role</option>
                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>Student Sending Agent</option>
                <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Individual Student</option>
                <option value="school" {{ old('role') === 'school' ? 'selected' : '' }}>Japanese Language School
                </option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- School Name (only for school role) -->
        <div class="mt-4 hidden" id="school_name_wrapper">
            <x-input-label for="school_name" :value="__('School Name')" />
            <x-text-input id="school_name" class="block mt-1 w-full" type="text" name="school_name"
                :value="old('school_name')" autocomplete="organization" />
            <x-input-error :messages="$errors->get('school_name')" class="mt-2" />
        </div>

        <!-- Student Name (optional override for student role) -->
        <div class="mt-4 hidden" id="student_name_wrapper">
            <x-input-label for="student_name" :value="__('Student Full Name')" />
            <x-text-input id="student_name" class="block mt-1 w-full" type="text" name="student_name"
                :value="old('student_name')" autocomplete="name" />
            <x-input-error :messages="$errors->get('student_name')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const schoolWrapper = document.getElementById('school_name_wrapper');
        const studentWrapper = document.getElementById('student_name_wrapper');
        const schoolInput = document.getElementById('school_name');
        const studentInput = document.getElementById('student_name');

        function toggleRoleFields() {
            const role = roleSelect.value;

            schoolWrapper.classList.add('hidden');
            studentWrapper.classList.add('hidden');

            schoolInput.required = false;
            studentInput.required = false;

            if (role === 'school') {
                schoolWrapper.classList.remove('hidden');
                schoolInput.required = true;
            }

            if (role === 'student') {
                studentWrapper.classList.remove('hidden');
            }
        }

        roleSelect.addEventListener('change', toggleRoleFields);
        toggleRoleFields();
    });
    </script>
</x-guest-layout>