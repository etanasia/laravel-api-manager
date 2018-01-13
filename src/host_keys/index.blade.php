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
            <form action="{{url('host-keys')}}" method="GET">
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Hostname</label>
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
                <a href="{{ url('host-keys/create') }}" class="btn btn-success pull-right">Request</a>
                {{-- <span class="btn btn-success" onclick="request()">Request</span> --}}
                <table class="table table-bordered table-hover">
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Created At</th>
                        <th>Hostname</th>
                        <th>Api Keys</th>
                        <th>State</th>
                        <th>Transition</th>
                        <th>User</th>
                    </tr>
                    <?php $i = 1 + $data->currentPage() * $data->perPage() - $data->perPage(); ?>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{$row->created_at->format('M d, Y')}}</td>
                        <td>{{ $row->hostname }}</td>
                        <td>{{$row->keys}}</td>
                        <td>{{$row->state}}</td>
                        <td>{{$row->transition}}</td>
                        <td>{{$row->getUserName->name}}</td>
                    </tr>
                    @endforeach
                </table>
            </div><!-- /.box-body -->
            <div class="box-footer clearfix">
                {!! $data->render() !!}
            </div>
                {!! csrf_field() !!}
        </div><!-- /.box -->

    </div><!--/.col -->
</div>   <!-- /.row -->
</section>
</div>
</div>
{{-- <script type="text/javascript">
  function request() {
    var base_url = "{{ url('/') }}";
    var a = confirm("Are You sure You want to request Apikey?");
    if(a == true){
      $.ajax({
        headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
  			url : "/host-keys/request",
  			data : {
            client: base_url,
            requests: "Request"
        },
  			type : 'POST'
  		});
    }else {
      return false;
    }
  }
</script> --}}
