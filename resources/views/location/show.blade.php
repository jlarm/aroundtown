<x-layout>
<div class="max-w-7xl mx-auto mt-10">
    <div class="flex justify-between items-center">
        <h1 class="font-display text-6xl text-truffle-trouble">{{ $location->name }}</h1>
        <flux:button variant="ghost" icon="arrow-left" wire:navigate.hover :href="route('home')">Back</flux:button>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-5">
        <div>
            @if($location->image_path)
                <picture>
                    <source srcset="{{ Storage::url($location->webp_image_path) }}" type="image/webp">
                    <img class="aspect-3/2 object-cover rounded-2xl" src="{{ Storage::url($location->image_path) }}" alt="{{ $location->name }}">
                </picture>
            @endif
        </div>
        <div class="space-y-5">
            {!! $location->description !!}
            <div>
                <p class="font-bold">Address</p>
                <address>
                    {{ $location->address }}<br />
                    {{ $location->city }}, {{ $location->state }} {{ $location->zip }}
                </address>
            </div>
            <div>
                <p class="font-bold">Phone Number</p>
                <p>{{ $location->phone }}</p>
            </div>
            <div>
                @if($location->url)
                    <flux:button
                        variant="filled"
                        :href="$location->url"
                        target="_blank"
                        icon:trailing="arrow-up-right"
                    >
                        Visit Website
                    </flux:button>
                @endif
                @if($location->menu_url)
                        <flux:button
                            variant="filled"
                            :href="$location->menu_url"
                            target="_blank"
                            icon:trailing="arrow-up-right"
                        >
                            Visit Menu
                        </flux:button>
                @endif
                @if($location->directions_url)
                        <flux:button
                            variant="filled"
                            :href="$location->directions_url"
                            target="_blank"
                            icon:trailing="arrow-up-right"
                        >
                            Get Directions
                        </flux:button>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layout>
