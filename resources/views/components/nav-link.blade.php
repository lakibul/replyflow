@props(['href', 'active' => false])

<a href="{{ $href }}"
   @class([
       'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
       'bg-indigo-600 text-white'      => $active,
       'text-slate-300 hover:bg-slate-800 hover:text-white' => ! $active,
   ])>
    {{ $slot }}
</a>
