<div class="p-1">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        Perbandingan Perubahan
    </h3>
    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        Data untuk: <strong>{{ class_basename($record->approvable_type) }} (ID: {{ $record->approvable_id }})</strong>
    </p>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Field</th>
                <th scope="col" class="px-6 py-3">Data Lama</th>
                <th scope="col" class="px-6 py-3">Data Baru (Diajukan)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->changes as $field => $newValue)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $field }}
                    </th>
                    <td class="px-6 py-4" style="text-decoration: line-through; color: #EF4444;">
                        {{ data_get($record->approvable, $field, 'N/A') }}
                    </td>
                    <td class="px-6 py-4" style="color: #22C55E; font-weight: bold;">
                        {{ $newValue }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>