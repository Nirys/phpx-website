<x-layout title="Get Updates">
	
	<h1 class="font-mono font-semibold text-white text-2xl sm:text-4xl md:text-5xl lg:text-6xl">
		PHP×Phill<span x-data x-typed="['y Newsl', 'y Updates']">y</span>
	</h1>
	
	<x-flash-message />
	
	<p class="my-8 text-xl max-w-3xl">
		Don’t call it a newsletter. We’ll only ever email you about upcoming events or calls for speakers. Promise.
	</p>
	
	<form action="{{ route('newsletter-subscriber.store') }}" method="post" class="w-full max-w-md transform -rotate-1 ml-8">
		
		@csrf
		
		<label class="block font-mono font-bold text-white" for="full_name">
			Your Name
		</label>
		<input
			class="font-mono text-black p-2 font-semibold w-full"
			type="text" name="full_name" id="full_name"
		/>
		@error('full_name')
		<div class="bg-red-50 mt-1 mb-3 p-2 text-black border-l-8 border-red-400 font-mono font-bold w-full flex items-center gap-2 transform text-red-600">
			<svg class="w-5 h-5 fill-red-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				<path d="M64 80c-8.8 0-16 7.2-16 16V416c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V96c0-8.8-7.2-16-16-16H64zM0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm224 32c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM192 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
			</svg>
			{{ $message }}
		</div>
		@enderror
		
		<label class="block font-mono font-bold text-white mt-3" for="email">
			Email
		</label>
		<input
			class="font-mono text-black p-2 font-semibold w-full"
			type="text" name="email" id="email" placeholder="you@phpxphilly.com"
		/>
		@error('email')
		<div class="bg-red-50 mt-1 mb-3 p-2 text-black border-l-8 border-red-400 font-mono font-bold w-full flex items-center gap-2 transform text-red-600">
			<svg class="w-5 h-5 fill-red-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
				<path d="M64 80c-8.8 0-16 7.2-16 16V416c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V96c0-8.8-7.2-16-16-16H64zM0 96C0 60.7 28.7 32 64 32H384c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm224 32c13.3 0 24 10.7 24 24V264c0 13.3-10.7 24-24 24s-24-10.7-24-24V152c0-13.3 10.7-24 24-24zM192 352a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z" />
			</svg>
			{{ $message }}
		</div>
		@enderror
		
		<div class="mt-3">
			<button class="bg-white px-3 py-1.5 text-black font-semibold transform opacity-90 hover:opacity-100 focus:opacity-100">
				Get Updates
			</button>
		</div>
	</form>

</x-layout>
