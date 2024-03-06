@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Activities</h3>
                    <a href="{{ route('activities.create') }}" class="btn btn-outline-primary">Create Activity</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="activities-table" class="table table-striped">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Audio</th>
                                            <th>Subtitle</th>
                                            <th>Characters</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($activities as $activity)
                                            <tr>
                                                <td>{{ $activity->name }}</td>
                                                <td>{{ $activity->description }}</td>
                                                <td>{!! $activity->audio ? '<audio controls><source src="'.asset('storage/'.$activity->audio).'" type="audio/mpeg"></audio>' : '<span class="badge badge-danger">No Audio</span>' !!}</td>

                                                <td>
                                                    @if($activity->sub)
                                                        <span class="badge badge-success">Uploaded</span>
                                                    @else
                                                        <span class="badge badge-danger">Empty</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @foreach($activity->characters as $character)
                                                        <span class="badge badge-primary">{{ $character->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td>{{ $activity->start_date }}</td>
                                                <td>{{ $activity->end_date }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('activities.edit', $activity->id) }}" class="btn btn-outline-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" style="border-top-right-radius: 5px!important; border-bottom-right-radius: 5px!important;">
                                                            <i class="fa fa-edit mr-2"></i><span class="small">Edit</span>
                                                        </a>
                                                        <form action="{{ route('activities.destroy', $activity->id) }}" method="POST" style="display: inline;">
                                                            {{ csrf_field() }}
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <button type="submit" class="btn btn-outline-danger"  data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                                                <i class="fa fa-trash mr-2"></i><span class="small">Delete</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $("body").tooltip({ selector: '[data-toggle=tooltip]' });
            $('#activities-table').DataTable({
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                }
            });
        });
    </script>
@endsection

