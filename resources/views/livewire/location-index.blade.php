<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    @foreach($locations as $location)
        <a wire:navigate.hover href="{{ route('location.show', $location) }}" class="space-y-3 rounded-2xl p-5 hover:bg-gray-50 hover:cursor-pointer">
            @if($location->image_path)
                <img class="aspect-3/2 object-cover rounded-2xl" src="{{ Storage::url($location->image_path) }}" alt="{{ $location->name }}">
            @endif
            <div>
                <h1 class="font-display text-3xl text-truffle-trouble">{{ $location->name }}</h1>
                <p class="text-anchorfish-blue">{{ $location->short_description }}</p>
            </div>
        </a>
    @endforeach
</div>
