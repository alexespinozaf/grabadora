@extends('layouts.gradesTemplate')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Create Activity</h3>
                    <input type="submit" class="btn btn-outline-primary" value="Create"/>
                </div>
            </div>
            <div class="card-body">
                        <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" id="description" cols="10" rows="2" class="form-control" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="audio">Audio</label>
                                <input type="file" name="audio" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="sub">Subtitle</label>
                                <input type="file" name="sub" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="characters">Characters</label>
                                <select name="characters[]" id="characters" class="form-control custom-select" data-placeholder="Select characters" multiple>
                                    @foreach($characters as $character)
                                        @php
                                            $rol_order = str_replace(['r1', 'r2', 'r3'], ['Rol 1', 'Rol 2', 'Rol 3'], $character->rol_order);
                                        @endphp
                                        <option value="{{ $character->id }}">{{ $character->name.' - '.$rol_order}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="resource">Resource</label>
                                <select name="resource" id="resource" class="form-control custom-select" data-placeholder="Select activity resource from Moodle" required>
                                    @foreach($resourceLinks as $resource)
                                        <option value="{{ $resource->id }}">{{ $resource->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control custom-select" data-placeholder="Select the activity type" required>
                                    <option value="q&a">Q&A</option>
                                    <option value="rolgame">Rol Game</option>
                                    <option value="simple">Simple</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-outline-primary" >Create</button>
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
                width: '100%',
            } );

        });
    </script>
@endsection

