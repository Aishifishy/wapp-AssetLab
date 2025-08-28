@extends('layouts.admin')

@section('title', 'Create Equipment Type')

@section('content')
<x-admin.form-wrapper 
    title="Create Equipment Type"
    :back-route="route('admin.equipment.categories.index')"
    back-text="Back to Equipment Types"
    :form-action="route('admin.equipment.categories.store')"
    submit-text="Create Equipment Type">

    <x-admin.form-field
        name="name"
        label="Equipment Type Name"
        :required="true"
        placeholder="e.g., Laboratory Equipment" />

    <x-admin.form-field
        name="description"
        label="Description"
        type="textarea"
        placeholder="Equipment type description" />

</x-admin.form-wrapper>
@endsection