@if (session('status'))
    <div class="mt-4 pixel-outline px-4 py-3 text-sm bg-green-100 text-green-900">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="mt-4 pixel-outline px-4 py-3 text-sm bg-red-100 text-red-900">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
