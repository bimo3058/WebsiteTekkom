@props(['disabled' => false])

<!-- <input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}> -->
<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:border-blue-600 dark:focus:border-blue-500 focus:ring-blue-600 dark:focus:ring-blue-500 rounded-xl shadow-sm'
]) !!}>