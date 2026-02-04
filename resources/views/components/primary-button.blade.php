<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-[#24C6A0] hover:bg-[#1ea987] text-white text-lg font-bold py-2 px-8 rounded-2xl transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
