@props(['name', 'label', 'type' => 'text', 'required' => false, 'placeholder' => '', 'value' => '', 'options' => null, 'rows' => 3])

<div class="form-group">
    <label for="{{ $name }}" class="{{ $required ? 'form-label form-label-required' : 'form-label-optional' }}">
        {{ $label }}
    </label>
    
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
