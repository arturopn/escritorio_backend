<div class="table-responsive">
    <table class="table" id="services-table">
        <thead>
            <tr>
                <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Status</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($services as $services)
            <tr>
                <td>{{ $services->name }}</td>
            <td>{{ $services->description }}</td>
            <td>{{ $services->price }}</td>
            <td>{{ $services->status }}</td>
                <td>
                    {!! Form::open(['route' => ['services.destroy', $services->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('services.show', [$services->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('services.edit', [$services->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
