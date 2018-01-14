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
        <b>Request Api Keys</b>
        </div><!-- /.box-header -->

        <!-- form start -->
        <form action="{{ url('host-keys/request') }}" method="post">
            <div class="box-body">
                <div class="form-group{{ $errors->has('host') ? ' has-error' : '' }}">
                    <label for="name"> Host</label>
                    <input type="text" name="host" id="host" class="form-control" value="{{ old('host') }}" required>
                    <input type="hidden" name="client" id="client" class="form-control" value="{{ url('/') }}">
                    @if ($errors->has('host'))
                        <span class="help-block">{{ $errors->first('host') }}</span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                    <label for="name"> Description</label>
                    <textarea name="description" class="form-control">{{old('description')}}</textarea>
                    @if ($errors->has('description'))
                        <span class="help-block">{{ $errors->first('description') }}</span>
                    @endif
                </div>
            </div><!-- /.box-body -->

            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary">Request</button>
                    <a href="{{ url('host-keys') }}" class="btn btn-danger">Cancel</a>
                </div>
            </div>
            {!! method_field('POST') !!}
            {!! csrf_field() !!}
        </form>
    </div><!-- /.box -->

</div><!--/.col -->
</div>
</section>
</div>
</div>   <!-- /.row -->
