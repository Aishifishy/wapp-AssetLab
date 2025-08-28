@props(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'options' => null, 'rows' => 3])

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            rows="{{ $rows }}"
            class="mt-1 block w-full rounded-md shadow-sm sm:text-sm @error($name) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-blue-500 @enderror"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select' && $options)
        <select 
            id="{{ $name }}" 
            name="{{ $name }}"
            class="mt-1 block w-full rounded-md shadow-sm sm:text-sm @error($name) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-blue-500 @enderror"
            @if($required) required @endif
            {{ $attributes }}
        >
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @if(old($name, $value) == $optionValue) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            class="mt-1 block w-full rounded-md shadow-sm sm:text-sm @error($name) border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @else border-gray-300 focus:border-blue-500 focus:ring-blue-500 @enderror"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes }}
        >
    @endif
    
    <x-form-error field="{{ $name }}" />
</div>
