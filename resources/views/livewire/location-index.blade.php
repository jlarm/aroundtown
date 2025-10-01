<div class="space-y-8">
    <div class="flex flex-wrap gap-3">
        <flux:button
            wire:click="filterByCategory(null)"
            :variant="$category === null ? 'filled' : 'ghost'"
        >
            All Locations
        </flux:button>
        @foreach($categories as $cat)
            <flux:button
                wire:click="filterByCategory({{ $cat->id }})"
                :variant="$category === $cat->id ? 'filled' : 'ghost'"
            >
                {{ $cat->name }} ({{ $cat->locations_count }})
            </flux:button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        @forelse($locations as $location)
            <a wire:navigate.hover href="{{ route('location.show', $location) }}" class="space-y-3 rounded-2xl p-5 hover:bg-gray-50 hover:cursor-pointer">
                @if($location->image_path)
                    <picture>
                        <source srcset="{{ Storage::url($location->webp_image_path) }}" type="image/webp">
                        <img class="aspect-3/2 object-cover rounded-2xl" src="{{ Storage::url($location->image_path) }}" alt="{{ $location->name }}">
                    </picture>
                @endif
                <div>
                    <h1 class="font-display text-3xl text-truffle-trouble">{{ $location->name }}</h1>
                    <p class="text-anchorfish-blue">{{ $location->short_description }}</p>
                    @if($location->categories->isNotEmpty())
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach($location->categories as $category)
                                <span class="text-xs px-2 py-1 bg-gray-100 rounded-full">{{ $category->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500">No locations found in this category.</p>
            </div>
        @endforelse
    </div>
</div>
