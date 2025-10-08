@props(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'options' => null, 'rows' => 3])

<div class="form-group">
    @if($type !== 'checkbox')
        <label for="{{ $name }}" class="{{ $required ? 'form-label form-label-required' : 'form-label-optional' }}">
            {{ $label }}
        </label>
    @endif
    
    @if($type === 'textarea')
        <textarea 
            id="{{ $name }}" 
            name="{{ $name }}" 
            rows="{{ $rows }}"
            class="@error($name) form-textarea-error @else form-textarea @enderror"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes }}
        >{{ old($name, $value) }}</textarea>
    @elseif($type === 'select' && $options)
        <select 
            id="{{ $name }}" 
            name="{{ $name }}"
            class="@error($name) form-select-error @else form-select @enderror"
            @if($required) required @endif
            {{ $attributes }}
        >
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" @if(old($name, $value) == $optionValue) selected @endif>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <div class="flex items-center">
            <input type="hidden" name="{{ $name }}" value="0">
            <input 
                type="checkbox" 
                id="{{ $name }}" 
                name="{{ $name }}" 
                value="1"
                @if(old($name, $value) == '1' || old($name, $value) === true) checked @endif
                class="form-checkbox"
                {{ $attributes }}
            >
            <label for="{{ $name }}" class="ml-2 block text-sm text-gray-900">
                {{ $label }}
            </label>
        </div>
    @else
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            class="@error($name) form-input-error @else form-input @enderror"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes }}
        >
    @endif
    
    <x-form-error field="{{ $name }}" />
</div>
