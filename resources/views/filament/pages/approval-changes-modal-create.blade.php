<div class="p-1">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        Detail Data Baru yang Diajukan
    </h3>
    <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
        Data untuk: <strong>{{ class_basename($record->approvable_type) }}</strong>
    </p>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Field</th>
                <th scope="col" class="px-6 py-3">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->changes as $field => $newValue)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $field }}
                    </th>
                    <td class="px-6 py-4" style="color: green; font-weight: bold;">
                        {{ $newValue }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>