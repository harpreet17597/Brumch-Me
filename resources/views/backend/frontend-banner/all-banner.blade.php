@extends('backend.admin-master')
@section('site-title')
    {{__('Banner Details')}}
@endsection

@section('content')
<div class="col-lg-12 col-ml-12 padding-bottom-30">
    <div class="row mt-5">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                        <label><strong>{{ __('Banners') }} </strong></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-ml-12 padding-bottom-30">
    <div class="row mt-5">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                        <form action="{{route('admin.frontend.banner.upload-banner-image')}}" method="post" enctype="multipart/form-data" class="form_upload_banner">
                           <input name="file" type="file" accept="image/*" data-file-id="1" required/>
                           <input name="id" type="hidden" value="1" />
                           <input  type="submit" class="btn btn-xs btn-success"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                    <form action="{{route('admin.frontend.banner.upload-banner-image')}}" method="post" enctype="multipart/form-data" class="form_upload_banner">
                        <input name="file" type="file" accept="image/*" data-file-id="2" required/>
                        <input name="id" type="hidden" value="2" />
                        <input  type="submit" class="btn btn-xs btn-success"/>
                    </form>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines">
                    <form action="{{route('admin.frontend.banner.upload-banner-image')}}" method="post" enctype="multipart/form-data" class="form_upload_banner">    
                        <input name="file" type="file" accept="image/*" data-file-id="3" required/>
                        <input name="id" type="hidden" value="3" />
                        <input  type="submit" class="btn btn-xs btn-success"/>
                    </form>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-12 col-ml-12 padding-bottom-30">
    <div class="row mt-5">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines text-center">
                       <img src="{{$banners->where('id',1)->first()->banner_image ?? ''}}" id="preview-1" style="width:300px;height:300px;"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines text-center">
                       <img src="{{$banners->where('id',2)->first()->banner_image ?? ''}}" id="preview-2" style="width:300px;height:300px;"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="checkbox-inlines text-center">
                       <img src="{{$banners->where('id',3)->first()->banner_image ?? ''}}" id="preview-3" style="width:300px;height:300px;"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('dist/js/common/ajax.js')}}"></script>
<script>
    $('input[type="file"]').change(function(e) {
        var id = $(this).data('file-id');
        var fileName = e.target.files[0].name;
        $("#file").val(fileName);
        var reader = new FileReader();
        reader.onload = function(e) {
            // get loaded data and render thumbnail.
             document.getElementById(`preview-${id}`).src = e.target.result;
        };
        // read the image file as a data URL.
        reader.readAsDataURL(this.files[0]);   
   });

   $(document).ready(function() {
     
       /*submit form*/
       $('form.form_upload_banner').on('submit',function(event) {
            event.preventDefault(); // Prevent the default form submission
            // Create a new FormData object
            var formData = new FormData(this);
            console.log(formData);
            // Perform an AJAX request using jQuery's $.ajax
            $.ajax({
                url: $(this).attr('action'), // Replace with your API endpoint URL
                type: 'POST',
                dataType: "json",
                data: formData,
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $(":submit").attr("disabled", true);
                    $('.cs_loader').show();
                },
                complete: function() {
                    $(":submit").removeAttr("disabled");
                    $('.cs_loader').hide();
                },
                processData: false, // Prevent jQuery from processing the data
                contentType: false, // Prevent jQuery from setting the content type
                success: function(response) {
                    Swal.fire({
                        title: response.message,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                    });
                },
                error: function(xhr, status, error) {
                   let responseJSON = xhr.responseJSON;
                   Swal.fire({
                        title: responseJSON.message,
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                    });
                }
            });
       });

   });

</script>
@endsection