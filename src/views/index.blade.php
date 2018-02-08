<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="wrapper">
<div class="content-wrapper col-md-10 col-md-offset-1">
<section class="content">
<div class="row">
    <div class="col-md-12">
        <div class="box">
            @if(Session::has('message'))
                <div class="alert alert-info">
                    {{ Session::get('message') }}
                </div>
            @endif
            <form action="{{url('api-manager')}}" method="GET">
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
                        <th>User</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    <?php $i = 1 + $data->currentPage() * $data->perPage() - $data->perPage(); ?>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{$row->created_at->format('M d, Y')}}</td>
                        <td>{{ $row->client }}</a></td>
                        <td>{{$row->api_key}}</td>
                        <td>{{ $row->getUserName->name }}</a></td>
                        <td>{{ $row->description }}</a></td>
                        <td>{{ $row->getHistory->getStateTo->label }}</a></td>
                        <td><a href="{{ url('api-manager', $row->id).'/edit' }}" class="btn btn-warning btn-sm">
                        	action
                        </a>
                        <a href="{{ url('api-manager', $row->id).'' }}" class="btn btn-success btn-sm">
                        	view
                        </a></td>
                    </tr>
                    @endforeach
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer clearfix">
                {!! $data->render() !!}
                <div class="pull-right">
                    <a href="{{ url('api-manager/create') }}" class="btn btn-success">Add</a>
                </div>
            </div>
                {!! csrf_field() !!}
        </div><!-- /.box -->

    </div><!--/.col -->
</div>   <!-- /.row -->
</section>
</div>
</div>
