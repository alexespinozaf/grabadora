@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Edit Activity</h3>
                    <input type="submit" class="btn btn-outline-primary" value="Edit"/>
                </div>
            </div>
            <div class="card-body">
                <form id="activity-form" action="{{ route('activities.update', $activity->id) }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ $activity->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" cols="10" rows="2" class="form-control" required>{{ $activity->description }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="audio">Audio</label>
                        <input type="file" name="audio" class="form-control"  value="{{ $activity->audio }}">
                    </div>
                    <div class="form-group">
                        <label for="sub">Subtitle</label>
                        <input type="file" name="sub" class="form-control"  value="{{ asset($activity->sub)}}">
                    </div>
                    <div class="form-group">
                        <label for="characters">Characters</label>
                        <select name="characters[]" id="characters" class="form-control custom-select" multiple>
                            @foreach($characters as $character)
                                @php
                                    $rol_order = str_replace(['r1', 'r2', 'r3'], ['Rol 1', 'Rol 2', 'Rol 3'], $character->rol_order);
                                @endphp
                                <option value="{{ $character->id }}" {{ in_array($character->id, $activity->characters->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $character->name.' - '.$rol_order}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $activity->start_date }}" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $activity->end_date }}" required>
                    </div>
                    <div class="form-group">
                        <label for="resource">Resource</label>
                        <select name="resource" id="resource" class="form-control custom-select" data-placeholder="Select activity resource from Moodle">
                            @foreach($resourceLinks as $resource)
                                <option value="{{ $resource->id }}" {{ $activity->resourcelink_id == $resource->id ? 'selected' : '' }}>{{ $resource->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control custom-select" data-placeholder="Select the activity type">
                            <option value="q&a" {{ $activity->type == 'q&a' ? 'selected' : '' }}>Q&A</option>
                            <option value="rolgame" {{ $activity->type == 'rolgame' ? 'selected' : '' }}>Rol Game</option>
                            <option value="simple" {{ $activity->type == 'simple' ? 'selected' : '' }}>Simple</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center">
                        <input type="submit" class="btn btn-outline-primary" value="Edit"/>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){

            $( '.custom-select' ).select2( {
                placeholder: $( this ).data( 'placeholder' ),
                closeOnSelect: false,
            } );

        });
    </script>
@endsection

