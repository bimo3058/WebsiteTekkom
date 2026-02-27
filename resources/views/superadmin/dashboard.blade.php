<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">
            Superadmin Dashboard
        </h2>
    </x-slot>

    <div class="p-6">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Email</th>
                    <th class="p-2 border">Role</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="p-2 border">{{ $user->name }}</td>
                    <td class="p-2 border">{{ $user->email }}</td>
                    <td class="p-2 border">{{ $user->role }}</td>
                    <td class="p-2 border">
                        <form method="POST" action="{{ route('superadmin.updateRole', $user->id) }}">
                            @csrf
                            <select name="role" class="border p-1">
                                <option value="SUPERADMIN">SUPERADMIN</option>
                                <option value="ADMIN">ADMIN</option>
                                <option value="LECTURER">LECTURER</option>
                                <option value="STUDENT">STUDENT</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded">
                                Update
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</x-app-layout>