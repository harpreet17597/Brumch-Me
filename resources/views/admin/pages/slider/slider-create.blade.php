@extends('admin.layouts.master')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Create New Slider</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.sliders.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Slider Title :</label>
                        <input type="text" class="form-control" name="title" placeholder="Enter title" value="{{old('title') ?? ''}}">
                        @error('title')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Slider Description :</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Enter description">{{old('description') ?? ''}}</textarea>
                        @error('description')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="image">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image" accept="image/*" />
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                            <div class="input-group-append">
                               <span class="input-group-text">Upload</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-md-4">
                    <img src="" alt="" id="preview" width="200">
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
       Create New Slider
    </div>
    <!-- /.card-footer-->
</div>
@endSection

@section('js')
<script>
    $('input[type="file"]').change(function(e) 
    {
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);
        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
            document.getElementById("preview").src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);
    });
</script>
@endsection