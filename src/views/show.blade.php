<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<div class="wrapper">
<div class="content-wrapper col-md-10 col-md-offset-1">
<section class="content">
<div class="row">
<!-- left column -->
<div class="col-md-12">
    <!-- general form elements -->
    <div class="box box-primary">
        <div class="box-header">
        <b>Show Api Keys</b>
        </div><!-- /.box-header -->

        <!-- form start -->
        <form action="{{ url('api-manager', $data->id) }}" method="post">
            <div class="box-body">
                <div class="form-group row">
                    <div class="col-lg-6"><label for="api_keys">Api Keys</label></div>
                    <div class="col-lg-6"><label for="api_keys">{{ $data->api_key }}</label></div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6"><label for="client">Client</label></div>
                    <div class="col-lg-6"><label for="client">{{ $data->client }}</label></div>
                </div>
                <div class="form-group row">
                    <div class="col-lg-6"><label for="description">Description</label></div>
                    <div class="col-lg-6"><label for="description">{{ $data->description }}</label></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Content</th>
                            <th>Workflow</th>
                            <th>State From</th>
                            <th>State To</th>
                            <th>Action</th>
                        </tr>
                        <?php $i = 1; ?>
                        @foreach($history as $row)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{$row->get_api_keys->client}}</td>
                            <td>{{$row->get_workflow->label }}</a></td>
                            <td>{{$row->get_state_from->label}}</td>
                            <td>{{$row->get_state_to->label}}</td>
                            <td>
                              @if($workflowstateto == "Approved" || $workflowstateto == "Rejected")
                                Complete
                              @endif
                              @if($workflowstateto == "Request")
                                @if($row->get_state_from->label == "Propose" || $row->get_state_to->label == "Request")
                                  @foreach ($transition as $key)
                                    @if($key->from == "Request")
                                      @if($key->to == "Approved")
                                        <span class="btn btn-success" onclick="transisi({{$key->get_api_keys->client}}, {{$key->to}})">{{$key->label}}</span>
                                      @endif
                                      @if($key->to == "Rejected")
                                        <span class="btn btn-danger" onclick="transisi({{$key->get_api_keys->client}}, {{$key->to}})">{{$key->label}}</span>
                                      @endif
                                    @endif
                                  @endforeach
                                @endif
                              @endif
                              @if($row->get_state_from->label == "Propose" || $row->get_state_to->label == "Propose")
                                Complete
                              @elseif($row->get_state_from->label == "Request" || $row->get_state_to->label == "Approved")
                                Complete
                              @elseif($row->get_state_from->label == "Request" || $row->get_state_to->label == "Rejected")
                                Complete
                              @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer">
                <div class="pull-right">
                    <a href="{{ url('api-manager') }}" class="btn btn-danger">Cancel</a>
                </div>
            </div>
            {!! method_field('PUT') !!}
            {!! csrf_field() !!}
        </form>
    </div><!-- /.box -->

</div><!--/.col -->
</div>   <!-- /.row -->
</section>
</div>
</div>
<script type="text/javascript">
  function transisi(clients, requests) {
    var id = {{$id}};
    var a = confirm("Are You sure You want to "+requests+"?");
    if(a == true){
      $.ajax({
  			url : "/api-manager/transition",
  			data : {
            client: clients,
            request: requests,
        },
  			type : 'POST'
  		}).success(function(res) {
  			if (res.status == 100) {
  				window.location = "/api-manager/"+id;
  			} else {
  				alert('error '+requests+' data');
  			}
  		}).error(function(e) {
  			alert('error '+requests+' data');
  		});
    }else {
      return false;
    }
  }
</script>
