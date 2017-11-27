<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="wrapper">
<div class="content-wrapper col-md-10 col-md-offset-1">
<section class="content">
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <form action="{{url('api_manager')}}" method="GET">
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="search" id="name" value="{{ Request::get('search') }}" placeholder=" Name">
                    </div>
                </div><!-- /.box-body -->

                <div class="box-footer">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div><!-- /.col -->
</div><!-- /.row -->
<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- general form elements -->
        <div class="box box-primary">
            <div class="box-header">
            </div><!-- /.box-header -->
            <div class="box-body table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Created At</th>
                        <th>Client</th>
                        <th>Api Keys</th>
                        <th>Action</th>
                    </tr>
                    <?php $i = 1 + $data->currentPage() * $data->perPage() - $data->perPage(); ?>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{$row->created_at->format('M d, Y')}}</td>
                        <td>{{ $row->client }}</a></td>
                        <td>{{$row->api_keys}}</td>
                        <td><a href="{{ url('api_manager', $row->id).'/edit' }}" class="btn btn-warning btn-sm">
                        	action
                        </a></td>
                    </tr>
                    @endforeach
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer clearfix">
                {!! $data->render() !!}
                <div class="pull-right">
                    <a href="{{ url('api_manager/create') }}" class="btn btn-success">Add</a>
                </div>
            </div>
                {!! csrf_field() !!}
        </div><!-- /.box -->

    </div><!--/.col -->
</div>   <!-- /.row -->
</section>
</div>
</div>