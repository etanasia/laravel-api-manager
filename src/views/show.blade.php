<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
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
            <div class="box-body">
                <div class="form-group">
                    <div class="col-lg-3"><label for="api_keys">Api Keys</label></div>
                    <div class="col-lg-9"><label for="api_keys">{{ $data->api_key }}</label></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3"><label for="client">Client</label></div>
                    <div class="col-lg-9"><label for="client">{{ $data->client }}</label></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3"><label for="description">Description</label></div>
                    <div class="col-lg-9"><label for="description">{{ $data->description }}</label></div>
                </div>
                <div class="form-group">
                    <div class="col-lg-3"><label for="description">Status</label></div>
                    <div class="col-lg-9"><label for="description">{{ $data->getHistory->getStateTo->label }}</label></div>
                </div>
                <div class="form-group">
                  <div class="col-lg-3"><label for="transition">Transition</label></div>
                  <div class="col-lg-9">
                  @foreach($transition as $keys)
                    @if($data->getHistory->getStateTo->label == "Request")
                      @if($histories->getStateFrom->label == "Propose" && $histories->getStateTo->label == "Request")
                        @if($keys->from == "Request" || $keys->from == "request")
                          @if($keys->to == "Approved" || $keys->to == "approved")
                            <div class="col-lg-4 row">
                              <span class="btn btn-success" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                          @if($keys->to == "Rejected" || $keys->to == "rejected")
                            <div class="col-lg-4 row">
                              <span class="btn btn-danger" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif                          
                          @if($keys->to == "Needs Completed Document" || $keys->to == "needs completed document")
                            <div class="col-lg-4 row">
                              <span class="btn btn-info" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                        @endif
                      @endif
                    @endif

                    @if($data->getHistory->getStateTo->label == "Needs Completed Document")
                      @if($histories->getStateFrom->label == "Request" && $histories->getStateTo->label == "Needs Completed Document")
                        @if($keys->from == "Needs Completed Document" || $keys->from == "needs completed document")
                          @if($keys->to == "Document Submitted" || $keys->to == "document submitted")
                            <div class="col-lg-4 row">
                              <span class="btn btn-primary" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                        @endif
                      @endif
                    @endif

                    @if($data->getHistory->getStateTo->label == "Document Submitted")
                      @if($histories->getStateFrom->label == "Needs Completed Document" && $histories->getStateTo->label == "Document Submitted")
                        @if($keys->from == "Document Submitted" || $keys->from == "document submitted")
                          @if($keys->to == "Approved" || $keys->to == "approved")
                            <div class="col-lg-4 row">
                              <span class="btn btn-success" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                          @if($keys->to == "Rejected" || $keys->to == "rejected")
                            <div class="col-lg-4 row">
                              <span class="btn btn-danger" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                        @endif
                      @endif
                    @endif

                    @if($data->getHistory->getStateTo->label == "Approved")
                      @if($histories->getStateFrom->label == "Request" && $histories->getStateTo->label == "Approved" || $histories->getStateFrom->label == "Document Submitted" && $histories->getStateTo->label == "Approved" || $histories->getStateFrom->label == "Rejected" && $histories->getStateTo->label == "Approved")
                        @if($keys->from == "Approved" || $keys->from == "approved")
                          @if($keys->to == "Rejected" || $keys->to == "rejected")
                            <div class="col-lg-4 row">
                              <span class="btn btn-danger" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                        @endif
                      @endif
                    @endif

                    @if($data->getHistory->getStateTo->label == "Rejected")
                      @if($histories->getStateFrom->label == "Request" && $histories->getStateTo->label == "Rejected" || $histories->getStateFrom->label == "Document Submitted" && $histories->getStateTo->label == "Rejected" || $histories->getStateFrom->label == "Approved" && $histories->getStateTo->label == "Rejected")
                        @if($keys->from == "Rejected" || $keys->from == "rejected")
                          @if($keys->to == "Approved" || $keys->to == "approved")
                            <div class="col-lg-4 row">
                              <span class="btn btn-success" onclick="transisi('{{$histories->getApiKeys->client}}', '{{$keys->to}}')">{{$keys->label}}</span>
                            </div>
                          @endif
                        @endif
                      @endif
                    @endif

                  @endforeach
                  </div>
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
                        <?php $i = 1 + $history->currentPage() * $history->perPage() - $history->perPage(); ?>
                        @foreach($history as $row)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{$row->getApiKeys->client}}</td>
                            <td>{{$row->getWorkflow->label }}</a></td>
                            <td>{{$row->getStateFrom->label}}</td>
                            <td>{{$row->getStateTo->label}}</td>
                            <td>
                              @if($row->getStateFrom->label == "Propose" && $row->getStateTo->label == "Propose")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Propose" && $row->getStateTo->label == "Request")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Request" && $row->getStateTo->label == "Approved")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Request" && $row->getStateTo->label == "Rejected")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Request" && $row->getStateTo->label == "Needs Completed Document")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Needs Completed Document" && $row->getStateTo->label == "Document Submitted")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Document Submitted" && $row->getStateTo->label == "Approved")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Document Submitted" && $row->getStateTo->label == "Rejected")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Approved" && $row->getStateTo->label == "Rejected")
                                {{$row->getStateTo->label}} Complete
                              @elseif($row->getStateFrom->label == "Rejected" && $row->getStateTo->label == "Approved")
                                {{$row->getStateTo->label}} Complete
                              @endif
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer">
                {!! $history->render() !!}
                <div class="pull-right">
                    <a href="{{ url('api-manager') }}" class="btn btn-danger">Cancel</a>
                </div>
            </div>
            {{-- {!! method_field('PUT') !!}
            {!! csrf_field() !!} --}}
    </div><!-- /.box -->

</div><!--/.col -->
</div>   <!-- /.row -->
</section>
</div>
</div>
<script type="text/javascript">
  function transisi(clients, requests) {
    var base_url = "{{ url('/') }}";
    var a = confirm("Are You sure You want to "+requests+"?");
    if(a == true){
      $.ajax({
        headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
  			url : base_url+"/api-manager/transition",
  			data : {
            client: clients,
            host: base_url,
            request: requests,
        },
  			type : 'POST',
        success : function(){
            window.location ='{{ url()->current() }}'
        }
  		});
    }else {
      return false;
    }
  }
</script>
