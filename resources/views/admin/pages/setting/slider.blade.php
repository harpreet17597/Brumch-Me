@extends('admin.layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.css" integrity="sha512-7uSoC3grlnRktCWoO4LjHMjotq8gf9XDFQerPuaph+cqR7JC9XKGdvN+UwZMC14aAaBDItdRj3DcSDs4kMWUgg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Slider</h3>
    </div>
    <div class="card-body ">
        <form action="{{route('admin.settings.slider')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Slider Title:</label>
                        <input type="name" class="form-control" name="slider_title" placeholder="Title" value="{{old('slider_title') ?? $setting->slider_title}}">
                        @error('slider_title')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label for="name">Slider Description:</label>
                        <input type="name" class="form-control" name="slider_description" placeholder="Enter new password" value="{{old('slider_description') ?? $setting->slider_description}}">
                        @error('slider_description')<p>{{$message}}</p>@enderror
                    </div>
                    <div class="form-group">
                       <div id="sliderImageUpload" class="dropzone"></div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
    Slider
    </div>
    <!-- /.card-footer-->
</div>
@endSection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.js" integrity="sha512-9e9rr82F9BPzG81+6UrwWLFj8ZLf59jnuIA/tIf8dEGoQVu7l5qvr02G/BiAabsFOYrIUTMslVN+iDYuszftVQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js" integrity="sha512-rstIgDs0xPgmG6RX1Aba4KV5cWJbAMcvRCVmglpam9SoHZiUCyQVDdH2LPlxoHtrv17XWblE/V/PP+Tr04hbtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{asset('dist/js/custom.js')}}"></script>
<script>  

let images = [];


Dropzone.autoDiscover = false;

    let $btn = $('button.btn');
    let $form = $('form');

    myDropzone = new Dropzone('div#sliderImageUpload', {
    addRemoveLinks: true,
    autoProcessQueue: false,
    uploadMultiple: true,
    parallelUploads: 100,
    maxFiles: 3,
    paramName: 'files',
    clickable: true,
    url: "{{route('admin.settings.slider')}}",
    removedfile: function(file) {

        var fileName = file.name;
        console.log(fileName);
        console.log(file)
        var _ref;
          return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
    },
    init: async function () {

        var myDropzone = this;
        // Update selector to match your button
        $btn.click(function (e) {
            e.preventDefault();
            if ( $form.valid() ) {
                myDropzone.processQueue();
            }
            return false;
        });
        
        try {

            let respons = await dynamicAjax("{{route('admin.slider-images')}}","GET");
            
            $.each(respons,function(i,val) {

        
                let file = val.image;
                let ext      = file.substr(file.lastIndexOf('.') + 1);
                let filePath = "{{url('')}}/"+file;
                let fileName = file.substr(file.lastIndexOf('.') + 1);

                var mockFile = { name: fileName, size: 12, type: 'image/jpeg','id':val.id };
                myDropzone.options.addedfile.call(myDropzone, mockFile);
                myDropzone.options.thumbnail.call(myDropzone, mockFile, filePath);
                mockFile.previewElement.classList.add('dz-success');
                mockFile.previewElement.classList.add('dz-complete');
                mockFile._removeLink.text = 'delete';
                mockFile._removeLink.dataset.id = val.id;
                
            })
        }
        catch(error) {

        }

        this.on('sending', function (file, xhr, formData) {
            // Append all form inputs to the formData Dropzone will POST
            var data = $form.serializeArray();
            $.each(data, function (key, el) {
                formData.append(el.name, el.value);
            });
            console.log(formData);

        });
    },
    error: function (file, response){
        if ($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
        else
            var message = response.message;
        file.previewElement.classList.add("dz-error");
        _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            node = _ref[_i];
            _results.push(node.textContent = message);
        }
        return _results;
    },
    successmultiple: function (file, response) {
        console.log(file, response);
        // $modal.modal("show");
    },
    completemultiple: function (file, response) {
        console.log(file, response, "completemultiple");
        //$modal.modal("show");
    },
    reset: function () {
        console.log("resetFiles");
        this.removeAllFiles(true);
    }
});
</script>
@endsection