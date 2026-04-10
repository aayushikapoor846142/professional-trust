
@extends('admin-panel.layouts.app')
@section('content')


<div class="container pb-4">
    <form action="{{ route('panel.tickets.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            {!! FormHelper::formInputText([ 'label' => "Subject", 'id' => 'subject', 'name' => 'subject', ]) !!}
        </div>
        <div class="mb-3">
            {!! FormHelper::formTextarea([
                'name'=>"description",
                'id'=>"description",
                "label"=>"Description",                           
                'textarea_class'=>"noval cds-texteditor",
                'required'=>true,
            ]) !!}
        </div>
        <div class="mb-3">
            {!! FormHelper::formSelect([ 
                'name' => 'category_id',
                'label' => 'Category',
                'id' => 'category_id',
                'options' => collect($categories)->map(function($category) {
                    return ['value' => $category->id, 'label' => $category->name];
                })->prepend(['value' => '', 'label' => 'Select Category'])->toArray(),
                'value_column' => 'value',
                'label_column' => 'label',
                'is_multiple' => false,
                'required' => true,
            ]) !!}
        </div>
        <div class="mb-3">
            {!! FormHelper::formSelect([ 
                'name' => 'priority',
                'label' => 'Priority',
                'id' => 'priority',
                'options' => [
                    ['value' => 'low', 'label' => 'Low'],
                    ['value' => 'medium', 'label' => 'Medium'],
                    ['value' => 'high', 'label' => 'High'],
                    ['value' => 'urgent', 'label' => 'Urgent'],
                ],
                'value_column' => 'value',
                'label_column' => 'label',
                'is_multiple' => false,
                'required' => true,
            ]) !!}
        </div>
        <button type="submit" class="btn btn-success">Submit Ticket</button>
        <a href="{{ route('panel.tickets.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 